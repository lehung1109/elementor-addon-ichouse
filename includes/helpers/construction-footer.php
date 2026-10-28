<?php
if (! defined('ABSPATH')) {
  exit;
}

if (! function_exists('eai_rc_map_construction_footer_menu_items')) {
  /**
   * Map WP nav menu (top-level only) to ConstructionFooter menuItems props.
   *
   * @return array<int, array<string, mixed>>
   */
  function eai_rc_map_construction_footer_menu_items(int $menu_id): array
  {
    if ($menu_id <= 0) {
      return [];
    }

    $tree = eai_get_menu_tree_with_active($menu_id);
    $mapped = [];

    foreach ($tree as $item) {
      if (! is_array($item)) {
        continue;
      }

      $label = trim((string) ($item['label'] ?? ''));
      $href = trim((string) ($item['href'] ?? ''));
      if ($label === '' || $href === '') {
        continue;
      }

      $mapped[] = [
        'label' => $label,
        'link' => eai_rc_map_link([
          'url' => $href,
          'is_external' => false,
          'nofollow' => false,
        ]),
      ];
    }

    return $mapped;
  }
}

if (! function_exists('eai_construction_footer_build_tel_link')) {
  /**
   * Build tel: LinkModel from display phone text (digits and + only).
   *
   * @return array{url: string, is_external: bool, nofollow: bool}
   */
  function eai_construction_footer_build_tel_link(string $phone_text): array
  {
    $tel = preg_replace('/[^\d+]/', '', $phone_text) ?? '';

    return eai_rc_map_link([
      'url' => $tel !== '' ? 'tel:' . $tel : '',
      'is_external' => false,
      'nofollow' => false,
    ]);
  }
}

if (! function_exists('eai_construction_footer_build_mailto_link')) {
  /**
   * Build mailto: LinkModel from display email text.
   *
   * @return array{url: string, is_external: bool, nofollow: bool}
   */
  function eai_construction_footer_build_mailto_link(string $email_text): array
  {
    $email = trim($email_text);

    return eai_rc_map_link([
      'url' => $email !== '' ? 'mailto:' . $email : '',
      'is_external' => false,
      'nofollow' => false,
    ]);
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
    $menu_id = (int) ($settings['menu_id'] ?? 0);
    $phone_text = (string) ($settings['phone_text'] ?? '');
    $email_text = (string) ($settings['email_text'] ?? '');
    $social_links = is_array($settings['social_links'] ?? null) ? $settings['social_links'] : [];
    $addresses = is_array($settings['addresses'] ?? null) ? $settings['addresses'] : [];
    $class_name = trim((string) ($settings['class_name'] ?? ''));

    $props = [
      'logo' => eai_rc_map_media_model($logo, [], null, $logo_resolution),
      'menuItems' => eai_rc_map_construction_footer_menu_items($menu_id),
      'companyName' => (string) ($settings['company_name'] ?? ''),
      'socialLinks' => eai_rc_map_construction_footer_social_links($social_links),
      'phone' => [
        'text' => $phone_text,
        'link' => eai_construction_footer_build_tel_link($phone_text),
      ],
      'addresses' => eai_rc_map_construction_footer_addresses($addresses),
      'email' => [
        'text' => $email_text,
        'link' => eai_construction_footer_build_mailto_link($email_text),
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
