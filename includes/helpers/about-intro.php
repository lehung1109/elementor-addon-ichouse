<?php
if (! defined('ABSPATH')) {
  exit;
}

if (! function_exists('eai_about_intro_get_rc_props')) {
  /**
   * @param array<string, mixed> $settings
   * @return array<string, mixed>
   */
  function eai_about_intro_get_rc_props(array $settings): array
  {
    $background_image = is_array($settings['background_image'] ?? null)
      ? $settings['background_image']
      : [];
    $image = is_array($settings['image'] ?? null) ? $settings['image'] : [];
    $background_resolution = (string) ($settings['background_image_resolution'] ?? 'large');
    $image_resolution = (string) ($settings['image_resolution'] ?? 'large');
    $button_link = is_array($settings['button_link'] ?? null) ? $settings['button_link'] : [];
    $target_id = trim((string) ($settings['scroll_reveal_target_id'] ?? 'about-intro'));
    if ($target_id === '') {
      $target_id = 'about-intro';
    }

    $class_name = trim((string) ($settings['class_name'] ?? ''));
    $props = [
      'backgroundImage' => eai_rc_map_media_model($background_image, [], null, $background_resolution),
      'image' => eai_rc_map_media_model($image, [], null, $image_resolution),
      'subtitle' => (string) ($settings['subtitle'] ?? ''),
      'descriptionHtml' => (string) ($settings['description_html'] ?? ''),
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
