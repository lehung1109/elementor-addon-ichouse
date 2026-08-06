<?php
if (! defined('ABSPATH')) {
  exit;
}

if (! function_exists('eai_rc_map_key_personnel_items')) {
  /**
   * Map Elementor repeater rows to KeyPersonnelModel.items.
   *
   * @param array<int, array<string, mixed>> $rows
   * @return array<int, array<string, mixed>>
   */
  function eai_rc_map_key_personnel_items(array $rows): array
  {
    $mapped = [];

    foreach ($rows as $row) {
      if (! is_array($row)) {
        continue;
      }

      $image = is_array($row['image'] ?? null) ? $row['image'] : [];
      $resolution = (string) ($row['image_resolution'] ?? 'large');
      $media = eai_rc_map_media_model($image, [], null, $resolution);
      if (empty($media['url'])) {
        continue;
      }

      $title = trim((string) ($row['title'] ?? ''));
      if ($title === '') {
        continue;
      }

      $item = [
        'image' => $media,
        'title' => $title,
        'descriptionHtml' => (string) ($row['description_html'] ?? ''),
      ];

      $link = is_array($row['link'] ?? null) ? $row['link'] : [];
      $link_label = trim((string) ($row['link_label'] ?? ''));
      $link_url = trim((string) ($link['url'] ?? ''));

      if ($link_url !== '' && $link_label !== '') {
        $item['link'] = eai_rc_map_link($link);
        $item['linkLabel'] = $link_label;
      }

      $mapped[] = $item;
    }

    return $mapped;
  }
}

if (! function_exists('eai_key_personnel_get_rc_props')) {
  /**
   * @param array<string, mixed> $settings
   * @return array<string, mixed>
   */
  function eai_key_personnel_get_rc_props(array $settings): array
  {
    $items = is_array($settings['items'] ?? null) ? $settings['items'] : [];
    $class_name = trim((string) ($settings['class_name'] ?? ''));

    $props = [
      'title' => (string) ($settings['title'] ?? ''),
      'items' => eai_rc_map_key_personnel_items($items),
    ];

    if ($class_name !== '') {
      $props['className'] = $class_name;
    }

    return $props;
  }
}

if (! function_exists('eai_key_personnel_get_editor_sample_props')) {
  /**
   * Static sample for Elementor canvas (mirrors api-rc src/data/key-personnel-wrapper.ts).
   *
   * @param array<string, mixed> $settings
   * @return array<string, mixed>
   */
  function eai_key_personnel_get_editor_sample_props(array $settings): array
  {
    $class_name = trim((string) ($settings['class_name'] ?? ''));
    $title = trim((string) ($settings['title'] ?? ''));

    $props = [
      'title' => $title !== '' ? $title : 'ĐỘI NGŨ NHÂN SỰ CHỦ CHỐT',
      'items' => [
        [
          'image' => [
            'url' => 'https://placehold.co/480x600/png?text=Nga',
            'alt' => 'KS. Lưu Hoàng Nga',
            'display_dimensions' => ['width' => 480, 'height' => 600],
          ],
          'title' => 'KS. Lưu Hoàng Nga',
          'descriptionHtml' => '<ul><li>Tốt nghiệp Đại học Xây dựng Hà Nội</li><li>Chứng chỉ hành nghề giám sát thi công xây dựng công trình</li><li>15 năm kinh nghiệm trong lĩnh vực xây dựng dân dụng</li></ul>',
          'link' => ['url' => '#', 'is_external' => false, 'nofollow' => false],
          'linkLabel' => 'Xem chi tiết',
        ],
        [
          'image' => [
            'url' => 'https://placehold.co/480x600/png?text=Hung',
            'alt' => 'KS. Nguyễn Văn Hùng',
            'display_dimensions' => ['width' => 480, 'height' => 600],
          ],
          'title' => 'KS. Nguyễn Văn Hùng',
          'descriptionHtml' => '<ul><li>Tốt nghiệp Đại học Kiến trúc Hà Nội</li><li>Chứng chỉ hành nghề thiết kế kết cấu công trình</li><li>12 năm kinh nghiệm thiết kế và giám sát thi công</li></ul>',
          'link' => ['url' => '#', 'is_external' => false, 'nofollow' => false],
          'linkLabel' => 'Xem chi tiết',
        ],
        [
          'image' => [
            'url' => 'https://placehold.co/480x600/png?text=Lan',
            'alt' => 'ThS. Phạm Thị Lan',
            'display_dimensions' => ['width' => 480, 'height' => 600],
          ],
          'title' => 'ThS. Phạm Thị Lan',
          'descriptionHtml' => '<ul><li>Thạc sĩ Quản lý xây dựng — Đại học Xây dựng</li><li>Chứng chỉ QS / ước lượng chi phí công trình</li><li>10 năm kinh nghiệm quản lý dự án dân dụng cao cấp</li></ul>',
        ],
      ],
    ];

    if ($class_name !== '') {
      $props['className'] = $class_name;
    }

    return $props;
  }
}
