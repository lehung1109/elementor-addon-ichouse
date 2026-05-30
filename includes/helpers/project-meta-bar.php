<?php
if (! defined('ABSPATH')) {
  exit;
}

if (! function_exists('eai_get_project_meta_bar_icon_options')) {
  /**
   * @return array<string, string>
   */
  function eai_get_project_meta_bar_icon_options(): array
  {
    return [
      'user-round' => esc_html__('Người', 'eai'),
      'bed-double' => esc_html__('Phòng ngủ', 'eai'),
      'palette' => esc_html__('Phong cách', 'eai'),
      'ruler' => esc_html__('Diện tích', 'eai'),
    ];
  }
}

if (! function_exists('eai_project_meta_bar_resolve_icon')) {
  function eai_project_meta_bar_resolve_icon(string $icon): string
  {
    $icon = sanitize_key($icon);
    $allowed = array_keys(eai_get_project_meta_bar_icon_options());

    if (in_array($icon, $allowed, true)) {
      return $icon;
    }

    return 'user-round';
  }
}

if (! function_exists('eai_project_meta_bar_get_post_type_for_controls')) {
  function eai_project_meta_bar_get_post_type_for_controls(): string
  {
    $post_id = (int) get_queried_object_id();
    if ($post_id <= 0) {
      $post_id = (int) get_the_ID();
    }

    if ($post_id > 0) {
      $post_type = get_post_type($post_id);
      if (is_string($post_type) && $post_type !== '') {
        return $post_type;
      }
    }

    return 'post';
  }
}

if (! function_exists('eai_project_meta_bar_map_term_names')) {
  function eai_project_meta_bar_map_term_names(int $post_id, string $taxonomy): string
  {
    if ($post_id <= 0 || $taxonomy === '') {
      return '';
    }

    $terms = get_the_terms($post_id, $taxonomy);
    if ($terms === false || is_wp_error($terms) || $terms === []) {
      return '';
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
      return '';
    }

    $sorted = eai_related_posts_sort_terms($filtered);
    $names = [];

    foreach ($sorted as $term) {
      $names[] = $term->name;
    }

    return implode(', ', $names);
  }
}

if (! function_exists('eai_project_meta_bar_get_rc_props')) {
  /**
   * @param array<string, mixed> $settings
   * @return array<string, mixed>
   */
  function eai_project_meta_bar_get_rc_props(int $post_id, array $settings): array
  {
    $columns_setting = $settings['columns'] ?? [];
    if (! is_array($columns_setting)) {
      $columns_setting = [];
    }

    $columns = [];

    foreach ($columns_setting as $row) {
      if (count($columns) >= 4) {
        break;
      }

      if (! is_array($row)) {
        continue;
      }

      $taxonomy = sanitize_key((string) ($row['taxonomy'] ?? ''));
      if ($taxonomy === '') {
        continue;
      }

      $resolved_taxonomy = eai_page_title_bar_resolve_taxonomy($post_id, $taxonomy);
      if ($resolved_taxonomy === '') {
        continue;
      }

      $taxonomy_obj = get_taxonomy($resolved_taxonomy);
      $title = '';
      if ($taxonomy_obj instanceof \WP_Taxonomy) {
        $title = (string) $taxonomy_obj->labels->singular_name;
      }

      $content = eai_project_meta_bar_map_term_names($post_id, $resolved_taxonomy);

      $icon = eai_project_meta_bar_resolve_icon((string) ($row['icon'] ?? ''));

      if (trim($title) === '' && trim($content) === '') {
        continue;
      }

      $columns[] = [
        'title' => $title,
        'content' => $content,
        'icon' => $icon,
      ];
    }

    $props = [
      'columns' => $columns,
    ];

    $class_name = trim((string) ($settings['class_name'] ?? ''));
    if ($class_name !== '') {
      $props['className'] = $class_name;
    }

    return $props;
  }
}
