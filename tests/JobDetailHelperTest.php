<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class JobDetailHelperTest extends TestCase
{
  public function testMapsManualContentAndFiltersEmptySectionRowsAndBullets(): void
  {
    $props = eai_job_detail_get_rc_props(7, [
      'title' => ' Kiến trúc sư ',
      'vacancies' => ' 2 ',
      'salary' => ' Thỏa thuận ',
      'employment_type' => ' Toàn thời gian ',
      'application_deadline' => ' 31/12/2026 ',
      'apply_label' => ' Ứng tuyển ',
      'apply_link' => ['url' => 'https://example.com/apply', 'is_external' => true, 'nofollow' => true],
      'sidebar_title' => '',
      'sections' => [
        ['title' => ' Mô tả ', 'items' => "Việc một\n\n Việc hai "],
        ['title' => '', 'items' => "Bỏ dòng"],
        ['title' => 'Rỗng', 'items' => " \n "],
      ],
      'employment_type_taxonomy' => 'job_type',
      'location_field' => 'field_location',
      'class_name' => ' custom ',
    ]);

    self::assertSame('Kiến trúc sư', $props['title']);
    self::assertSame(['vacancies' => '2', 'salary' => 'Thỏa thuận', 'employmentType' => 'Toàn thời gian', 'applicationDeadline' => '31/12/2026'], $props['metadata']);
    self::assertSame(['url' => 'https://example.com/apply', 'is_external' => true, 'nofollow' => true], $props['applyLink']);
    self::assertSame([['title' => 'Mô tả', 'items' => ['Việc một', 'Việc hai']]], $props['sections']);
    self::assertSame('Ứng tuyển khác', $props['sidebarTitle']);
    self::assertSame('custom', $props['className']);
  }

  public function testBuildsExactRelatedQueryWithoutThumbnailMetaQuery(): void
  {
    self::assertSame([
      'post_type' => 'job',
      'post_status' => 'publish',
      'posts_per_page' => 4,
      'orderby' => 'date',
      'order' => 'DESC',
      'post__not_in' => [7],
      'ignore_sticky_posts' => true,
      'no_found_rows' => true,
    ], eai_job_detail_build_related_query_args(7, 'job'));
  }

  public function testMapsSidebarItemWithoutThumbnailAndOmitsMetadataIndependently(): void
  {
    $post = (object) ['ID' => 8, 'post_title' => 'Công việc khác', 'post_type' => 'job', 'post_status' => 'publish'];
    $item = eai_job_detail_map_related_post($post, ['employment_type_taxonomy' => 'job_type', 'location_field' => 'field_location']);

    self::assertSame('Jobs', $item['categoryLabel']);
    self::assertSame('Công việc khác', $item['title']);
    self::assertSame([], $item['metadata']);
    self::assertSame('https://example.com/project-8', $item['link']['url']);

    $with_metadata = eai_job_detail_map_related_post((object) ['ID' => 7, 'post_title' => 'Có metadata', 'post_type' => 'job', 'post_status' => 'publish'], ['employment_type_taxonomy' => 'job_type', 'location_field' => 'field_location']);
    self::assertSame(['Toàn thời gian, Làm từ xa', 'Hà Nội'], $with_metadata['metadata']);
    self::assertNull(eai_job_detail_map_related_post((object) ['ID' => 14, 'post_title' => 'Thiếu link', 'post_type' => 'job'], []));
  }

  public function testQueryCapsAtFourAndExcludesCurrentPost(): void
  {
    $items = eai_job_detail_query_related_jobs(7, 'project', []);
    self::assertLessThanOrEqual(4, count($items));
    self::assertNotContains('Biệt thự mẫu', array_column($items, 'title'));
  }

  public function testAggregateKeepsManualColumnWhenContextInvalidAndDetectsEmptyProps(): void
  {
    $props = eai_job_detail_get_rc_props(0, ['title' => ' Nội dung thủ công ']);
    self::assertSame('Nội dung thủ công', $props['title']);
    self::assertSame([], $props['relatedJobs']);
    self::assertFalse(eai_job_detail_props_are_empty($props));
    self::assertTrue(eai_job_detail_props_are_empty(eai_job_detail_get_rc_props(0, [])));
  }

  public function testBuildsCompleteEditorSample(): void
  {
    $sample = eai_job_detail_get_editor_sample_props([]);
    self::assertFalse(eai_job_detail_props_are_empty($sample));
    self::assertNotSame('', $sample['title']);
    self::assertNotEmpty($sample['sections']);
    self::assertNotEmpty($sample['relatedJobs']);
  }

  public function testSourceWiringUsesContextFallbackEditorOnlyWhenAllPropsEmptyAndRawSsr(): void
  {
    $root = dirname(__DIR__);
    $widget = file_get_contents($root . '/includes/widgets/EAI-job-detail.php');
    $template = file_get_contents($root . '/includes/templates/EAI-job-detail.php');
    $helpers = file_get_contents($root . '/includes/helpers/bootstrap.php');
    $plugin = file_get_contents($root . '/includes/plugin.php');

    self::assertIsString($widget);
    self::assertStringContainsString("eai_rc_render_html('JobDetail'", $widget);
    self::assertStringContainsString("'title' => ['Tiêu đề', 'ICHOUSE tuyển dụng Content Creator - HCM']", $widget);
    self::assertStringContainsString("'vacancies' => ['Số lượng cần tuyển', '1']", $widget);
    self::assertStringContainsString("'salary' => ['Mức lương', '10.000.000 - 12.000.000']", $widget);
    self::assertStringContainsString("'employment_type' => ['Tính chất công việc', 'Toàn thời gian']", $widget);
    self::assertStringContainsString("'application_deadline' => ['Hạn ứng tuyển', '31/03/2026']", $widget);
    self::assertStringContainsString("'default' => ['url' => '/ung-tuyen/content-creator', 'is_external' => false, 'nofollow' => false]", $widget);
    self::assertStringContainsString("'title' => 'Mô tả công việc'", $widget);
    self::assertStringContainsString("'title' => 'Quyền lợi được hưởng'", $widget);
    self::assertStringContainsString("'title' => 'Yêu cầu công việc'", $widget);
    self::assertStringContainsString("'title' => 'Thông tin liên hệ'", $widget);
    self::assertStringContainsString('is_singular()', $widget);
    self::assertStringContainsString('get_queried_object_id()', $widget);
    self::assertStringContainsString('get_the_ID()', $widget);
    self::assertStringContainsString('eai_is_elementor_edit_mode()', $widget);
    self::assertStringContainsString('eai_job_detail_props_are_empty', $widget);
    self::assertIsString($template);
    self::assertStringContainsString('echo $html;', $template);
    self::assertStringNotContainsString('wp_kses_post', $template);
    self::assertStringContainsString("require_once __DIR__ . '/job-detail.php';", (string) $helpers);
    self::assertStringContainsString("widgets/EAI-job-detail.php", (string) $plugin);
    self::assertStringContainsString('new \\EAI_Job_Detail_Widget()', (string) $plugin);
  }
}
