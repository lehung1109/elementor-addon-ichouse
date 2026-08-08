<?php
if (! defined('ABSPATH')) {
  exit;
}

function eai_job_listing_list_config_from_settings(array $settings): array
{
  $allowed_orderby = ['date', 'modified', 'title', 'menu_order'];
  $orderby = sanitize_key((string) ($settings['orderby'] ?? 'date'));
  $page_query_param = sanitize_key((string) ($settings['page_query_param'] ?? 'jobs_page'));
  $include_terms = array_values(array_unique(array_filter(array_map(
    static fn ($term): string => sanitize_title((string) $term),
    (array) ($settings['include_terms'] ?? [])
  ))));

  return [
    'post_type' => sanitize_key((string) ($settings['post_type'] ?? 'post')) ?: 'post',
    'page_size' => max(1, min(24, (int) ($settings['page_size'] ?? 3))),
    'orderby' => in_array($orderby, $allowed_orderby, true) ? $orderby : 'date',
    'order' => strtoupper((string) ($settings['order'] ?? 'DESC')) === 'ASC' ? 'ASC' : 'DESC',
    'image_size' => sanitize_key((string) ($settings['image_size'] ?? 'large')) ?: 'large',
    'page_query_param' => $page_query_param ?: 'jobs_page',
    'class_name' => trim((string) ($settings['class_name'] ?? '')),
    'taxonomy' => sanitize_key((string) ($settings['taxonomy'] ?? '')),
    'include_terms' => $include_terms,
    'employment_type_taxonomy' => sanitize_key((string) ($settings['employment_type_taxonomy'] ?? '')),
    'location_field' => sanitize_key((string) ($settings['location_field'] ?? '')),
    'expiration_date_field' => sanitize_key((string) ($settings['expiration_date_field'] ?? '')),
  ];
}

function eai_job_listing_list_resolve_term_slugs(array $config): array
{
  $post_type = (string) ($config['post_type'] ?? '');
  $taxonomy = (string) ($config['taxonomy'] ?? '');
  $include_terms = (array) ($config['include_terms'] ?? []);
  $taxonomy_object = $taxonomy !== '' && taxonomy_exists($taxonomy) ? get_taxonomy($taxonomy) : false;
  if ($taxonomy_object === false || empty($taxonomy_object->public) || $include_terms === [] || ! is_object_in_taxonomy($post_type, $taxonomy)) {
    return [];
  }

  $terms = get_terms([
    'taxonomy' => $taxonomy,
    'hide_empty' => false,
    'slug' => $include_terms,
  ]);
  if (is_wp_error($terms)) {
    return [];
  }

  $valid_slugs = array_map(static fn ($term): string => (string) ($term->slug ?? ''), (array) $terms);

  return array_values(array_intersect($include_terms, $valid_slugs));
}

function eai_job_listing_list_build_query_args(array $config, int $page, int $page_size): array
{
  $args = [
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

  $term_slugs = eai_job_listing_list_resolve_term_slugs($config);
  if ($term_slugs !== []) {
    $args['tax_query'] = [[
      'taxonomy' => (string) $config['taxonomy'],
      'field' => 'slug',
      'terms' => $term_slugs,
      'operator' => 'IN',
    ]];
  }

  return $args;
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

function eai_job_listing_list_term_names(int $post_id, string $post_type, string $taxonomy): string
{
  $taxonomy_object = $taxonomy !== '' && taxonomy_exists($taxonomy) ? get_taxonomy($taxonomy) : false;
  if ($taxonomy_object === false || empty($taxonomy_object->public) || ! is_object_in_taxonomy($post_type, $taxonomy)) {
    return '';
  }

  $terms = get_the_terms($post_id, $taxonomy);
  if (is_wp_error($terms) || empty($terms)) {
    return '';
  }

  $names = array_values(array_unique(array_filter(array_map(
    static fn ($term): string => trim((string) ($term->name ?? '')),
    (array) $terms
  ))));

  return implode(', ', $names);
}

function eai_job_listing_list_acf_field(int $post_id, string $field_key, array $allowed_types): array|false
{
  if ($field_key === '' || ! function_exists('get_field_object')) {
    return false;
  }

  $field = get_field_object($field_key, $post_id, false, true);
  if (! is_array($field) || sanitize_key((string) ($field['key'] ?? '')) !== $field_key) {
    return false;
  }

  return in_array((string) ($field['type'] ?? ''), $allowed_types, true) ? $field : false;
}

function eai_job_listing_list_location(int $post_id, string $field_key): string
{
  $field = eai_job_listing_list_acf_field($post_id, $field_key, ['text', 'textarea', 'select', 'radio']);
  if ($field === false) {
    return '';
  }

  $value = $field['value'] ?? '';
  if (in_array($field['type'], ['select', 'radio'], true)) {
    $values = is_array($value) ? $value : [$value];
    $labels = array_map(
      static fn ($item): string => trim((string) (($field['choices'] ?? [])[(string) $item] ?? $item)),
      $values
    );
    return implode(', ', array_values(array_filter($labels)));
  }

  return is_scalar($value) ? trim((string) $value) : '';
}

function eai_job_listing_list_expiration_status(int $post_id, string $field_key): string
{
  $field = eai_job_listing_list_acf_field($post_id, $field_key, ['date_picker']);
  if ($field === false) {
    return '';
  }

  $raw_date = trim((string) ($field['value'] ?? ''));
  $expiration = \DateTimeImmutable::createFromFormat('!Ymd', $raw_date, wp_timezone());
  $errors = \DateTimeImmutable::getLastErrors();
  if ($expiration === false || (is_array($errors) && ($errors['warning_count'] > 0 || $errors['error_count'] > 0))) {
    return '';
  }

  $today = \DateTimeImmutable::createFromFormat('!Ymd', current_time('Ymd'), wp_timezone());
  return $today !== false && $today > $expiration ? 'Hết hạn' : '';
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

  $post_type = (string) ($config['post_type'] ?? ($post->post_type ?? 'post'));

  return [
    'id' => (string) $post->ID,
    'image' => $image,
    'categoryLabel' => eai_job_listing_list_post_type_label($post_type),
    'title' => $title,
    'link' => eai_rc_map_link(['url' => $permalink]),
    'statusLabel' => eai_job_listing_list_expiration_status((int) $post->ID, (string) ($config['expiration_date_field'] ?? '')),
    'employmentType' => eai_job_listing_list_term_names((int) $post->ID, $post_type, (string) ($config['employment_type_taxonomy'] ?? '')),
    'location' => eai_job_listing_list_location((int) $post->ID, (string) ($config['location_field'] ?? '')),
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
    'taxonomy' => $config['taxonomy'] ?? '',
    'include_terms' => $config['include_terms'] ?? [],
    'employment_type_taxonomy' => $config['employment_type_taxonomy'] ?? '',
    'location_field' => $config['location_field'] ?? '',
    'expiration_date_field' => $config['expiration_date_field'] ?? '',
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
