<?php
if (! defined('ABSPATH')) {
  exit;
}

if (! function_exists('eai_rc_map_featured_projects_items')) {
  /**
   * Map Elementor repeater rows to FeaturedProjectsModel.items.
   *
   * @param array<int, array<string, mixed>> $rows
   * @return array<int, array<string, mixed>>
   */
  function eai_rc_map_featured_projects_items(array $rows): array
  {
    $mapped = [];

    foreach ($rows as $row) {
      if (! is_array($row)) {
        continue;
      }

      $image = is_array($row['image'] ?? null) ? $row['image'] : [];
      $resolution = (string) ($row['image_resolution'] ?? 'large');
      $media = eai_rc_map_media_model($image, [], null, $resolution);
      if (empty($media['url'])) {
        continue;
      }

      $link = is_array($row['link'] ?? null) ? $row['link'] : [];
      if (trim((string) ($link['url'] ?? '')) === '') {
        continue;
      }

      $mapped[] = [
        'image' => $media,
        'title' => (string) ($row['title'] ?? ''),
        'description' => (string) ($row['description'] ?? ''),
        'link' => eai_rc_map_link($link),
      ];
    }

    return $mapped;
  }
}

if (! function_exists('eai_featured_projects_get_rc_props')) {
  /**
   * @param array<string, mixed> $settings
   * @return array<string, mixed>
   */
  function eai_featured_projects_get_rc_props(array $settings): array
  {
    $items = is_array($settings['items'] ?? null) ? $settings['items'] : [];
    $button_link = is_array($settings['button_link'] ?? null) ? $settings['button_link'] : [];
    $class_name = trim((string) ($settings['class_name'] ?? ''));
    $target_id = trim((string) ($settings['scroll_reveal_target_id'] ?? 'featured-projects'));

    $props = [
      'subtitle' => (string) ($settings['subtitle'] ?? ''),
      'title' => (string) ($settings['title'] ?? ''),
      'items' => eai_rc_map_featured_projects_items($items),
      'buttonLabel' => (string) ($settings['button_label'] ?? ''),
      'buttonLink' => eai_rc_map_link($button_link),
      'scrollReveal' => [
        'targetId' => $target_id !== '' ? $target_id : 'featured-projects',
      ],
    ];

    if ($class_name !== '') {
      $props['className'] = $class_name;
    }

    return $props;
  }
}
