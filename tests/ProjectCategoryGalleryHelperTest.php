<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class ProjectCategoryGalleryHelperTest extends TestCase
{
  public function testNormalizesSettingsAndClampsPageSize(): void
  {
    $config = eai_project_category_gallery_config_from_settings([
      'post_type' => 'project',
      'taxonomy' => 'project-category',
      'investor_taxonomy' => ' Project-Investor<script> ',
      'model_taxonomy' => ' Project-Model ',
      'include_terms' => ['villa', 'nha-pho', 'villa', ''],
      'page_size' => 99,
      'initial_category' => 'not-allowed',
      'orderby' => 'invalid',
      'order' => 'asc',
      'image_size' => 'large',
    ]);

    self::assertSame('project', $config['post_type']);
    self::assertSame('project-category', $config['taxonomy']);
    self::assertSame('project-investorscript', $config['investor_taxonomy']);
    self::assertSame('project-model', $config['model_taxonomy']);
    self::assertSame(['villa', 'nha-pho'], $config['include_terms']);
    self::assertSame(24, $config['page_size']);
    self::assertSame('', $config['initial_category']);
    self::assertSame('date', $config['orderby']);
    self::assertSame('ASC', $config['order']);
  }

  public function testResolveCategoryKeepsCategoryWhenIncludeTermsEmpty(): void
  {
    self::assertSame('nha-pho', eai_project_category_gallery_resolve_category('nha-pho', []));
    self::assertSame('nha-pho', eai_project_category_gallery_resolve_category(' nha-pho ', []));
    self::assertSame('', eai_project_category_gallery_resolve_category('', []));
  }

  public function testResolveCategoryRespectsIncludeTerms(): void
  {
    $include_terms = ['villa', 'nha-pho'];

    self::assertSame('nha-pho', eai_project_category_gallery_resolve_category('nha-pho', $include_terms));
    self::assertSame('villa', eai_project_category_gallery_resolve_category('villa', $include_terms));
    self::assertSame('', eai_project_category_gallery_resolve_category('van-phong', $include_terms));
    self::assertSame('', eai_project_category_gallery_resolve_category('', $include_terms));
  }

  public function testBuildsFiltersAndQueryArgs(): void
  {
    $config = [
      'post_type' => 'project', 'taxonomy' => 'project-category',
      'include_terms' => ['villa', 'nha-pho'], 'page_size' => 6,
      'orderby' => 'modified', 'order' => 'DESC', 'image_size' => 'large',
    ];

    self::assertSame([
      ['label' => 'Tất cả', 'value' => ''],
      ['label' => 'Villa', 'value' => 'villa'],
      ['label' => 'Nhà phố', 'value' => 'nha-pho'],
    ], eai_project_category_gallery_build_filters($config, [
      (object) ['slug' => 'villa', 'name' => 'Villa'],
      (object) ['slug' => 'nha-pho', 'name' => 'Nhà phố'],
    ]));

    $args = eai_project_category_gallery_build_query_args($config, 'villa', 2, 6);
    self::assertSame(6, $args['offset']);
    self::assertSame(7, $args['posts_per_page']);
    self::assertSame(['villa'], $args['tax_query'][0]['terms']);
  }

  public function testMapsPostMetadataFromAssignedTaxonomyTermsAndIgnoresExcerpt(): void
  {
    $post = (object) ['ID' => 7, 'post_title' => 'Biệt thự mẫu', 'post_excerpt' => 'Excerpt không được dùng'];
    $item = eai_rc_map_project_category_gallery_post($post, [
      'taxonomy' => 'project-category',
      'investor_taxonomy' => 'project-investor',
      'model_taxonomy' => 'project-model',
      'image_size' => 'large',
    ], 'villa');

    self::assertSame('7', $item['id']);
    self::assertSame('Biệt thự mẫu', $item['title']);
    self::assertSame("Chủ đầu tư: ICHouse, Nhà đầu tư mẹ\nMô hình: Nghỉ dưỡng, Nhà ở", $item['description']);
    self::assertStringNotContainsString('Excerpt không được dùng', $item['description']);
    self::assertSame('villa', $item['category']);
    self::assertSame('https://example.com/project-7', $item['link']['url']);
    self::assertSame('https://example.com/logo-large.png', $item['image']['url']);
    self::assertArrayNotHasKey('link', $item['image']);
  }

  public function testSkipsMissingOrInvalidMetadataTaxonomies(): void
  {
    $post = (object) ['ID' => 7, 'post_title' => 'Biệt thự mẫu', 'post_excerpt' => 'Excerpt cũ'];

    $onlyInvestor = eai_rc_map_project_category_gallery_post($post, [
      'taxonomy' => 'project-category',
      'investor_taxonomy' => 'project-investor',
      'model_taxonomy' => 'wrong-post-taxonomy',
      'image_size' => 'large',
    ], 'villa');
    $withoutMetadata = eai_rc_map_project_category_gallery_post($post, [
      'taxonomy' => 'project-category',
      'investor_taxonomy' => '',
      'model_taxonomy' => '',
      'image_size' => 'large',
    ], 'villa');

    self::assertSame('Chủ đầu tư: ICHouse, Nhà đầu tư mẹ', $onlyInvestor['description']);
    self::assertSame('', $withoutMetadata['description']);
  }

  public function testSkipsPrivateMetadataTaxonomy(): void
  {
    $item = eai_rc_map_project_category_gallery_post(
      (object) ['ID' => 7, 'post_title' => 'Biệt thự mẫu', 'post_excerpt' => 'Excerpt cũ'],
      [
        'taxonomy' => 'project-category',
        'investor_taxonomy' => 'private-project-taxonomy',
        'model_taxonomy' => '',
        'image_size' => 'large',
      ],
      'villa'
    );

    self::assertSame('', $item['description']);
  }

  public function testExcludesConfiguredCategoryTermsFromMetadata(): void
  {
    $item = eai_rc_map_project_category_gallery_post(
      (object) ['ID' => 7, 'post_title' => 'Biệt thự mẫu', 'post_excerpt' => 'Excerpt cũ'],
      [
        'taxonomy' => 'project-category',
        'investor_taxonomy' => 'category',
        'model_taxonomy' => '',
        'image_size' => 'large',
      ],
      'villa'
    );

    self::assertSame('', $item['description']);
  }

  public function testUsesPlaceholderForMissingImage(): void
  {
    $itemWithoutImage = eai_rc_map_project_category_gallery_post(
      (object) ['ID' => 8, 'post_title' => 'Không ảnh', 'post_excerpt' => 'Excerpt cũ'],
      [
        'taxonomy' => 'project-category',
        'investor_taxonomy' => 'project-investor',
        'model_taxonomy' => 'project-model',
        'image_size' => 'large',
      ],
      'villa'
    );

    self::assertNotNull($itemWithoutImage);
    self::assertSame('', $itemWithoutImage['description']);
    self::assertSame('https://placehold.co/600x400?text=anh-dai-dien', $itemWithoutImage['image']['url']);
    self::assertSame('Không ảnh', $itemWithoutImage['image']['alt']);
    self::assertSame(['width' => 600, 'height' => 400], $itemWithoutImage['image']['display_dimensions']);
  }

  public function testFilterEndpointCarriesMetadataTaxonomies(): void
  {
    $endpoint = eai_project_category_gallery_filter_endpoint([
      'post_type' => 'project',
      'taxonomy' => 'project-category',
      'investor_taxonomy' => 'project-investor',
      'model_taxonomy' => 'project-model',
      'include_terms' => ['villa'],
      'orderby' => 'date',
      'order' => 'DESC',
      'image_size' => 'large',
    ]);

    parse_str((string) parse_url($endpoint, PHP_URL_QUERY), $query);
    self::assertSame('project-investor', $query['investor_taxonomy']);
    self::assertSame('project-model', $query['model_taxonomy']);
  }

  public function testRestConfigKeepsMetadataTaxonomies(): void
  {
    require_once dirname(__DIR__) . '/includes/project-category-gallery-api.php';
    $config = eai_project_category_gallery_config_from_request(new WP_REST_Request([
      'post_type' => 'project',
      'taxonomy' => 'project-category',
      'investor_taxonomy' => ' Project-Investor ',
      'model_taxonomy' => ' Project-Model ',
      'include_terms' => ['villa'],
      'orderby' => 'date',
      'order' => 'DESC',
      'image_size' => 'large',
    ]));

    self::assertSame('project-investor', $config['investor_taxonomy']);
    self::assertSame('project-model', $config['model_taxonomy']);
  }

  public function testWidgetRegistersMetadataTaxonomyControls(): void
  {
    $source = file_get_contents(dirname(__DIR__) . '/includes/widgets/EAI-project-category-gallery.php');

    self::assertIsString($source);
    self::assertStringContainsString("add_control('investor_taxonomy'", $source);
    self::assertStringContainsString("add_control('model_taxonomy'", $source);
  }

  public function testBuildsPropsAndEditorSample(): void
  {
    $sample = eai_project_category_gallery_get_editor_sample_props([
      'class_name' => ' gallery-custom ',
      'page_size' => 6,
      'load_more_label' => 'Xem thêm',
    ], 'abc 123');

    self::assertSame('gallery-custom', $sample['className']);
    self::assertSame('project-category-gallery-abc123', $sample['scrollReveal']['targetId']);
    self::assertSame('/wp-json/eai/v1/project-category-gallery', $sample['filterEndpoint']);
    self::assertCount(5, $sample['filters']);
    self::assertCount(6, $sample['items']);
    self::assertTrue($sample['hasMore']);
    self::assertSame("Chủ đầu tư: ICHouse\nMô hình: Nhà ở", $sample['items'][0]['description']);
    self::assertSame(502, $sample['items'][0]['image']['display_dimensions']['width']);
  }
}
