<?php
if (! defined('ABSPATH')) {
  exit;
}

function eai_project_category_gallery_config_from_settings(array $settings): array
{
  $terms = is_array($settings['include_terms'] ?? null) ? $settings['include_terms'] : [];
  $terms = array_values(array_unique(array_filter(array_map(
    static fn($term): string => sanitize_title((string) $term),
    $terms
  ))));
  $page_size = max(1, min(24, (int) ($settings['page_size'] ?? 6)));
  $initial = sanitize_title((string) ($settings['initial_category'] ?? ''));
  if ($initial !== '' && ! in_array($initial, $terms, true)) {
    $initial = '';
  }
  $allowed_orderby = ['date', 'modified', 'title', 'menu_order'];
  $orderby = sanitize_key((string) ($settings['orderby'] ?? 'date'));

  return [
    'post_type' => sanitize_key((string) ($settings['post_type'] ?? 'post')),
    'taxonomy' => sanitize_key((string) ($settings['taxonomy'] ?? 'category')),
    'include_terms' => $terms,
    'page_size' => $page_size,
    'initial_category' => $initial,
    'orderby' => in_array($orderby, $allowed_orderby, true) ? $orderby : 'date',
    'order' => strtoupper((string) ($settings['order'] ?? 'DESC')) === 'ASC' ? 'ASC' : 'DESC',
    'image_size' => sanitize_key((string) ($settings['image_size'] ?? 'large')) ?: 'large',
    'load_more_label' => sanitize_text_field((string) ($settings['load_more_label'] ?? 'XEM THÊM')) ?: 'XEM THÊM',
  ];
}

function eai_project_category_gallery_build_filters(array $config, array $terms): array
{
  $allowed = $config['include_terms'] ?? [];
  $filters = [['label' => 'Tất cả', 'value' => '']];
  foreach ($terms as $term) {
    $slug = sanitize_title((string) ($term->slug ?? ''));
    if ($slug === '' || (! empty($allowed) && ! in_array($slug, $allowed, true))) {
      continue;
    }
    $filters[] = ['label' => (string) ($term->name ?? $slug), 'value' => $slug];
  }
  return $filters;
}

function eai_project_category_gallery_get_terms(array $config): array
{
  if (empty($config['taxonomy']) || ! function_exists('get_terms')) {
    return [];
  }
  $args = ['taxonomy' => $config['taxonomy'], 'hide_empty' => true];
  if (! empty($config['include_terms'])) {
    $args['slug'] = $config['include_terms'];
  }
  $terms = get_terms($args);
  return is_wp_error($terms) ? [] : (array) $terms;
}

function eai_project_category_gallery_resolve_category(string $category, array $include_terms): string
{
  $category = sanitize_title($category);
  if ($category === '' || empty($include_terms) || in_array($category, $include_terms, true)) {
    return $category;
  }
  return '';
}

function eai_project_category_gallery_build_query_args(array $config, string $category, int $page, int $page_size): array
{
  $page = max(1, $page);
  $page_size = max(1, min(24, $page_size));
  $args = [
    'post_type' => $config['post_type'], 'post_status' => 'publish',
    'posts_per_page' => $page_size + 1, 'offset' => ($page - 1) * $page_size,
    'orderby' => $config['orderby'], 'order' => $config['order'],
    'ignore_sticky_posts' => true,
  ];
  $allowed = $config['include_terms'] ?? [];
  if ($category !== '' && (empty($allowed) || in_array($category, $allowed, true))) {
    $args['tax_query'] = [[
      'taxonomy' => $config['taxonomy'], 'field' => 'slug', 'terms' => [$category],
    ]];
  } elseif (! empty($allowed)) {
    $args['tax_query'] = [[
      'taxonomy' => $config['taxonomy'], 'field' => 'slug', 'terms' => $allowed,
    ]];
  }
  return $args;
}

function eai_rc_map_project_category_gallery_post(object $post, array $config, string $category = ''): ?array
{
  $thumbnail_id = (int) get_post_thumbnail_id($post);
  $permalink = (string) get_permalink($post);
  if (trim($permalink) === '') {
    return null;
  }
  if ($category === '' && function_exists('get_the_terms')) {
    $terms = get_the_terms((int) $post->ID, (string) $config['taxonomy']);
    if (! is_wp_error($terms) && ! empty($terms)) {
      $category = (string) $terms[0]->slug;
    }
  }
  $image = $thumbnail_id > 0
    ? eai_rc_map_media_model(['id' => $thumbnail_id], [], null, (string) $config['image_size'])
    : [];
  if (trim((string) ($image['url'] ?? '')) === '') {
    $image = [
      'url' => 'https://placehold.co/600x400?text=anh-dai-dien',
      'alt' => (string) get_the_title($post),
      'display_dimensions' => ['width' => 600, 'height' => 400],
    ];
  }
  return [
    'id' => (string) $post->ID,
    'image' => $image,
    'title' => (string) get_the_title($post),
    'description' => (string) get_the_excerpt($post),
    'link' => eai_rc_map_link(['url' => $permalink]),
    'category' => $category,
  ];
}

function eai_project_category_gallery_query(array $config, string $category = '', int $page = 1, ?int $page_size = null): array
{
  $page_size = $page_size ?? (int) $config['page_size'];
  if (empty($config['post_type']) || empty($config['taxonomy']) || ! class_exists('WP_Query')) {
    return ['items' => [], 'hasMore' => false];
  }
  $query = new WP_Query(eai_project_category_gallery_build_query_args($config, $category, $page, $page_size));
  $items = [];
  foreach ((array) $query->posts as $post) {
    $item = is_object($post) ? eai_rc_map_project_category_gallery_post($post, $config, $category) : null;
    if ($item !== null) {
      $items[] = $item;
    }
  }
  wp_reset_postdata();
  $has_more = count($items) > $page_size;
  return ['items' => array_slice($items, 0, $page_size), 'hasMore' => $has_more];
}

function eai_project_category_gallery_filter_endpoint(array $config): string
{
  return add_query_arg([
    'post_type' => $config['post_type'], 'taxonomy' => $config['taxonomy'],
    'include_terms' => $config['include_terms'], 'orderby' => $config['orderby'],
    'order' => $config['order'], 'image_size' => $config['image_size'],
  ], rest_url('eai/v1/project-category-gallery'));
}

function eai_project_category_gallery_target_id(string $widget_id): string
{
  return 'project-category-gallery-' . sanitize_html_class($widget_id, 'widget');
}

function eai_project_category_gallery_get_rc_props(array $settings, string $widget_id): array
{
  $config = eai_project_category_gallery_config_from_settings($settings);
  $result = eai_project_category_gallery_query($config, $config['initial_category']);
  $props = [
    'filterEndpoint' => eai_project_category_gallery_filter_endpoint($config),
    'pageSize' => $config['page_size'],
    'filters' => eai_project_category_gallery_build_filters($config, eai_project_category_gallery_get_terms($config)),
    'items' => $result['items'], 'hasMore' => $result['hasMore'],
    'initialCategory' => $config['initial_category'], 'loadMoreLabel' => $config['load_more_label'],
    'scrollReveal' => ['targetId' => eai_project_category_gallery_target_id($widget_id)],
  ];
  $class_name = trim((string) ($settings['class_name'] ?? ''));
  if ($class_name !== '') {
    $props['className'] = $class_name;
  }
  return $props;
}

function eai_project_category_gallery_get_editor_sample_props(array $settings, string $widget_id): array
{
  $categories = ['biet-thu-villa', 'nha-pho', 'nha-pho-kinh-doanh', 'van-phong'];
  $labels = ['Biệt thự - Villa', 'Nhà phố', 'Nhà phố kết hợp kinh doanh', 'Văn phòng'];
  $items = [];
  for ($index = 0; $index < 6; $index++) {
    $items[] = [
      'id' => (string) ($index + 1),
      'image' => ['url' => 'https://placehold.co/502x602/png?text=Project+' . ($index + 1), 'alt' => 'Dự án mẫu', 'display_dimensions' => ['width' => 502, 'height' => 602]],
      'title' => 'Dự án mẫu ' . ($index + 1), 'description' => "Chủ đầu tư: ICHouse\nMô hình: Nhà ở",
      'link' => eai_rc_map_link(['url' => '#']), 'category' => $categories[$index % 4],
    ];
  }
  $props = [
    'filterEndpoint' => rest_url('eai/v1/project-category-gallery'),
    'pageSize' => max(1, min(24, (int) ($settings['page_size'] ?? 6))),
    'filters' => array_merge([['label' => 'Tất cả', 'value' => '']], array_map(
      static fn(string $label, string $value): array => ['label' => $label, 'value' => $value], $labels, $categories
    )),
    'items' => $items, 'hasMore' => true, 'initialCategory' => '',
    'loadMoreLabel' => trim((string) ($settings['load_more_label'] ?? 'XEM THÊM')) ?: 'XEM THÊM',
    'scrollReveal' => ['targetId' => eai_project_category_gallery_target_id($widget_id)],
  ];
  $class_name = trim((string) ($settings['class_name'] ?? ''));
  if ($class_name !== '') {
    $props['className'] = $class_name;
  }
  return $props;
}
