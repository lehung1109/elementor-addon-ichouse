<?php
if (! defined('ABSPATH')) {
  exit;
}

function eai_job_detail_map_sections(array $rows): array
{
  $sections = [];
  foreach ($rows as $row) {
    if (! is_array($row)) {
      continue;
    }
    $title = trim((string) ($row['title'] ?? ''));
    $items = preg_split('/\R/u', (string) ($row['items'] ?? '')) ?: [];
    $items = array_values(array_filter(array_map(static fn ($item): string => trim((string) $item), $items)));
    if ($title !== '' && $items !== []) {
      $sections[] = ['title' => $title, 'items' => $items];
    }
  }
  return $sections;
}

function eai_job_detail_build_related_query_args(int $current_id, string $post_type): array
{
  return [
    'post_type' => $post_type,
    'post_status' => 'publish',
    'posts_per_page' => 4,
    'orderby' => 'date',
    'order' => 'DESC',
    'post__not_in' => [$current_id],
    'ignore_sticky_posts' => true,
    'no_found_rows' => true,
  ];
}

function eai_job_detail_map_related_post(object $post, array $config): ?array
{
  $title = trim((string) get_the_title($post));
  $permalink = trim((string) get_permalink($post));
  if ($title === '' || $permalink === '') {
    return null;
  }

  $post_type = (string) get_post_type($post);
  $metadata = array_values(array_filter([
    eai_job_listing_list_term_names((int) $post->ID, $post_type, sanitize_key((string) ($config['employment_type_taxonomy'] ?? ''))),
    eai_job_listing_list_location((int) $post->ID, sanitize_key((string) ($config['location_field'] ?? ''))),
  ]));

  return [
    'categoryLabel' => eai_job_listing_list_post_type_label($post_type),
    'title' => $title,
    'link' => eai_rc_map_link(['url' => $permalink]),
    'metadata' => $metadata,
  ];
}

function eai_job_detail_query_related_jobs(int $current_id, string $post_type, array $config): array
{
  if ($current_id <= 0 || $post_type === '') {
    return [];
  }

  $items = [];
  foreach (get_posts(eai_job_detail_build_related_query_args($current_id, $post_type)) as $post) {
    if (! is_object($post) || (int) ($post->ID ?? 0) === $current_id) {
      continue;
    }
    $item = eai_job_detail_map_related_post($post, $config);
    if ($item !== null) {
      $items[] = $item;
    }
    if (count($items) >= 4) {
      break;
    }
  }
  return $items;
}

function eai_job_detail_get_rc_props(int $current_id, array $settings): array
{
  $post_type = $current_id > 0 ? get_post_type($current_id) : false;
  $props = [
    'title' => trim((string) ($settings['title'] ?? '')),
    'metadata' => [
      'vacancies' => trim((string) ($settings['vacancies'] ?? '')),
      'salary' => trim((string) ($settings['salary'] ?? '')),
      'employmentType' => trim((string) ($settings['employment_type'] ?? '')),
      'applicationDeadline' => trim((string) ($settings['application_deadline'] ?? '')),
    ],
    'applyLabel' => trim((string) ($settings['apply_label'] ?? '')),
    'applyLink' => eai_rc_map_link(is_array($settings['apply_link'] ?? null) ? $settings['apply_link'] : []),
    'sections' => eai_job_detail_map_sections((array) ($settings['sections'] ?? [])),
    'sidebarTitle' => trim((string) ($settings['sidebar_title'] ?? '')) ?: 'Ứng tuyển khác',
    'relatedJobs' => $post_type ? eai_job_detail_query_related_jobs($current_id, $post_type, $settings) : [],
  ];
  $class_name = trim((string) ($settings['class_name'] ?? ''));
  if ($class_name !== '') {
    $props['className'] = $class_name;
  }
  return $props;
}

function eai_job_detail_props_are_empty(array $props): bool
{
  return trim((string) ($props['title'] ?? '')) === ''
    && empty(array_filter((array) ($props['metadata'] ?? []), static fn ($value): bool => trim((string) $value) !== ''))
    && (trim((string) ($props['applyLabel'] ?? '')) === '' || trim((string) ($props['applyLink']['url'] ?? '')) === '')
    && empty($props['sections'])
    && empty($props['relatedJobs']);
}

function eai_job_detail_get_editor_sample_props(array $settings): array
{
  $settings['title'] = 'ICHOUSE tuyển dụng Content Creator - HCM';
  $settings['vacancies'] = '1';
  $settings['salary'] = '10.000.000 - 12.000.000';
  $settings['employment_type'] = 'Toàn thời gian';
  $settings['application_deadline'] = '31/03/2026';
  $settings['apply_label'] = 'Ứng tuyển';
  $settings['apply_link'] = ['url' => '#'];
  $settings['sections'] = [['title' => 'Mô tả công việc', 'items' => "Sản xuất nội dung cho các nền tảng truyền thông.\nPhối hợp cùng đội ngũ thiết kế."]];
  $props = eai_job_detail_get_rc_props(0, $settings);
  $props['relatedJobs'] = [[
    'categoryLabel' => 'ICHOUSE tuyển dụng',
    'title' => 'Kiến trúc sư triển khai',
    'link' => eai_rc_map_link(['url' => '#']),
    'metadata' => ['Toàn thời gian', 'Hà Nội'],
  ]];
  return $props;
}
