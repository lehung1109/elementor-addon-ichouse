<?php
if (! defined('ABSPATH')) {
  exit;
}

if (! function_exists('eai_rc_map_outstanding_advantages_media')) {
  /**
   * @param array<string, mixed> $row
   * @return array<string, mixed>
   */
  function eai_rc_map_outstanding_advantages_media(
    array $row,
    string $field,
    string $default_size = 'large'
  ): array {
    $media = is_array($row[$field] ?? null) ? $row[$field] : [];
    $resolution = (string) ($row[$field . '_resolution'] ?? $default_size);
    $model = eai_rc_map_media_model($media, [], null, $resolution);
    $alt_field = str_ends_with($field, '_image')
      ? substr($field, 0, -6) . '_alt'
      : $field . '_alt';
    $alt_override = trim((string) ($row[$alt_field] ?? ''));

    if ($alt_override !== '') {
      $model['alt'] = $alt_override;
    }

    return $model;
  }
}

if (! function_exists('eai_rc_map_outstanding_advantages_items')) {
  /**
   * @param array<int, mixed> $rows
   * @return array<int, array<string, mixed>>
   */
  function eai_rc_map_outstanding_advantages_items(array $rows): array
  {
    $mapped = [];

    foreach ($rows as $row) {
      if (! is_array($row)) {
        continue;
      }

      $title = trim((string) ($row['title'] ?? ''));
      $background_mobile = eai_rc_map_outstanding_advantages_media(
        $row,
        'background_mobile_image'
      );
      $background_desktop = eai_rc_map_outstanding_advantages_media(
        $row,
        'background_desktop_image'
      );

      if (
        $title === '' ||
        (empty($background_mobile['url']) && empty($background_desktop['url']))
      ) {
        continue;
      }

      $mapped[] = [
        'backgroundMobileImage' => $background_mobile,
        'backgroundDesktopImage' => $background_desktop,
        'image' => eai_rc_map_outstanding_advantages_media($row, 'image', 'medium'),
        'subtitle' => trim((string) ($row['subtitle'] ?? '')),
        'title' => $title,
        'description' => trim((string) ($row['description'] ?? '')),
      ];
    }

    return $mapped;
  }
}

if (! function_exists('eai_outstanding_advantages_get_target_id')) {
  function eai_outstanding_advantages_get_target_id(string $widget_id): string
  {
    $safe_widget_id = sanitize_html_class($widget_id);

    return 'outstanding-advantages-' . ($safe_widget_id !== '' ? $safe_widget_id : 'widget');
  }
}

if (! function_exists('eai_outstanding_advantages_get_rc_props')) {
  /**
   * @param array<string, mixed> $settings
   * @return array<string, mixed>
   */
  function eai_outstanding_advantages_get_rc_props(array $settings, string $widget_id): array
  {
    $rows = is_array($settings['items'] ?? null) ? $settings['items'] : [];
    $class_name = trim((string) ($settings['class_name'] ?? ''));
    $props = [
      'items' => eai_rc_map_outstanding_advantages_items($rows),
      'scrollReveal' => [
        'targetId' => eai_outstanding_advantages_get_target_id($widget_id),
      ],
    ];

    if ($class_name !== '') {
      $props['className'] = $class_name;
    }

    return $props;
  }
}

if (! function_exists('eai_outstanding_advantages_get_editor_sample_props')) {
  /**
   * @param array<string, mixed> $settings
   * @return array<string, mixed>
   */
  function eai_outstanding_advantages_get_editor_sample_props(
    array $settings,
    string $widget_id
  ): array {
    $items = [
      [
        'backgroundMobileImage' => [
          'url' => 'https://placehold.co/384x480/1a2b4a/png?text=BG+Mobile+1',
          'alt' => '',
          'display_dimensions' => ['width' => 384, 'height' => 480],
        ],
        'backgroundDesktopImage' => [
          'url' => 'https://placehold.co/384x480/1a2b4a/png?text=BG+Desktop+1',
          'alt' => '',
          'display_dimensions' => ['width' => 384, 'height' => 480],
        ],
        'image' => [
          'url' => 'https://placehold.co/229x137/png?text=Top+1',
          'alt' => 'Chuyên gia giàu kinh nghiệm',
          'display_dimensions' => ['width' => 229, 'height' => 137],
        ],
        'subtitle' => 'Ưu điểm vượt trội',
        'title' => 'Chuyên gia giàu kinh nghiệm',
        'description' => 'Đội ngũ ICHOUSE gồm các kiến trúc sư, kỹ sư và chuyên gia nội thất giàu kinh nghiệm, đồng hành cùng khách hàng từ tư vấn đến hoàn thiện công trình.',
      ],
      [
        'backgroundMobileImage' => [
          'url' => 'https://placehold.co/384x480/243b55/png?text=BG+Mobile+2',
          'alt' => '',
          'display_dimensions' => ['width' => 384, 'height' => 480],
        ],
        'backgroundDesktopImage' => [
          'url' => 'https://placehold.co/384x480/243b55/png?text=BG+Desktop+2',
          'alt' => '',
          'display_dimensions' => ['width' => 384, 'height' => 480],
        ],
        'image' => [
          'url' => 'https://placehold.co/229x137/png?text=Top+2',
          'alt' => 'Quy trình làm việc khoa học',
          'display_dimensions' => ['width' => 229, 'height' => 137],
        ],
        'subtitle' => 'Ưu điểm vượt trội',
        'title' => 'Quy trình làm việc khoa học',
        'description' => 'ICHOUSE có quy trình làm việc khoa học, rõ ràng từng giai đoạn — từ khảo sát, thiết kế, thi công đến bàn giao và bảo hành — đảm bảo tiến độ và chất lượng.',
      ],
      [
        'backgroundMobileImage' => [
          'url' => 'https://placehold.co/384x480/2c3e50/png?text=BG+Mobile+3',
          'alt' => '',
          'display_dimensions' => ['width' => 384, 'height' => 480],
        ],
        'backgroundDesktopImage' => [
          'url' => 'https://placehold.co/384x480/2c3e50/png?text=BG+Desktop+3',
          'alt' => '',
          'display_dimensions' => ['width' => 384, 'height' => 480],
        ],
        'image' => [
          'url' => 'https://placehold.co/229x137/png?text=Top+3',
          'alt' => 'Bảo hành, bảo trì tới 10 năm',
          'display_dimensions' => ['width' => 229, 'height' => 137],
        ],
        'subtitle' => 'Ưu điểm vượt trội',
        'title' => 'Bảo hành, bảo trì tới 10 năm',
        'description' => 'Cam kết bảo hành và bảo trì dài hạn tới 10 năm, hỗ trợ khách hàng yên tâm sử dụng và giữ công trình bền đẹp theo thời gian.',
      ],
    ];

    $class_name = trim((string) ($settings['class_name'] ?? ''));
    $props = [
      'items' => $items,
      'scrollReveal' => [
        'targetId' => eai_outstanding_advantages_get_target_id($widget_id),
      ],
    ];

    if ($class_name !== '') {
      $props['className'] = $class_name;
    }

    return $props;
  }
}
