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
      'include_terms' => ['villa', 'nha-pho', 'villa', ''],
      'page_size' => 99,
      'initial_category' => 'not-allowed',
      'orderby' => 'invalid',
      'order' => 'asc',
      'image_size' => 'large',
    ]);

    self::assertSame('project', $config['post_type']);
    self::assertSame('project-category', $config['taxonomy']);
    self::assertSame(['villa', 'nha-pho'], $config['include_terms']);
    self::assertSame(24, $config['page_size']);
    self::assertSame('', $config['initial_category']);
    self::assertSame('date', $config['orderby']);
    self::assertSame('ASC', $config['order']);
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

  public function testMapsPostAndRejectsMissingImage(): void
  {
    $post = (object) ['ID' => 7, 'post_title' => 'Biệt thự mẫu', 'post_excerpt' => 'Mô tả dự án'];
    $item = eai_rc_map_project_category_gallery_post($post, [
      'taxonomy' => 'project-category', 'image_size' => 'large',
    ], 'villa');

    self::assertSame('7', $item['id']);
    self::assertSame('Biệt thự mẫu', $item['title']);
    self::assertSame('Mô tả dự án', $item['description']);
    self::assertSame('villa', $item['category']);
    self::assertSame('https://example.com/project-7', $item['link']['url']);
    self::assertSame('https://example.com/logo-large.png', $item['image']['url']);
    self::assertArrayNotHasKey('link', $item['image']);

    self::assertNull(eai_rc_map_project_category_gallery_post(
      (object) ['ID' => 8, 'post_title' => 'Không ảnh', 'post_excerpt' => ''],
      ['taxonomy' => 'project-category', 'image_size' => 'large'],
      'villa'
    ));
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
    self::assertSame(502, $sample['items'][0]['image']['display_dimensions']['width']);
  }
}
