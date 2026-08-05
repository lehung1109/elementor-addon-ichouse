<?php
if (! defined('ABSPATH')) {
  exit;
}

if (! function_exists('eai_fields_of_activity_get_rc_props')) {
  /**
   * Map Elementor settings to FieldsOfActivityModel props.
   *
   * @param array<string, mixed> $settings
   * @return array<string, mixed>
   */
  function eai_fields_of_activity_get_rc_props(array $settings): array
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

    $images = [];
    foreach ([1, 2] as $index) {
      $image_key = 'image_' . $index;
      $resolution_key = 'image_' . $index . '_resolution';
      $image = is_array($settings[$image_key] ?? null) ? $settings[$image_key] : [];
      $resolution = (string) ($settings[$resolution_key] ?? 'large');
      $media = eai_rc_map_media_model($image, [], null, $resolution);
      if (! empty($media['url'])) {
        $images[] = $media;
      }
    }

    $button_link = is_array($settings['button_link'] ?? null) ? $settings['button_link'] : [];
    $class_name = trim((string) ($settings['class_name'] ?? ''));
    $target_id = trim((string) ($settings['scroll_reveal_target_id'] ?? 'fields-of-activity'));
    if ($target_id === '') {
      $target_id = 'fields-of-activity';
    }

    $props = [
      'title' => (string) ($settings['title'] ?? ''),
      'items' => $items,
      'images' => $images,
      'buttonLabel' => (string) ($settings['button_label'] ?? ''),
      'buttonLink' => eai_rc_map_link($button_link),
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
