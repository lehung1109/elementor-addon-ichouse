<?php
if (! defined('ABSPATH')) {
  exit;
}

function eai_job_listing_list_config_from_settings(array $settings): array
{
  $allowed_orderby = ['date', 'modified', 'title', 'menu_order'];
  $orderby = sanitize_key((string) ($settings['orderby'] ?? 'date'));
  $page_query_param = sanitize_key((string) ($settings['page_query_param'] ?? 'jobs_page'));

  return [
    'post_type' => sanitize_key((string) ($settings['post_type'] ?? 'post')) ?: 'post',
    'page_size' => max(1, min(24, (int) ($settings['page_size'] ?? 3))),
    'orderby' => in_array($orderby, $allowed_orderby, true) ? $orderby : 'date',
    'order' => strtoupper((string) ($settings['order'] ?? 'DESC')) === 'ASC' ? 'ASC' : 'DESC',
    'image_size' => sanitize_key((string) ($settings['image_size'] ?? 'large')) ?: 'large',
    'page_query_param' => $page_query_param ?: 'jobs_page',
    'class_name' => trim((string) ($settings['class_name'] ?? '')),
  ];
}

function eai_job_listing_list_build_query_args(array $config, int $page, int $page_size): array
{
  return [
    'post_type' => (string) ($config['post_type'] ?? 'post'),
    'post_status' => 'publish',
    'posts_per_page' => max(1, min(24, $page_size)),
    'paged' => max(1, $page),
    'orderby' => (string) ($config['orderby'] ?? 'date'),
    'order' => (string) ($config['order'] ?? 'DESC'),
    'ignore_sticky_posts' => true,
    'no_found_rows' => false,
    'meta_query' => [[
      'key' => '_thumbnail_id',
      'compare' => 'EXISTS',
    ]],
  ];
}

function eai_job_listing_list_post_type_label(string $post_type): string
{
  if (function_exists('get_post_type_object')) {
    $object = get_post_type_object($post_type);
    $label = trim((string) ($object->labels->singular_name ?? ''));
    if ($label !== '') {
      return $label;
    }
  }

  return ucfirst(str_replace(['-', '_'], ' ', $post_type));
}

function eai_job_listing_list_description(object $post): string
{
  $excerpt = trim((string) get_the_excerpt($post));
  if ($excerpt !== '') {
    return $excerpt;
  }

  $content = trim(strip_tags((string) ($post->post_content ?? '')));
  if ($content === '') {
    return '';
  }

  return function_exists('wp_trim_words') ? wp_trim_words($content, 40, '…') : $content;
}

function eai_rc_map_job_listing_list_post(object $post, array $config): ?array
{
  if (($post->post_status ?? 'publish') !== 'publish') {
    return null;
  }

  $thumbnail_id = (int) get_post_thumbnail_id($post);
  $title = trim((string) get_the_title($post));
  $permalink = trim((string) get_permalink($post));
  if ($thumbnail_id <= 0 || $title === '' || $permalink === '') {
    return null;
  }

  $image = eai_rc_map_media_model(
    ['id' => $thumbnail_id, 'alt' => $title],
    [],
    null,
    (string) ($config['image_size'] ?? 'large')
  );
  if (trim((string) ($image['url'] ?? '')) === '') {
    return null;
  }

  return [
    'id' => (string) $post->ID,
    'image' => $image,
    'categoryLabel' => eai_job_listing_list_post_type_label((string) ($config['post_type'] ?? ($post->post_type ?? 'post'))),
    'title' => $title,
    'link' => eai_rc_map_link(['url' => $permalink]),
    'statusLabel' => '',
    'employmentType' => '',
    'location' => '',
    'description' => eai_job_listing_list_description($post),
  ];
}

function eai_job_listing_list_query(array $config, int $page = 1, ?int $page_size = null): array
{
  $page_size = max(1, min(24, $page_size ?? (int) ($config['page_size'] ?? 3)));
  $page = max(1, $page);
  if (empty($config['post_type']) || ! class_exists('WP_Query')) {
    return ['items' => [], 'page' => 1, 'totalPages' => 0];
  }

  $query = new WP_Query(eai_job_listing_list_build_query_args($config, $page, $page_size));
  $total_pages = max(0, (int) ($query->max_num_pages ?? 0));
  $resolved_page = $total_pages > 0 ? min($page, $total_pages) : 1;
  if ($resolved_page !== $page) {
    $query = new WP_Query(eai_job_listing_list_build_query_args($config, $resolved_page, $page_size));
  }

  $items = [];
  foreach ((array) ($query->posts ?? []) as $post) {
    $item = is_object($post) ? eai_rc_map_job_listing_list_post($post, $config) : null;
    if ($item !== null) {
      $items[] = $item;
    }
  }
  wp_reset_postdata();

  return ['items' => $items, 'page' => $resolved_page, 'totalPages' => $total_pages];
}

function eai_job_listing_list_endpoint(array $config): string
{
  return add_query_arg([
    'post_type' => $config['post_type'],
    'orderby' => $config['orderby'],
    'order' => $config['order'],
    'image_size' => $config['image_size'],
  ], rest_url('eai/v1/job-listing-list'));
}

function eai_job_listing_list_current_page(string $query_param): int
{
  if (! isset($_GET[$query_param])) {
    return 1;
  }

  return max(1, (int) sanitize_text_field(wp_unslash((string) $_GET[$query_param])));
}

function eai_job_listing_list_get_rc_props(array $settings): array
{
  $config = eai_job_listing_list_config_from_settings($settings);
  $page = eai_job_listing_list_current_page($config['page_query_param']);
  $result = eai_job_listing_list_query($config, $page);
  $props = [
    'listEndpoint' => eai_job_listing_list_endpoint($config),
    'pageSize' => $config['page_size'],
    'items' => $result['items'],
    'totalPages' => $result['totalPages'],
    'initialPage' => $result['page'],
    'pageQueryParam' => $config['page_query_param'],
  ];
  if ($config['class_name'] !== '') {
    $props['className'] = $config['class_name'];
  }
  return $props;
}

function eai_job_listing_list_get_editor_sample_props(array $settings): array
{
  $config = isset($settings['page_size'], $settings['page_query_param'], $settings['post_type'])
    ? eai_job_listing_list_config_from_settings($settings)
    : $settings;
  $items = [];
  $categories = ['Hành chính-Nhân sự', 'Kiến trúc sư', 'Kinh doanh'];
  $titles = ['TUYỂN DỤNG NHÂN VIÊN HÀNH CHÍNH NHÂN SỰ', 'TUYỂN DỤNG KIẾN TRÚC SƯ THIẾT KẾ', 'TUYỂN DỤNG NHÂN VIÊN KINH DOANH'];
  for ($index = 0; $index < 3; $index++) {
    $items[] = [
      'id' => (string) ($index + 1),
      'image' => ['url' => 'https://placehold.co/250x200/png?text=Job+' . ($index + 1), 'alt' => $titles[$index], 'display_dimensions' => ['width' => 250, 'height' => 200]],
      'categoryLabel' => $categories[$index],
      'title' => $titles[$index],
      'link' => eai_rc_map_link(['url' => '#']),
      'statusLabel' => $index < 2 ? 'Hết hạn' : '',
      'employmentType' => 'Toàn thời gian',
      'location' => 'Hà Nội',
      'description' => 'Mô tả ngắn vị trí tuyển dụng tại ICHouse.',
    ];
  }

  $props = [
    'listEndpoint' => eai_job_listing_list_endpoint($config),
    'pageSize' => (int) $config['page_size'],
    'items' => $items,
    'totalPages' => 3,
    'initialPage' => 1,
    'pageQueryParam' => (string) $config['page_query_param'],
  ];
  if (($config['class_name'] ?? '') !== '') {
    $props['className'] = $config['class_name'];
  }
  return $props;
}
