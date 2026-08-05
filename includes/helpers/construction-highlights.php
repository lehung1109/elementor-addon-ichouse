<?php
if (! defined('ABSPATH')) {
  exit;
}

if (! function_exists('eai_construction_highlights_get_rc_props')) {
  /**
   * Map Elementor settings to ConstructionHighlightsModel props.
   *
   * @param array<string, mixed> $settings
   * @return array<string, mixed>
   */
  function eai_construction_highlights_get_rc_props(array $settings): array
  {
    $items_raw = is_array($settings['items'] ?? null) ? $settings['items'] : [];
    $items = [];

    foreach ($items_raw as $row) {
      if (! is_array($row)) {
        continue;
      }

      $title = trim((string) ($row['title'] ?? ''));
      if ($title === '') {
        continue;
      }

      $icon_image = is_array($row['icon_image'] ?? null) ? $row['icon_image'] : [];
      $icon_resolution = (string) ($row['icon_image_resolution'] ?? 'thumbnail');
      $icon_media = eai_rc_map_media_model($icon_image, [], null, $icon_resolution);

      $item = [
        'title' => $title,
        'contentHtml' => (string) ($row['content_html'] ?? ''),
        'defaultOpen' => ($row['default_open'] ?? '') === 'yes',
      ];

      if (! empty($icon_media['url'])) {
        $item['iconImage'] = $icon_media;
      }

      $items[] = $item;
    }

    $image = is_array($settings['image'] ?? null) ? $settings['image'] : [];
    $image_resolution = (string) ($settings['image_resolution'] ?? 'large');
    $image_media = eai_rc_map_media_model($image, [], null, $image_resolution);

    $class_name = trim((string) ($settings['class_name'] ?? ''));
    $target_id = trim((string) ($settings['scroll_reveal_target_id'] ?? 'construction-highlights'));
    if ($target_id === '') {
      $target_id = 'construction-highlights';
    }

    $props = [
      'subtitle' => (string) ($settings['subtitle'] ?? ''),
      'titleHtml' => (string) ($settings['title_html'] ?? ''),
      'items' => $items,
      'image' => $image_media,
      'scrollReveal' => [
        'targetId' => $target_id,
      ],
    ];

    if ($class_name !== '') {
      $props['className'] = $class_name;
    }

    return $props;
  }
}
