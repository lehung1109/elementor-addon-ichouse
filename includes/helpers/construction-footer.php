<?php
if (! defined('ABSPATH')) {
  exit;
}

if (! function_exists('eai_rc_map_construction_footer_menu_items')) {
  /**
   * @param array<int, array<string, mixed>> $rows
   * @return array<int, array<string, mixed>>
   */
  function eai_rc_map_construction_footer_menu_items(array $rows): array
  {
    $mapped = [];

    foreach ($rows as $row) {
      if (! is_array($row)) {
        continue;
      }

      $label = trim((string) ($row['label'] ?? ''));
      $link = is_array($row['link'] ?? null) ? $row['link'] : [];
      if ($label === '' || trim((string) ($link['url'] ?? '')) === '') {
        continue;
      }

      $mapped[] = [
        'label' => $label,
        'link' => eai_rc_map_link($link),
      ];
    }

    return $mapped;
  }
}

if (! function_exists('eai_rc_map_construction_footer_social_links')) {
  /**
   * @param array<int, array<string, mixed>> $rows
   * @return array<int, array<string, mixed>>
   */
  function eai_rc_map_construction_footer_social_links(array $rows): array
  {
    $mapped = [];

    foreach ($rows as $row) {
      if (! is_array($row)) {
        continue;
      }

      $aria_label = trim((string) ($row['aria_label'] ?? ''));
      $icon = is_array($row['icon'] ?? null) ? $row['icon'] : [];
      $resolution = (string) ($row['icon_resolution'] ?? 'thumbnail');
      $media = eai_rc_map_media_model($icon, [], null, $resolution);
      $link = is_array($row['link'] ?? null) ? $row['link'] : [];

      if (
        $aria_label === '' ||
        empty($media['url']) ||
        trim((string) ($link['url'] ?? '')) === ''
      ) {
        continue;
      }

      $mapped[] = [
        'ariaLabel' => $aria_label,
        'icon' => $media,
        'link' => eai_rc_map_link($link),
      ];
    }

    return $mapped;
  }
}

if (! function_exists('eai_rc_map_construction_footer_addresses')) {
  /**
   * @param array<int, array<string, mixed>> $rows
   * @return array<int, string>
   */
  function eai_rc_map_construction_footer_addresses(array $rows): array
  {
    $mapped = [];

    foreach ($rows as $row) {
      if (! is_array($row)) {
        continue;
      }

      $line = trim((string) ($row['line'] ?? ''));
      if ($line === '') {
        continue;
      }

      $mapped[] = $line;
    }

    return $mapped;
  }
}

if (! function_exists('eai_construction_footer_get_rc_props')) {
  /**
   * @param array<string, mixed> $settings
   * @return array<string, mixed>
   */
  function eai_construction_footer_get_rc_props(array $settings): array
  {
    $logo = is_array($settings['logo'] ?? null) ? $settings['logo'] : [];
    $logo_resolution = (string) ($settings['logo_resolution'] ?? 'medium');
    $badge = is_array($settings['badge'] ?? null) ? $settings['badge'] : [];
    $badge_resolution = (string) ($settings['badge_resolution'] ?? 'medium');
    $phone_link = is_array($settings['phone_link'] ?? null) ? $settings['phone_link'] : [];
    $email_link = is_array($settings['email_link'] ?? null) ? $settings['email_link'] : [];
    $menu_items = is_array($settings['menu_items'] ?? null) ? $settings['menu_items'] : [];
    $social_links = is_array($settings['social_links'] ?? null) ? $settings['social_links'] : [];
    $addresses = is_array($settings['addresses'] ?? null) ? $settings['addresses'] : [];
    $class_name = trim((string) ($settings['class_name'] ?? ''));

    $props = [
      'logo' => eai_rc_map_media_model($logo, [], null, $logo_resolution),
      'menuItems' => eai_rc_map_construction_footer_menu_items($menu_items),
      'companyName' => (string) ($settings['company_name'] ?? ''),
      'socialLinks' => eai_rc_map_construction_footer_social_links($social_links),
      'phone' => [
        'text' => (string) ($settings['phone_text'] ?? ''),
        'link' => eai_rc_map_link($phone_link),
      ],
      'addresses' => eai_rc_map_construction_footer_addresses($addresses),
      'email' => [
        'text' => (string) ($settings['email_text'] ?? ''),
        'link' => eai_rc_map_link($email_link),
      ],
      'copyright' => (string) ($settings['copyright'] ?? ''),
    ];

    $badge_media = eai_rc_map_media_model($badge, [], null, $badge_resolution);
    if (! empty($badge_media['url'])) {
      $props['badge'] = $badge_media;
    }

    if ($class_name !== '') {
      $props['className'] = $class_name;
    }

    return $props;
  }
}
