<?php
if (! defined('ABSPATH')) {
  exit;
}

function eai_news_list_config_from_settings(array $settings): array
{
  $post_type = sanitize_key((string) ($settings['post_type'] ?? 'post')) ?: 'post';
  $page_query_param = sanitize_key((string) ($settings['page_query_param'] ?? 'paged')) ?: 'paged';
  $taxonomy = sanitize_key((string) ($settings['taxonomy'] ?? ''));
  $include_terms = array_values(array_unique(array_filter(array_map(
    static fn ($term): string => sanitize_title((string) $term),
    (array) ($settings['taxonomy_terms_' . $taxonomy] ?? [])
  ))));

  return [
    'post_type' => $post_type,
    'page_size' => max(1, min(24, (int) ($settings['page_size'] ?? 5))),
    'image_size' => sanitize_key((string) ($settings['image_size'] ?? 'large')) ?: 'large',
    'featured_background_image' => is_array($settings['featured_background_image'] ?? null)
      ? $settings['featured_background_image']
      : [],
    'page_query_param' => $page_query_param,
    'class_name' => trim((string) ($settings['class_name'] ?? '')),
    'taxonomy' => $taxonomy,
    'include_terms' => $include_terms,
  ];
}

function eai_news_list_resolve_term_slugs(array $config): array
{
  $post_type = (string) ($config['post_type'] ?? '');
  $taxonomy = (string) ($config['taxonomy'] ?? '');
  $include_terms = (array) ($config['include_terms'] ?? []);
  $taxonomy_object = $taxonomy !== '' && taxonomy_exists($taxonomy) ? get_taxonomy($taxonomy) : false;
  if ($taxonomy_object === false || empty($taxonomy_object->public) || ! is_object_in_taxonomy($post_type, $taxonomy)) {
    return [];
  }

  if ($include_terms === []) {
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

function eai_news_list_build_query_args(array $config, int $page, int $page_size): array
{
  $args = [
    'post_type' => (string) ($config['post_type'] ?? 'post'),
    'post_status' => 'publish',
    'posts_per_page' => max(1, min(24, $page_size)),
    'paged' => max(1, $page),
    'orderby' => 'modified',
    'order' => 'DESC',
    'ignore_sticky_posts' => true,
    'no_found_rows' => false,
    'meta_query' => [[
      'key' => '_thumbnail_id',
      'compare' => 'EXISTS',
    ]],
  ];

  $taxonomy = (string) ($config['taxonomy'] ?? '');
  $taxonomy_object = $taxonomy !== '' && taxonomy_exists($taxonomy) ? get_taxonomy($taxonomy) : false;
  if ($taxonomy_object !== false && ! empty($taxonomy_object->public) && is_object_in_taxonomy((string) ($config['post_type'] ?? 'post'), $taxonomy)) {
    $term_slugs = eai_news_list_resolve_term_slugs($config);
    if ($term_slugs !== []) {
      $args['tax_query'] = [[
        'taxonomy' => $taxonomy,
        'field' => 'slug',
        'terms' => $term_slugs,
        'operator' => 'IN',
      ]];
    } elseif (empty($config['include_terms'])) {
      $args['tax_query'] = [[
        'taxonomy' => $taxonomy,
        'field' => 'slug',
        'terms' => [],
        'operator' => 'EXISTS',
      ]];
    }
  }

  return $args;
}

function eai_news_list_description(object $post): string
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

function eai_rc_map_news_list_post(object $post, array $config): ?array
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

  $background = eai_rc_map_media_model(
    (array) ($config['featured_background_image'] ?? []),
    [],
    null,
    (string) ($config['image_size'] ?? 'large')
  );
  if (trim((string) ($background['url'] ?? '')) === '') {
    $background = $image;
    $background['alt'] = '';
  }

  return [
    'id' => (string) $post->ID,
    'image' => $image,
    'backgroundImage' => $background,
    'time' => (string) get_the_modified_date('', $post),
    'title' => $title,
    'description' => eai_news_list_description($post),
    'link' => eai_rc_map_link(['url' => $permalink]),
  ];
}

function eai_news_list_current_page(string $query_param): int
{
  if (! isset($_GET[$query_param])) {
    return 1;
  }

  return max(1, (int) sanitize_text_field(wp_unslash((string) $_GET[$query_param])));
}

function eai_news_list_query(array $config, int $page = 1, ?int $page_size = null): array
{
  $page_size = max(1, min(24, $page_size ?? (int) ($config['page_size'] ?? 5)));
  $page = max(1, $page);
  if (empty($config['post_type']) || ! class_exists('WP_Query')) {
    return ['items' => [], 'page' => 1, 'totalPages' => 0];
  }

  $query = new WP_Query(eai_news_list_build_query_args($config, $page, $page_size));
  $total_pages = max(0, (int) ($query->max_num_pages ?? 0));
  $resolved_page = $total_pages > 0 ? min($page, $total_pages) : 1;
  if ($resolved_page !== $page) {
    $query = new WP_Query(eai_news_list_build_query_args($config, $resolved_page, $page_size));
  }

  $items = [];
  foreach ((array) ($query->posts ?? []) as $post) {
    $item = is_object($post) ? eai_rc_map_news_list_post($post, $config) : null;
    if ($item !== null) {
      $items[] = $item;
    }
  }
  wp_reset_postdata();

  return ['items' => $items, 'page' => $resolved_page, 'totalPages' => $total_pages];
}

function eai_news_list_endpoint(array $config): string
{
  return add_query_arg([
    'post_type' => $config['post_type'],
    'image_size' => $config['image_size'],
    'featured_background_image' => (array) ($config['featured_background_image'] ?? []),
    'taxonomy' => $config['taxonomy'] ?? '',
    'include_terms' => $config['include_terms'] ?? [],
  ], rest_url('eai/v1/news-list'));
}

function eai_news_list_config_from_request(\WP_REST_Request $request): array
{
  $taxonomy = sanitize_key((string) $request->get_param('taxonomy'));

  return eai_news_list_config_from_settings([
    'post_type' => $request->get_param('post_type'),
    'image_size' => $request->get_param('image_size'),
    'featured_background_image' => $request->get_param('featured_background_image'),
    'taxonomy' => $taxonomy,
    'taxonomy_terms_' . $taxonomy => $request->get_param('include_terms'),
    'page_size' => 5,
  ]);
}

function eai_news_list_get_rc_props(array $settings): array
{
  $config = eai_news_list_config_from_settings($settings);
  $result = eai_news_list_query(
    $config,
    eai_news_list_current_page($config['page_query_param'])
  );
  $props = [
    'listEndpoint' => eai_news_list_endpoint($config),
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

function eai_news_list_get_editor_sample_props(array $settings): array
{
  $config = eai_news_list_config_from_settings($settings);
  $items = [];
  $titles = [
    'Một buổi lễ khởi công, nhưng là cả hành trình chuẩn bị chỉn chu!',
    'Top 8 thiết kế nhà 2 mặt tiền kinh doanh không nên bỏ qua',
    'Gợi ý 6 mẫu nhà văn phòng 7 tầng hiện đại chuẩn xu hướng',
    'Xây nhà biệt thự 2 tầng đẹp hiện đại với 6 mẫu thiết kế của năm',
    '5+ mẫu nhà ở kết hợp văn phòng cho thuê đẹp và tối ưu công năng',
  ];
  foreach ($titles as $index => $title) {
    $id = $index + 1;
    $items[] = [
      'id' => (string) $id,
      'image' => [
        'url' => 'https://placehold.co/800x450/png?text=News+' . $id,
        'alt' => $title,
        'display_dimensions' => ['width' => 800, 'height' => 450],
      ],
      'backgroundImage' => [
        'url' => 'https://placehold.co/800x450/png?text=Featured+Background',
        'alt' => '',
        'display_dimensions' => ['width' => 800, 'height' => 450],
      ],
      'time' => sprintf('%02d/07/2026', 23 - $index),
      'title' => $title,
      'description' => 'Vừa qua, trong niềm hứng khởi và hân hoan, đội ngũ đã chuẩn bị chỉn chu để khởi đầu hành trình mới.',
      'link' => eai_rc_map_link(['url' => '#']),
    ];
  }

  $props = [
    'listEndpoint' => eai_news_list_endpoint($config),
    'pageSize' => $config['page_size'],
    'items' => $items,
    'totalPages' => 3,
    'initialPage' => 1,
    'pageQueryParam' => $config['page_query_param'],
  ];
  if ($config['class_name'] !== '') {
    $props['className'] = $config['class_name'];
  }

  return $props;
}
