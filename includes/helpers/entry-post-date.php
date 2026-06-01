<?php
if (! defined('ABSPATH')) {
  exit;
}

if (! function_exists('eai_entry_post_date_format_label')) {
  function eai_entry_post_date_format_label(int $post_id): string
  {
    if ($post_id <= 0) {
      return '';
    }

    $timestamp = get_post_timestamp($post_id);
    if ($timestamp === false) {
      return '';
    }

    $month_names = [
      1 => 'Một',
      2 => 'Hai',
      3 => 'Ba',
      4 => 'Tư',
      5 => 'Năm',
      6 => 'Sáu',
      7 => 'Bảy',
      8 => 'Tám',
      9 => 'Chín',
      10 => 'Mười',
      11 => 'Mười Một',
      12 => 'Mười Hai',
    ];

    $day = (int) wp_date('j', $timestamp);
    $month = (int) wp_date('n', $timestamp);
    $year = wp_date('Y', $timestamp);

    if (! isset($month_names[$month])) {
      return '';
    }

    return sprintf('%d Tháng %s, %s', $day, $month_names[$month], $year);
  }
}

if (! function_exists('eai_entry_post_date_map_term')) {
  /**
   * First term item after inline-list sort/exclude rules.
   *
   * @return array{text: string, link: array{url: string, is_external: bool, nofollow: bool}}|null
   */
  function eai_entry_post_date_map_term(int $post_id, string $taxonomy): ?array
  {
    $items = eai_inline_list_map_term_items($post_id, $taxonomy);
    if ($items === []) {
      return null;
    }

    return $items[0];
  }
}

if (! function_exists('eai_entry_post_date_get_term_prefix')) {
  function eai_entry_post_date_get_term_prefix(array $settings): string
  {
    if (array_key_exists('term_prefix', $settings)) {
      return (string) $settings['term_prefix'];
    }

    return ' by KTS. ';
  }
}

if (! function_exists('eai_entry_post_date_get_rc_props')) {
  /**
   * @param array<string, mixed> $settings
   * @return array<string, mixed>
   */
  function eai_entry_post_date_get_rc_props(int $post_id, array $settings): array
  {
    $taxonomy = (string) ($settings['taxonomy'] ?? 'category');
    $term = eai_entry_post_date_map_term($post_id, $taxonomy);

    $props = [
      'dateLabel' => eai_entry_post_date_format_label($post_id),
      'dateLink' => eai_rc_map_link(['url' => home_url('/')]),
      'term' => $term ?? ['text' => '', 'link' => eai_rc_map_link(['url' => ''])],
      'termPrefix' => eai_entry_post_date_get_term_prefix($settings),
    ];

    $class_name = trim((string) ($settings['class_name'] ?? ''));
    if ($class_name !== '') {
      $props['className'] = $class_name;
    }

    return $props;
  }
}

if (! function_exists('eai_entry_post_date_get_editor_sample_props')) {
  /**
   * Static demo props for Elementor editor (mirrors api-rc/src/data/entry-post-date.ts).
   *
   * @param array<string, mixed> $settings
   * @return array<string, mixed>
   */
  function eai_entry_post_date_get_editor_sample_props(array $settings): array
  {
    $props = [
      'dateLabel' => '9 Tháng Tư, 2024',
      'dateLink' => eai_rc_map_link(['url' => home_url('/')]),
      'term' => [
        'text' => 'Tác giả mẫu',
        'link' => eai_rc_map_link(['url' => home_url('/category/tac-gia-mau/')]),
      ],
      'termPrefix' => eai_entry_post_date_get_term_prefix($settings),
    ];

    $class_name = trim((string) ($settings['class_name'] ?? ''));
    if ($class_name !== '') {
      $props['className'] = $class_name;
    }

    return $props;
  }
}

if (! function_exists('eai_entry_post_date_props_are_empty')) {
  /**
   * @param array<string, mixed> $props
   */
  function eai_entry_post_date_props_are_empty(array $props): bool
  {
    $date_label = trim((string) ($props['dateLabel'] ?? ''));
    $term = $props['term'] ?? [];
    $term_text = trim((string) (is_array($term) ? ($term['text'] ?? '') : ''));

    return $date_label === '' || $term_text === '';
  }
}
