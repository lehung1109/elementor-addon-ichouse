<?php
if (! defined('ABSPATH')) {
  exit;
}

if (! function_exists('eai_rc_map_service_offerings_items')) {
  /**
   * @param array<int, mixed> $rows
   * @return array<int, array<string, mixed>>
   */
  function eai_rc_map_service_offerings_items(array $rows): array
  {
    $mapped = [];

    foreach ($rows as $row) {
      if (! is_array($row)) {
        continue;
      }

      $title = trim((string) ($row['title'] ?? ''));
      if ($title === '') {
        continue;
      }

      $image = is_array($row['image'] ?? null) ? $row['image'] : [];
      $resolution = (string) ($row['image_resolution'] ?? 'large');
      $media = eai_rc_map_media_model($image, [], null, $resolution);

      if (empty($media['url'])) {
        continue;
      }

      $alt_override = trim((string) ($row['alt'] ?? ''));
      if ($alt_override !== '') {
        $media['alt'] = $alt_override;
      }

      $mapped[] = [
        'title' => $title,
        'descriptionHtml' => (string) ($row['description_html'] ?? ''),
        'image' => $media,
      ];
    }

    return $mapped;
  }
}

if (! function_exists('eai_service_offerings_get_target_id')) {
  function eai_service_offerings_get_target_id(string $widget_id): string
  {
    $safe_widget_id = sanitize_html_class($widget_id);

    return 'service-offerings-' . ($safe_widget_id !== '' ? $safe_widget_id : 'widget');
  }
}

if (! function_exists('eai_service_offerings_get_rc_props')) {
  /**
   * @param array<string, mixed> $settings
   * @return array<string, mixed>
   */
  function eai_service_offerings_get_rc_props(array $settings, string $widget_id): array
  {
    $rows = is_array($settings['items'] ?? null) ? $settings['items'] : [];
    $class_name = trim((string) ($settings['class_name'] ?? ''));
    $props = [
      'items' => eai_rc_map_service_offerings_items($rows),
      'scrollReveal' => [
        'targetId' => eai_service_offerings_get_target_id($widget_id),
      ],
    ];

    if ($class_name !== '') {
      $props['className'] = $class_name;
    }

    return $props;
  }
}

if (! function_exists('eai_service_offerings_get_editor_sample_props')) {
  /**
   * Static sample for Elementor canvas (mirrors api-rc src/data/service-offerings.ts).
   *
   * @param array<string, mixed> $settings
   * @return array<string, mixed>
   */
  function eai_service_offerings_get_editor_sample_props(array $settings, string $widget_id): array
  {
    $props = [
      'items' => [
        [
          'title' => 'Thiết kế kiến trúc và nội thất công trình dân dụng',
          'descriptionHtml' => '<ul><li>Thiết kế mặt bằng công năng và phối cảnh 3D ngoại thất, nội thất.</li><li>Triển khai chi tiết hồ sơ thiết kế thi công kiến trúc, kết cấu và điện nước hoàn thiện.</li></ul>',
          'image' => [
            'url' => 'https://placehold.co/450x480/png?text=Thiet+ke',
            'alt' => 'Thiết kế kiến trúc và nội thất',
            'display_dimensions' => ['width' => 450, 'height' => 480],
          ],
        ],
        [
          'title' => 'Thi công xây dựng công trình',
          'descriptionHtml' => '<ul><li>Thi công xây dựng phần thô và Thi công hoàn thiện, lắp đặt trang thiết bị cơ điện.</li><li>Sản xuất, thi công lắp đặt đồ nội thất &amp; các giải pháp thông minh.</li></ul>',
          'image' => [
            'url' => 'https://placehold.co/450x480/png?text=Thi+cong',
            'alt' => 'Thi công xây dựng công trình',
            'display_dimensions' => ['width' => 450, 'height' => 480],
          ],
        ],
      ],
      'scrollReveal' => [
        'targetId' => eai_service_offerings_get_target_id($widget_id),
      ],
    ];

    $class_name = trim((string) ($settings['class_name'] ?? ''));
    if ($class_name !== '') {
      $props['className'] = $class_name;
    }

    return $props;
  }
}
