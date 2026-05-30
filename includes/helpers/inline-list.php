<?php
if (! defined('ABSPATH')) {
  exit;
}

if (! function_exists('eai_inline_list_resolve_taxonomy')) {
  function eai_inline_list_resolve_taxonomy(string $taxonomy): string
  {
    $taxonomy = sanitize_key($taxonomy);
    if ($taxonomy === '' || ! taxonomy_exists($taxonomy)) {
      return '';
    }

    $allowed = eai_get_public_taxonomy_options();
    if (! isset($allowed[$taxonomy])) {
      return '';
    }

    return $taxonomy;
  }
}

if (! function_exists('eai_inline_list_map_term_items')) {
  /**
   * @return array<int, array{text: string, link: array{url: string, is_external: bool, nofollow: bool}}>
   */
  function eai_inline_list_map_term_items(string $taxonomy): array
  {
    $taxonomy = eai_inline_list_resolve_taxonomy($taxonomy);
    if ($taxonomy === '') {
      return [];
    }

    $terms = get_terms(
      [
        'taxonomy' => $taxonomy,
        'hide_empty' => false,
      ]
    );

    if (is_wp_error($terms) || $terms === []) {
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
        'text' => $term->name,
        'link' => eai_rc_map_link(['url' => $url]),
      ];
    }

    return $items;
  }
}

if (! function_exists('eai_inline_list_get_rc_props')) {
  /**
   * @param array<string, mixed> $settings
   * @return array<string, mixed>
   */
  function eai_inline_list_get_rc_props(array $settings): array
  {
    $taxonomy = (string) ($settings['taxonomy'] ?? 'category');
    $props = [
      'items' => eai_inline_list_map_term_items($taxonomy),
    ];

    $class_name = trim((string) ($settings['class_name'] ?? ''));
    if ($class_name !== '') {
      $props['className'] = $class_name;
    }

    return $props;
  }
}

if (! function_exists('eai_inline_list_get_editor_sample_props')) {
  /**
   * Static demo props for Elementor editor when taxonomy has no terms.
   *
   * @param array<string, mixed> $settings
   * @return array<string, mixed>
   */
  function eai_inline_list_get_editor_sample_props(array $settings): array
  {
    $google_link = [
      'url' => 'https://www.google.com',
      'is_external' => true,
      'nofollow' => false,
    ];

    $props = [
      'items' => [
        ['text' => 'Google 1', 'link' => $google_link],
        ['text' => 'Google 2', 'link' => $google_link],
        ['text' => 'Google 3', 'link' => $google_link],
      ],
    ];

    $class_name = trim((string) ($settings['class_name'] ?? ''));
    if ($class_name !== '') {
      $props['className'] = $class_name;
    }

    return $props;
  }
}
