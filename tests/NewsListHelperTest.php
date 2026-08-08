<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class NewsListHelperTest extends TestCase
{
  public function testNormalizesSettingsAndBuildsModifiedPagedQuery(): void
  {
    $config = eai_news_list_config_from_settings([
      'post_type' => 'post<script>',
      'page_size' => 99,
      'image_size' => '',
      'page_query_param' => ' news page ',
      'class_name' => ' news-list-custom ',
    ]);

    self::assertSame('postscript', $config['post_type']);
    self::assertSame(24, $config['page_size']);
    self::assertSame('large', $config['image_size']);
    self::assertSame('newspage', $config['page_query_param']);
    self::assertSame('news-list-custom', $config['class_name']);

    $args = eai_news_list_build_query_args($config, 2, 5);
    self::assertSame('postscript', $args['post_type']);
    self::assertSame('publish', $args['post_status']);
    self::assertSame(5, $args['posts_per_page']);
    self::assertSame(2, $args['paged']);
    self::assertSame('modified', $args['orderby']);
    self::assertSame('DESC', $args['order']);
    self::assertSame('_thumbnail_id', $args['meta_query'][0]['key']);
  }

  public function testMapsNewsItemWithFeaturedBackgroundFallback(): void
  {
    $post = (object) [
      'ID' => 7,
      'post_title' => 'Tin tức công trình',
      'post_excerpt' => 'Mô tả tin tức.',
      'post_content' => 'Nội dung dài.',
      'post_status' => 'publish',
    ];

    $item = eai_rc_map_news_list_post($post, ['image_size' => 'large']);

    self::assertSame('7', $item['id']);
    self::assertSame('Tin tức công trình', $item['title']);
    self::assertSame('Mô tả tin tức.', $item['description']);
    self::assertSame('22/07/2026', $item['time']);
    self::assertSame('https://example.com/logo-large.png', $item['image']['url']);
    self::assertSame('', $item['backgroundImage']['alt']);
    self::assertSame($item['image']['url'], $item['backgroundImage']['url']);
    self::assertSame('https://example.com/project-7', $item['link']['url']);

    self::assertNull(eai_rc_map_news_list_post(
      (object) [
        'ID' => 8,
        'post_title' => 'Thiếu ảnh',
        'post_excerpt' => '',
        'post_content' => '',
        'post_status' => 'publish',
      ],
      ['image_size' => 'large']
    ));
  }

  public function testBuildsEditorSamplePropsWithCanonicalShape(): void
  {
    $sample = eai_news_list_get_editor_sample_props([
      'page_size' => 5,
      'image_size' => 'large',
      'page_query_param' => 'news_page',
      'class_name' => ' news-list-custom ',
    ]);

    self::assertSame('news-list-custom', $sample['className']);
    self::assertSame(5, $sample['pageSize']);
    self::assertSame(1, $sample['initialPage']);
    self::assertSame(3, $sample['totalPages']);
    self::assertSame('news_page', $sample['pageQueryParam']);
    self::assertCount(5, $sample['items']);
    self::assertArrayHasKey('backgroundImage', $sample['items'][0]);
    self::assertArrayHasKey('description', $sample['items'][0]);
    self::assertSame('Một buổi lễ khởi công, nhưng là cả hành trình chuẩn bị chỉn chu!', $sample['items'][0]['title']);
  }

  public function testNormalizesTaxonomyFilterSettings(): void
  {
    $config = eai_news_list_config_from_settings([
      'post_type' => 'job',
      'taxonomy' => ' Job-Type ',
      'taxonomy_terms_job-type' => [' Full Time ', 'full-time', '', 'Remote Work'],
    ]);

    self::assertSame('job-type', $config['taxonomy']);
    self::assertSame(['full-time', 'remote-work'], $config['include_terms']);
  }

  public function testDefaultsToNoTaxonomyFilter(): void
  {
    $config = eai_news_list_config_from_settings([]);

    self::assertSame('', $config['taxonomy']);
    self::assertSame([], $config['include_terms']);
  }

  public function testBuildsTaxQueryInForSelectedTerms(): void
  {
    $args = eai_news_list_build_query_args([
      'post_type' => 'job',
      'taxonomy' => 'job_type',
      'include_terms' => ['full-time', 'remote'],
    ], 1, 5);

    self::assertSame([[
      'taxonomy' => 'job_type',
      'field' => 'slug',
      'terms' => ['full-time', 'remote'],
      'operator' => 'IN',
    ]], $args['tax_query']);
  }

  public function testBuildsTaxQueryExistsForEmptyTerms(): void
  {
    $args = eai_news_list_build_query_args([
      'post_type' => 'job',
      'taxonomy' => 'job_type',
      'include_terms' => [],
    ], 1, 5);

    self::assertSame('EXISTS', $args['tax_query'][0]['operator']);
    self::assertSame('job_type', $args['tax_query'][0]['taxonomy']);
    self::assertSame([], $args['tax_query'][0]['terms']);
  }

  public function testOmitsTaxQueryForInvalidOrEmptyFilter(): void
  {
    $none = eai_news_list_build_query_args([
      'post_type' => 'post',
    ], 1, 5);
    $private = eai_news_list_build_query_args([
      'post_type' => 'job',
      'taxonomy' => 'private_job_type',
      'include_terms' => ['full-time'],
    ], 1, 5);
    $invalid_terms = eai_news_list_build_query_args([
      'post_type' => 'job',
      'taxonomy' => 'job_type',
      'include_terms' => ['not-a-real-term'],
    ], 1, 5);

    self::assertArrayNotHasKey('tax_query', $none);
    self::assertArrayNotHasKey('tax_query', $private);
    self::assertArrayNotHasKey('tax_query', $invalid_terms);
  }

  public function testEndpointIncludesTaxonomyFilter(): void
  {
    $endpoint = eai_news_list_endpoint([
      'post_type' => 'job',
      'image_size' => 'large',
      'featured_background_image' => [],
      'taxonomy' => 'job_type',
      'include_terms' => ['full-time'],
    ]);

    self::assertStringContainsString('taxonomy=job_type', $endpoint);
    self::assertStringContainsString('include_terms%5B0%5D=full-time', $endpoint);
  }
}
