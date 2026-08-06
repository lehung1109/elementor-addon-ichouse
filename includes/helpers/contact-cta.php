<?php
if (! defined('ABSPATH')) {
  exit;
}

if (! function_exists('eai_contact_cta_get_rc_props')) {
  /**
   * @param array<string, mixed> $settings
   * @return array<string, mixed>
   */
  function eai_contact_cta_get_rc_props(array $settings): array
  {
    $image = is_array($settings['image'] ?? null) ? $settings['image'] : [];
    $image_resolution = (string) ($settings['image_resolution'] ?? 'large');
    $content_bg = is_array($settings['content_background_image'] ?? null)
      ? $settings['content_background_image']
      : [];
    $content_bg_resolution = (string) ($settings['content_background_image_resolution'] ?? 'large');

    $class_name = trim((string) ($settings['class_name'] ?? ''));
    $popup_target = eai_normalize_contact_popup_key((string) ($settings['popup_target'] ?? ''));

    $props = [
      'subtitle' => (string) ($settings['subtitle'] ?? ''),
      'title' => (string) ($settings['title'] ?? ''),
      'buttonLabel' => (string) ($settings['button_label'] ?? ''),
      'popupTarget' => $popup_target,
      'image' => eai_rc_map_media_model($image, [], null, $image_resolution),
    ];

    $content_bg_model = eai_rc_map_media_model($content_bg, [], null, $content_bg_resolution);
    $content_bg_url = trim((string) ($content_bg_model['url'] ?? ''));
    if ($content_bg_url !== '') {
      $props['contentBackgroundImage'] = $content_bg_model;
    }

    if ($class_name !== '') {
      $props['className'] = $class_name;
    }

    return $props;
  }
}
