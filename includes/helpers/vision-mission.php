<?php
if (! defined('ABSPATH')) {
  exit;
}

if (! function_exists('eai_rc_map_vision_mission_items')) {
  /**
   * @param array<int, array<string, mixed>> $rows
   * @return array<int, array{title: string, description: string}>
   */
  function eai_rc_map_vision_mission_items(array $rows): array
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

if (! function_exists('eai_rc_map_vision_mission_columns')) {
  /**
   * @param array<int, array<string, mixed>> $rows
   * @return array<int, array{title: string, items: array<int, array{title: string, description: string}>}>
   */
  function eai_rc_map_vision_mission_columns(array $rows): array
  {
    $mapped = [];

    foreach ($rows as $row) {
      if (! is_array($row)) {
        continue;
      }

      $title = trim((string) ($row['title'] ?? ''));
      $items_raw = is_array($row['items'] ?? null) ? $row['items'] : [];
      $items = eai_rc_map_vision_mission_items($items_raw);

      if ($title === '' && empty($items)) {
        continue;
      }

      $mapped[] = [
        'title' => $title,
        'items' => $items,
      ];
    }

    return $mapped;
  }
}

if (! function_exists('eai_vision_mission_get_target_id')) {
  function eai_vision_mission_get_target_id(string $widget_id): string
  {
    $safe_widget_id = sanitize_html_class($widget_id);

    return 'vision-mission-' . ($safe_widget_id !== '' ? $safe_widget_id : 'widget');
  }
}

if (! function_exists('eai_vision_mission_get_rc_props')) {
  /**
   * @param array<string, mixed> $settings
   * @return array<string, mixed>
   */
  function eai_vision_mission_get_rc_props(array $settings, string $widget_id = ''): array
  {
    $columns = is_array($settings['columns'] ?? null) ? $settings['columns'] : [];
    $class_name = trim((string) ($settings['class_name'] ?? ''));

    $props = [
      'columns' => eai_rc_map_vision_mission_columns($columns),
      'scrollReveal' => [
        'targetId' => eai_vision_mission_get_target_id($widget_id),
      ],
    ];

    if ($class_name !== '') {
      $props['className'] = $class_name;
    }

    return $props;
  }
}

if (! function_exists('eai_vision_mission_get_editor_sample_props')) {
  /**
   * @param array<string, mixed> $settings
   * @return array<string, mixed>
   */
  function eai_vision_mission_get_editor_sample_props(array $settings, string $widget_id = ''): array
  {
    $props = [
      'columns' => [
        [
          'title' => 'TẦM NHÌN',
          'items' => [
            [
              'title' => '2027 - Doanh nghiệp xây dựng dân dụng có vị thế tại Việt Nam',
              'description' => 'Trở thành doanh nghiệp xây dựng dân dụng có vị thế tại Việt Nam, kiến tạo những công trình nhà ở kiểu mẫu, góp phần thay đổi diện mạo đô thị và nâng cao chất lượng sống của khách hàng.',
            ],
            [
              'title' => '2030 - Doanh nghiệp xây dựng dân dụng kiểu mẫu tại Việt Nam',
              'description' => 'Trở thành hình mẫu trong lĩnh vực xây dựng nhà ở dân dụng cao cấp tại Việt Nam - nơi hội tụ đội ngũ chuyên nghiệp, quy trình chuẩn hóa và công trình đạt chuẩn chất lượng cao, góp phần phát triển bền vững ngành xây dựng.',
            ],
          ],
        ],
        [
          'title' => 'SỨ MỆNH',
          'items' => [
            [
              'title' => 'Tạo nên những công trình giàu sức sáng tạo',
              'description' => 'ICHOUSE tạo nên những công trình giàu sức sáng tạo mang phong cách kiến trúc đặc sắc, áp dụng các giải pháp kết cấu và công nghệ xây dựng Châu Âu, đảm bảo tính bền vững với thời gian.',
            ],
            [
              'title' => 'Góp phần phát triển xã hội bền vững',
              'description' => 'Ngoài ra, ICHOUSE còn góp phần phát triển xã hội trên cơ sở là tác nhân quan trọng trong xu thế phát triển bền vững ở lĩnh vực này.',
            ],
          ],
        ],
      ],
      'scrollReveal' => [
        'targetId' => eai_vision_mission_get_target_id($widget_id),
      ],
    ];

    $class_name = trim((string) ($settings['class_name'] ?? ''));
    if ($class_name !== '') {
      $props['className'] = $class_name;
    }

    return $props;
  }
}
