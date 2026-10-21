<?php
if (! defined('ABSPATH')) {
  exit;
}

if (! function_exists('eai_ocean_park_advantages_map_row_media')) {
  /**
   * @param array<string, mixed> $row
   * @param array{width: int, height: int} $fallback_dims
   * @return array<string, mixed>
   */
  function eai_ocean_park_advantages_map_row_media(
    array $row,
    array $fallback_dims = ['width' => 800, 'height' => 450]
  ): array {
    $media = is_array($row['image'] ?? null) ? $row['image'] : [];
    $resolution = array_key_exists('image_resolution', $row)
      ? sanitize_key((string) $row['image_resolution'])
      : 'large';
    $model = eai_rc_map_media_model($media, [], null, $resolution === '' ? 'full' : $resolution);
    $alt_override = trim((string) ($row['image_alt'] ?? ''));
    if ($alt_override !== '') {
      $model['alt'] = $alt_override;
    }
    if (
      ! empty($model['url']) &&
      ((int) ($model['display_dimensions']['width'] ?? 0) <= 0 ||
        (int) ($model['display_dimensions']['height'] ?? 0) <= 0)
    ) {
      $model['display_dimensions'] = $fallback_dims;
    }

    return $model;
  }
}

if (! function_exists('eai_ocean_park_advantages_map_local_strengths')) {
  /**
   * @param array<int, mixed> $rows
   * @return array<int, array<string, mixed>>
   */
  function eai_ocean_park_advantages_map_local_strengths(array $rows): array
  {
    $mapped = [];
    foreach ($rows as $row) {
      if (! is_array($row)) {
        continue;
      }
      $title = trim((string) ($row['title'] ?? ''));
      $address = trim((string) ($row['address'] ?? ''));
      if ($title === '' || $address === '') {
        continue;
      }

      $raw_points = preg_split('/\R/u', (string) ($row['highlight_points'] ?? '')) ?: [];
      $highlight_points = array_values(array_filter(array_map(
        static fn($line): string => trim((string) $line),
        $raw_points
      )));

      $mapped[] = [
        'badge' => trim((string) ($row['badge'] ?? '')),
        'title' => $title,
        'address' => $address,
        'distanceNote' => trim((string) ($row['distance_note'] ?? '')),
        'description' => trim((string) ($row['description'] ?? '')),
        'highlightPoints' => $highlight_points,
        'image' => eai_ocean_park_advantages_map_row_media($row, ['width' => 800, 'height' => 450]),
      ];
    }

    return $mapped;
  }
}

if (! function_exists('eai_ocean_park_advantages_map_capability_pillars')) {
  /**
   * @param array<int, mixed> $rows
   * @return array<int, array<string, mixed>>
   */
  function eai_ocean_park_advantages_map_capability_pillars(array $rows): array
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

      $raw_points = preg_split('/\R/u', (string) ($row['checkpoints'] ?? '')) ?: [];
      $checkpoints = array_values(array_filter(array_map(
        static fn($line): string => trim((string) $line),
        $raw_points
      )));

      $mapped[] = [
        'title' => $title,
        'description' => trim((string) ($row['description'] ?? '')),
        'checkpoints' => $checkpoints,
        'image' => eai_ocean_park_advantages_map_row_media($row, ['width' => 800, 'height' => 350]),
      ];
    }

    return $mapped;
  }
}

if (! function_exists('eai_ocean_park_advantages_get_rc_props')) {
  /**
   * @param array<string, mixed> $settings
   * @return array<string, mixed>
   */
  function eai_ocean_park_advantages_get_rc_props(array $settings): array
  {
    $proof_link = is_array($settings['proof_banner_button_link'] ?? null)
      ? $settings['proof_banner_button_link']
      : [];

    $props = [
      'eyebrow' => trim((string) ($settings['eyebrow'] ?? '')),
      'title' => trim((string) ($settings['title'] ?? '')),
      'description' => trim((string) ($settings['description'] ?? '')),
      'localStrengths' => eai_ocean_park_advantages_map_local_strengths((array) ($settings['local_strengths'] ?? [])),
      'capabilityPillars' => eai_ocean_park_advantages_map_capability_pillars((array) ($settings['capability_pillars'] ?? [])),
      'proofBanner' => [
        'title' => trim((string) ($settings['proof_banner_title'] ?? '')),
        'description' => trim((string) ($settings['proof_banner_description'] ?? '')),
        'buttonLabel' => trim((string) ($settings['proof_banner_button_label'] ?? '')),
        'buttonLink' => eai_rc_map_link($proof_link),
      ],
    ];

    $class_name = trim((string) ($settings['class_name'] ?? ''));
    if ($class_name !== '') {
      $props['className'] = $class_name;
    }

    return $props;
  }
}

if (! function_exists('eai_ocean_park_advantages_props_are_empty')) {
  /**
   * @param array<string, mixed> $props
   */
  function eai_ocean_park_advantages_props_are_empty(array $props): bool
  {
    return empty($props['localStrengths']) && empty($props['capabilityPillars']);
  }
}

if (! function_exists('eai_ocean_park_advantages_get_editor_sample_props')) {
  /**
   * @param array<string, mixed> $settings
   * @return array<string, mixed>
   */
  function eai_ocean_park_advantages_get_editor_sample_props(array $settings): array
  {
    $props = [
      'eyebrow' => trim((string) ($settings['eyebrow'] ?? '')) ?: '3 LỢI THẾ ĐỊA LÝ ĐỘC QUYỀN CỦA IC HOUSE TẠI VINHOMES OCEAN PARK',
      'title' => trim((string) ($settings['title'] ?? '')) ?: 'Nhà Mẫu Trong Khu Đô Thị • Văn Phòng Kế Cận • Nhà Máy Sản Xuất Ngay Sát Ocean Park',
      'description' => trim((string) ($settings['description'] ?? '')) ?: 'Không phải di chuyển xa vào nội thành hay chờ đợi đội thợ từ nơi khác, cư dân Ocean Park 1, Ocean Park 2 và Ocean Park 3 được IC HOUSE phục vụ trực tiếp bằng hệ sinh thái Nhà mẫu – Văn phòng KTS – Nhà máy sản xuất bao quanh đại đô thị.',
      'localStrengths' => [
        [
          'badge' => '01 • NHÀ MẪU TRONG ĐẠI ĐÔ THỊ',
          'title' => 'Nhà Mẫu Nội Thất Thực Tế Ngay Tại Vinhomes Ocean Park',
          'address' => 'Sao Biển 23, KĐT Vinhomes Ocean Park, Gia Lâm, Hà Nội',
          'distanceNote' => 'Chỉ 3 phút di chuyển nội khu',
          'description' => 'Gia chủ có thể ghé ngay Sao Biển 23 để chạm tận tay từng đường nét hoàn thiện.',
          'highlightPoints' => [
            'Trải nghiệm trực tiếp chất lượng tủ bếp cánh kính Anode, gỗ An Cường, sơn Inchem và đá Vicostone.',
            'Kiểm chứng độ êm của phụ kiện Blum/Hafele và chi tiết bo cạnh, chỉ nẹp tinh xảo.',
          ],
          'image' => [
            'url' => 'https://placehold.co/800x450/152243/f5a623/png?text=Nha+Mau+Sao+Bien+23',
            'alt' => 'Nhà mẫu nội thất IC HOUSE tại Sao Biển 23',
            'display_dimensions' => ['width' => 800, 'height' => 450],
          ],
        ],
        [
          'badge' => '02 • TRỤ SỞ VĂN PHÒNG KẾ CẬN',
          'title' => 'Trụ Sở Văn Phòng Kiến Trúc Sư Ngay Cửa Ngõ Đại Đô Thị',
          'address' => 'Số 01 Voi Phục, Gia Lâm, Hà Nội',
          'distanceNote' => '5 phút tới các tòa căn hộ',
          'description' => 'Đội ngũ Kiến trúc sư trưởng và Kỹ sư giám sát làm việc thường trực ngay tại Gia Lâm.',
          'highlightPoints' => [
            'Khảo sát laser hiện trạng căn hộ miễn phí ngay trong ngày nhận bàn giao nhà.',
            'Phản ứng bảo hành – bảo trì siêu tốc trong vòng 24 giờ ngay tại địa phương.',
          ],
          'image' => [
            'url' => 'https://placehold.co/800x450/152243/f5a623/png?text=Van+Phong+Voi+Phuc',
            'alt' => 'Trụ sở văn phòng IC HOUSE tại Số 01 Voi Phục',
            'display_dimensions' => ['width' => 800, 'height' => 450],
          ],
        ],
        [
          'badge' => '03 • NHÀ MÁY SẢN XUẤT SÁT VÁCH',
          'title' => 'Nhà Máy Sản Xuất Nội Thất Quy Mô Lớn Sát Ocean Park 1 • 2 • 3',
          'address' => 'Thôn Ngọc Trai, Xã Như Quỳnh, Tỉnh Hưng Yên',
          'distanceNote' => '10 phút vận chuyển tới chân tòa',
          'description' => 'Nhà máy sản xuất trực tiếp rộng 2.000m² đặt ngay tại Ngọc Trai – Như Quỳnh.',
          'highlightPoints' => [
            'Gia chủ được mời tới xưởng nghiệm thu cốt gỗ MDF lõi xanh An Cường & phôi mộc trước khi sơn phủ.',
            'Giá gốc trực tiếp từ nhà máy sản xuất IC HOUSE — tiết kiệm 15–20%.',
          ],
          'image' => [
            'url' => 'https://placehold.co/800x450/152243/f5a623/png?text=Nha+May+Nhu+Quynh',
            'alt' => 'Nhà máy sản xuất nội thất IC HOUSE tại Như Quỳnh',
            'display_dimensions' => ['width' => 800, 'height' => 450],
          ],
        ],
      ],
      'capabilityPillars' => [
        [
          'title' => 'Xưởng sản xuất trực tiếp 2.000m² — Dây chuyền CNC & Sơn Inchem khép kín',
          'description' => 'Hệ thống máy cắt CNC trung tâm, máy dán cạnh tự động 8 chức năng không đường line và phòng sơn Inchem tiêu chuẩn.',
          'checkpoints' => [
            'Kiểm soát 100% nguồn gốc cốt gỗ An Cường, Plywood phủ Veneer tự nhiên và đá Vicostone.',
            'Độ chính xác kích thước đến từng milimet, lắp đặt khít tuyệt đối với tường và trần căn hộ.',
          ],
          'image' => [
            'url' => 'https://placehold.co/800x350/152243/f5a623/png?text=Day+Chuyen+CNC+2000m2',
            'alt' => 'Xưởng sản xuất trực tiếp 2.000m² IC HOUSE',
            'display_dimensions' => ['width' => 800, 'height' => 350],
          ],
        ],
        [
          'title' => 'Thi công thực tế chuẩn Vinhomes & Masterise Homes',
          'description' => 'Am hiểu sâu sắc quy định thi công, hệ thống kỹ thuật MEP, điều hòa âm trần và kết cấu tường chịu lực.',
          'checkpoints' => [
            'Bao trọn thủ tục xin cấp phép thi công, bảo lãnh và nghiệm thu với Ban quản lý tòa nhà.',
            'Thi công sạch sẽ, bọc lót thang máy – hành lang đúng quy chuẩn.',
          ],
          'image' => [
            'url' => 'https://placehold.co/800x350/152243/f5a623/png?text=Thi+Cong+Chuan+Vinhomes',
            'alt' => 'Thi công thực tế chuẩn Vinhomes & Masterise Homes',
            'display_dimensions' => ['width' => 800, 'height' => 350],
          ],
        ],
      ],
      'proofBanner' => [
        'title' => trim((string) ($settings['proof_banner_title'] ?? '')) ?: 'Đặt lịch tham quan Nhà mẫu Sao Biển 23 & Nhà máy Như Quỳnh ngay hôm nay',
        'description' => trim((string) ($settings['proof_banner_description'] ?? '')) ?: 'IC HOUSE có xe đưa đón miễn phí quý cư dân từ các tòa căn hộ Ocean Park 1, 2, 3 sang tham quan Nhà mẫu và Nhà máy sản xuất.',
        'buttonLabel' => trim((string) ($settings['proof_banner_button_label'] ?? '')) ?: 'Đặt lịch tham quan Nhà mẫu',
        'buttonLink' => eai_rc_map_link(
          is_array($settings['proof_banner_button_link'] ?? null)
            ? $settings['proof_banner_button_link']
            : ['url' => '#dang-ky-tu-van']
        ),
      ],
    ];

    $class_name = trim((string) ($settings['class_name'] ?? ''));
    if ($class_name !== '') {
      $props['className'] = $class_name;
    }

    return $props;
  }
}
