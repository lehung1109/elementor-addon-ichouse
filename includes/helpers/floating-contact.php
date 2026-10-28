<?php
if (! defined('ABSPATH')) {
  exit;
}

if (! function_exists('eai_floating_contact_get_rc_props')) {
  /**
   * @param array<string, mixed> $settings
   * @return array<string, mixed>
   */
  function eai_floating_contact_get_rc_props(array $settings): array
  {
    $messenger_icon = is_array($settings['messenger_icon'] ?? null)
      ? $settings['messenger_icon']
      : [];
    $messenger_link = is_array($settings['messenger_link'] ?? null)
      ? $settings['messenger_link']
      : [];
    $zalo_icon = is_array($settings['zalo_icon'] ?? null)
      ? $settings['zalo_icon']
      : [];
    $zalo_link = is_array($settings['zalo_link'] ?? null)
      ? $settings['zalo_link']
      : [];
    $phone_link = is_array($settings['phone_link'] ?? null)
      ? $settings['phone_link']
      : [];

    $icon_resolution = (string) ($settings['icon_resolution'] ?? 'thumbnail');

    $props = [
      'messenger' => [
        'label' => (string) ($settings['messenger_label'] ?? ''),
        'icon' => eai_rc_map_media_model($messenger_icon, [], null, $icon_resolution),
        'link' => eai_rc_map_link($messenger_link),
      ],
      'zalo' => [
        'label' => (string) ($settings['zalo_label'] ?? ''),
        'icon' => eai_rc_map_media_model($zalo_icon, [], null, $icon_resolution),
        'link' => eai_rc_map_link($zalo_link),
      ],
      'phone' => [
        'label' => (string) ($settings['phone_label'] ?? ''),
        'link' => eai_rc_map_link($phone_link),
      ],
    ];

    $class_name = trim((string) ($settings['class_name'] ?? ''));
    if ($class_name !== '') {
      $props['className'] = $class_name;
    }

    return $props;
  }
}
