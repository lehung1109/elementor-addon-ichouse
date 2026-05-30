<?php
if (! defined('ABSPATH')) {
  exit;
}

if (! function_exists('eai_page_title_bar_resolve_taxonomy')) {
  /**
   * Validate taxonomy against post type; fallback to category when supported.
   */
  function eai_page_title_bar_resolve_taxonomy(int $post_id, string $taxonomy): string
  {
    $taxonomy = sanitize_key($taxonomy);
    if ($taxonomy === '') {
      $taxonomy = 'category';
    }

    $post_type = get_post_type($post_id);
    if (! is_string($post_type) || $post_type === '') {
      return '';
    }

    if (is_object_in_taxonomy($post_type, $taxonomy)) {
      return $taxonomy;
    }

    if (is_object_in_taxonomy($post_type, 'category')) {
      return 'category';
    }

    return '';
  }
}

if (! function_exists('eai_page_title_bar_map_term_items')) {
  /**
   * Breadcrumb level-2 items from terms assigned directly to the post.
   *
   * @return array<int, array{label: string, link: array{url: string, is_external: bool, nofollow: bool}}>
   */
  function eai_page_title_bar_map_term_items(int $post_id, string $taxonomy): array
  {
    if ($post_id <= 0 || $taxonomy === '') {
      return [];
    }

    $terms = get_the_terms($post_id, $taxonomy);
    if ($terms === false || is_wp_error($terms) || $terms === []) {
      return [];
    }

    $filtered = [];
    foreach ($terms as $term) {
      if (! ($term instanceof \WP_Term)) {
        continue;
      }
      if (eai_related_posts_is_excluded_term($term, $taxonomy)) {
        continue;
      }
      $filtered[] = $term;
    }

    if ($filtered === []) {
      return [];
    }

    $sorted = eai_related_posts_sort_terms($filtered);
    $items = [];

    foreach ($sorted as $term) {
      $url = get_term_link($term);
      if (is_wp_error($url)) {
        continue;
      }

      $items[] = [
        'label' => $term->name,
        'link' => eai_rc_map_link(['url' => $url]),
      ];
    }

    return $items;
  }
}

if (! function_exists('eai_page_title_bar_get_rc_props')) {
  /**
   * @param array<string, mixed> $settings
   * @return array<string, mixed>
   */
  function eai_page_title_bar_get_rc_props(int $post_id, array $settings): array
  {
    $taxonomy = sanitize_key((string) ($settings['taxonomy'] ?? 'category'));
    if ($taxonomy === '') {
      $taxonomy = 'category';
    }

    $resolved_taxonomy = eai_page_title_bar_resolve_taxonomy($post_id, $taxonomy);

    $home_label = trim((string) ($settings['home_label'] ?? 'Home'));
    if ($home_label === '') {
      $home_label = 'Home';
    }

    $term_items = $resolved_taxonomy !== ''
      ? eai_page_title_bar_map_term_items($post_id, $resolved_taxonomy)
      : [];

    $props = [
      'title' => get_the_title($post_id),
      'breadcrumbLevels' => [
        [
          'items' => [
            [
              'label' => $home_label,
              'link' => eai_rc_map_link(['url' => home_url('/')]),
            ],
          ],
        ],
        [
          'items' => $term_items,
        ],
      ],
    ];

    $class_name = trim((string) ($settings['class_name'] ?? ''));
    if ($class_name !== '') {
      $props['className'] = $class_name;
    }

    return $props;
  }
}
