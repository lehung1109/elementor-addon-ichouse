<?php
if (! defined('ABSPATH')) {
  exit;
}

if (! function_exists('eai_rc_map_director_profile_items')) {
  /**
   * @param array<int, array<string, mixed>> $rows
   * @return array<int, array{title: string, description: string}>
   */
  function eai_rc_map_director_profile_items(array $rows): array
  {
    $mapped = [];

    foreach ($rows as $row) {
      if (! is_array($row)) {
        continue;
      }

      $title = trim((string) ($row['title'] ?? ''));
      $description = trim((string) ($row['description'] ?? ''));

      if ($title === '' && $description === '') {
        continue;
      }

      $mapped[] = [
        'title' => $title,
        'description' => $description,
      ];
    }

    return $mapped;
  }
}

if (! function_exists('eai_director_profile_get_rc_props')) {
  /**
   * @param array<string, mixed> $settings
   * @return array<string, mixed>
   */
  function eai_director_profile_get_rc_props(array $settings): array
  {
    $background_mobile_image = is_array($settings['background_mobile_image'] ?? null)
      ? $settings['background_mobile_image']
      : [];
    $background_desktop_image = is_array($settings['background_desktop_image'] ?? null)
      ? $settings['background_desktop_image']
      : [];
    $background_mobile_resolution = (string) ($settings['background_mobile_image_resolution'] ?? 'large');
    $background_desktop_resolution = (string) ($settings['background_desktop_image_resolution'] ?? 'large');
    $items = is_array($settings['items'] ?? null) ? $settings['items'] : [];
    $target_id = trim((string) ($settings['scroll_reveal_target_id'] ?? 'director-profile'));
    if ($target_id === '') {
      $target_id = 'director-profile';
    }

    $class_name = trim((string) ($settings['class_name'] ?? ''));
    $props = [
      'backgroundMobileImage' => eai_rc_map_media_model(
        $background_mobile_image,
        [],
        null,
        $background_mobile_resolution
      ),
      'backgroundDesktopImage' => eai_rc_map_media_model(
        $background_desktop_image,
        [],
        null,
        $background_desktop_resolution
      ),
      'subtitle' => (string) ($settings['subtitle'] ?? ''),
      'descriptionHtml' => (string) ($settings['description_html'] ?? ''),
      'items' => eai_rc_map_director_profile_items($items),
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
