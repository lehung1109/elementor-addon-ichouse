<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class JobListingListHelperTest extends TestCase
{
  public function testNormalizesGenericQuerySettings(): void
  {
    $config = eai_job_listing_list_config_from_settings([
      'post_type' => 'job<script>',
      'page_size' => 99,
      'orderby' => 'invalid',
      'order' => 'asc',
      'image_size' => '',
      'page_query_param' => ' jobs page ',
      'class_name' => ' jobs-custom ',
      'taxonomy' => ' Job-Type<script> ',
      'include_terms' => [' Full Time ', 'full-time', '', 'Remote Work'],
      'employment_type_taxonomy' => ' Job_Type ',
      'location_field' => ' field_location ',
      'expiration_date_field' => ' field_expiration ',
    ]);

    self::assertSame('jobscript', $config['post_type']);
    self::assertSame(24, $config['page_size']);
    self::assertSame('date', $config['orderby']);
    self::assertSame('ASC', $config['order']);
    self::assertSame('large', $config['image_size']);
    self::assertSame('jobspage', $config['page_query_param']);
    self::assertSame('jobs-custom', $config['class_name']);
    self::assertSame('job-typescript', $config['taxonomy']);
    self::assertSame(['full-time', 'remote-work'], $config['include_terms']);
    self::assertSame('job_type', $config['employment_type_taxonomy']);
    self::assertSame('field_location', $config['location_field']);
    self::assertSame('field_expiration', $config['expiration_date_field']);
  }

  public function testDefaultsToNoTaxonomyFilter(): void
  {
    $config = eai_job_listing_list_config_from_settings([]);

    self::assertSame('', $config['taxonomy']);
    self::assertSame([], $config['include_terms']);
  }

  public function testBuildsPagedQueryForPublishedPostsWithThumbnails(): void
  {
    $args = eai_job_listing_list_build_query_args([
      'post_type' => 'job',
      'orderby' => 'modified',
      'order' => 'DESC',
    ], 2, 6);

    self::assertSame('job', $args['post_type']);
    self::assertSame('publish', $args['post_status']);
    self::assertSame(6, $args['posts_per_page']);
    self::assertSame(2, $args['paged']);
    self::assertFalse($args['no_found_rows']);
    self::assertSame('_thumbnail_id', $args['meta_query'][0]['key']);
    self::assertSame('EXISTS', $args['meta_query'][0]['compare']);
  }

  public function testBuildsTaxQueryForValidSelectedTerms(): void
  {
    $args = eai_job_listing_list_build_query_args([
      'post_type' => 'job',
      'orderby' => 'date',
      'order' => 'DESC',
      'taxonomy' => 'job_type',
      'include_terms' => ['full-time', 'remote'],
    ], 1, 3);

    self::assertSame([[
      'taxonomy' => 'job_type',
      'field' => 'slug',
      'terms' => ['full-time', 'remote'],
      'operator' => 'IN',
    ]], $args['tax_query']);
  }

  public function testOmitsTaxQueryForInvalidOrEmptyFilter(): void
  {
    $invalid = eai_job_listing_list_build_query_args([
      'post_type' => 'post',
      'taxonomy' => 'job_type',
      'include_terms' => ['full-time'],
    ], 1, 3);
    $empty = eai_job_listing_list_build_query_args([
      'post_type' => 'job',
      'taxonomy' => 'job_type',
      'include_terms' => [],
    ], 1, 3);
    $private = eai_job_listing_list_build_query_args([
      'post_type' => 'job',
      'taxonomy' => 'private_job_type',
      'include_terms' => ['full-time'],
    ], 1, 3);

    self::assertArrayNotHasKey('tax_query', $invalid);
    self::assertArrayNotHasKey('tax_query', $empty);
    self::assertArrayNotHasKey('tax_query', $private);
  }

  public function testMapsGenericPostAndRejectsMissingImage(): void
  {
    $post = (object) [
      'ID' => 7,
      'post_title' => 'Kiến trúc sư',
      'post_excerpt' => 'Thiết kế công trình.',
      'post_content' => 'Nội dung dài.',
      'post_status' => 'publish',
      'post_type' => 'job',
    ];

    $item = eai_rc_map_job_listing_list_post($post, [
      'post_type' => 'job',
      'image_size' => 'large',
      'employment_type_taxonomy' => 'job_type',
      'location_field' => 'field_location',
      'expiration_date_field' => 'field_expiration',
    ]);

    self::assertSame('7', $item['id']);
    self::assertSame('Kiến trúc sư', $item['title']);
    self::assertSame('Jobs', $item['categoryLabel']);
    self::assertSame('Thiết kế công trình.', $item['description']);
    self::assertSame('https://example.com/project-7', $item['link']['url']);
    self::assertSame('https://example.com/logo-large.png', $item['image']['url']);
    self::assertSame('Hết hạn', $item['statusLabel']);
    self::assertSame('Toàn thời gian, Làm từ xa', $item['employmentType']);
    self::assertSame('Hà Nội', $item['location']);

    self::assertNull(eai_rc_map_job_listing_list_post(
      (object) [
        'ID' => 8,
        'post_title' => 'Không ảnh',
        'post_excerpt' => '',
        'post_content' => '',
        'post_status' => 'publish',
        'post_type' => 'job',
      ],
      ['post_type' => 'job', 'image_size' => 'large']
    ));
  }

  public function testMapsSupportedLocationFieldsAndRejectsInvalidValues(): void
  {
    self::assertSame('Hà Nội', eai_job_listing_list_location(7, 'field_location'));
    self::assertSame('Đà Nẵng', eai_job_listing_list_location(7, 'field_location_text'));
    self::assertSame('Văn phòng Hồ Chí Minh', eai_job_listing_list_location(7, 'field_location_textarea'));
    self::assertSame('Làm việc từ xa', eai_job_listing_list_location(7, 'field_location_radio'));
    self::assertSame('', eai_job_listing_list_location(7, 'field_location_empty'));
    self::assertSame('', eai_job_listing_list_location(7, 'field_wrong_type'));
    self::assertSame('', eai_job_listing_list_location(7, 'field_missing'));
  }

  public function testExpirationStatusKeepsJobsActiveThroughExpirationDate(): void
  {
    self::assertSame('', eai_job_listing_list_expiration_status(7, 'field_expiration_today'));
    self::assertSame('', eai_job_listing_list_expiration_status(7, 'field_expiration_future'));
    self::assertSame('', eai_job_listing_list_expiration_status(7, 'field_expiration_invalid'));
    self::assertSame('', eai_job_listing_list_expiration_status(7, 'field_expiration_empty'));
    self::assertSame('', eai_job_listing_list_expiration_status(7, 'field_wrong_type'));
    self::assertSame('', eai_job_listing_list_expiration_status(7, ''));
  }

  public function testBuildsEndpointAndEditorSampleProps(): void
  {
    $config = eai_job_listing_list_config_from_settings([
      'post_type' => 'job',
      'page_size' => 3,
      'orderby' => 'date',
      'order' => 'DESC',
      'image_size' => 'large',
      'page_query_param' => 'jobs_page',
      'class_name' => ' jobs-custom ',
      'taxonomy' => 'job_type',
      'include_terms' => ['full-time', 'remote'],
      'employment_type_taxonomy' => 'job_type',
      'location_field' => 'field_location',
      'expiration_date_field' => 'field_expiration',
    ]);

    $endpoint = eai_job_listing_list_endpoint($config);
    self::assertStringStartsWith('/wp-json/eai/v1/job-listing-list?', $endpoint);
    self::assertStringContainsString('post_type=job', $endpoint);
    self::assertStringContainsString('taxonomy=job_type', $endpoint);
    self::assertStringContainsString('include_terms%5B0%5D=full-time', $endpoint);
    self::assertStringContainsString('include_terms%5B1%5D=remote', $endpoint);
    self::assertStringContainsString('employment_type_taxonomy=job_type', $endpoint);
    self::assertStringContainsString('location_field=field_location', $endpoint);
    self::assertStringContainsString('expiration_date_field=field_expiration', $endpoint);

    $sample = eai_job_listing_list_get_editor_sample_props($config);
    self::assertSame('jobs-custom', $sample['className']);
    self::assertSame(3, $sample['pageSize']);
    self::assertSame(1, $sample['initialPage']);
    self::assertSame('jobs_page', $sample['pageQueryParam']);
    self::assertSame(3, $sample['totalPages']);
    self::assertCount(3, $sample['items']);
    self::assertSame('Hành chính-Nhân sự', $sample['items'][0]['categoryLabel']);
  }
}
