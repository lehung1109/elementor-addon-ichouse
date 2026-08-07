<?php
if (! defined('ABSPATH')) {
  exit;
}

if (! function_exists('eai_fields_of_activity_get_target_id')) {
  function eai_fields_of_activity_get_target_id(string $widget_id): string
  {
    $safe_widget_id = sanitize_html_class($widget_id);

    return 'fields-of-activity-' . ($safe_widget_id !== '' ? $safe_widget_id : 'widget');
  }
}

if (! function_exists('eai_fields_of_activity_get_rc_props')) {
  /**
   * Map Elementor settings to FieldsOfActivityModel props.
   *
   * @param array<string, mixed> $settings
   * @return array<string, mixed>
   */
  function eai_fields_of_activity_get_rc_props(array $settings, string $widget_id): array
  {
    $items_raw = is_array($settings['items'] ?? null) ? $settings['items'] : [];

    $shared_icon_image = is_array($settings['icon_image'] ?? null) ? $settings['icon_image'] : [];
    $shared_icon_resolution = (string) ($settings['icon_image_resolution'] ?? 'thumbnail');
    $shared_icon = eai_rc_map_media_model($shared_icon_image, [], null, $shared_icon_resolution);

    // Legacy: widgets saved before shared icon used per-item icon_image.
    if (empty($shared_icon['url'])) {
      foreach ($items_raw as $row) {
        if (! is_array($row)) {
          continue;
        }
        $legacy_image = is_array($row['icon_image'] ?? null) ? $row['icon_image'] : [];
        $legacy_resolution = (string) ($row['icon_image_resolution'] ?? 'thumbnail');
        $legacy_icon = eai_rc_map_media_model($legacy_image, [], null, $legacy_resolution);
        if (! empty($legacy_icon['url'])) {
          $shared_icon = $legacy_icon;
          break;
        }
      }
    }

    $items = [];

    foreach ($items_raw as $row) {
      if (! is_array($row)) {
        continue;
      }

      $title = trim((string) ($row['title'] ?? ''));
      if ($title === '') {
        continue;
      }

      $item = [
        'title' => $title,
        'contentHtml' => (string) ($row['content_html'] ?? ''),
        'defaultOpen' => ($row['default_open'] ?? '') === 'yes',
      ];

      if (! empty($shared_icon['url'])) {
        $item['iconImage'] = $shared_icon;
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
    $target_id = eai_fields_of_activity_get_target_id($widget_id);

    $props = [
      'title' => (string) ($settings['title'] ?? ''),
      'items' => $items,
      'images' => $images,
      'buttonLabel' => (string) ($settings['button_label'] ?? ''),
      'buttonLink' => eai_rc_map_link($button_link),
      'checkboxIdPrefix' => $target_id . '-item',
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
