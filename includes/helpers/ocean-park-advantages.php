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
    array $fallback_dims = ['width' => 800, 'height' => 450],
    string $fallback_real_url = ''
  ): array {
    $media = is_array($row['image'] ?? null) ? $row['image'] : [];
    $resolution = array_key_exists('image_resolution', $row)
      ? sanitize_key((string) $row['image_resolution'])
      : 'large';
    $model = eai_rc_map_media_model($media, [], null, $resolution === '' ? 'full' : $resolution);
    $url = trim((string) ($model['url'] ?? ''));
    if ($fallback_real_url !== '' && ($url === '' || str_contains($url, 'placehold.co'))) {
      $model['url'] = $fallback_real_url;
    }
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
    $fallback_urls = [
      'https://noithat.ichouse.vn/wp-content/uploads/2026/05/DSC08300-3-HDR-scaled.jpg',
      'https://noithat.ichouse.vn/wp-content/uploads/2026/06/doi-ngu-kts-1024x400.jpg',
      'https://noithat.ichouse.vn/wp-content/uploads/2024/02/1-1.jpg',
    ];

    $mapped = [];
    $idx = 0;
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

      $fallback_url = $fallback_urls[$idx] ?? $fallback_urls[0];
      $idx++;

      $badge = trim((string) ($row['badge'] ?? ''));
      if ($badge === '01 • NHÀ MẪU TRONG ĐẠI ĐÔ THỊ') {
        $badge = '01 • NHÀ MẪU NỘI THẤT TRONG OCEAN PARK';
      } elseif ($badge === '02 • TRỤ SỞ VĂN PHÒNG KẾ CẬN') {
        $badge = '02 • VĂN PHÒNG CÔNG TY GẦN KHU ĐÔ THỊ';
      } elseif ($badge === '03 • NHÀ MÁY SẢN XUẤT SÁT VÁCH') {
        $badge = '03 • NHÀ XƯỞNG 2.000M² GẦN OCEAN PARK';
      }

      if ($title === 'Nhà Mẫu Nội Thất Thực Tế Ngay Tại Vinhomes Ocean Park') {
        $title = 'Nhà Mẫu Nội Thất Thực Tế Nằm Ngay Trong KĐT Vinhomes Ocean Park';
      } elseif ($title === 'Trụ Sở Văn Phòng Kiến Trúc Sư Ngay Cửa Ngõ Đại Đô Thị') {
        $title = 'Trụ Sở Văn Phòng Công Ty & Đội Ngũ KTS Ngay Gần Khu Đô Thị Ocean Park';
      } elseif ($title === 'Nhà Máy Sản Xuất Nội Thất Quy Mô Lớn Sát Ocean Park 1 • 2 • 3') {
        $title = 'Nhà Xưởng / Nhà Máy Sản Xuất Nội Thất 2.000m² Ngay Gần Ocean Park 1 • 2 • 3';
      }

      $mapped[] = [
        'badge' => $badge,
        'title' => $title,
        'address' => $address,
        'distanceNote' => trim((string) ($row['distance_note'] ?? '')),
        'description' => trim((string) ($row['description'] ?? '')),
        'highlightPoints' => $highlight_points,
        'image' => eai_ocean_park_advantages_map_row_media($row, ['width' => 800, 'height' => 450], $fallback_url),
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
    $fallback_urls = [
      'https://noithat.ichouse.vn/wp-content/uploads/2024/02/2-1.jpg',
      'https://noithat.ichouse.vn/wp-content/uploads/2026/05/Du-an-noi-that-can-ho-toa-H2-masteri-OCP-2.jpg',
    ];

    $mapped = [];
    $idx = 0;
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

      $fallback_url = $fallback_urls[$idx] ?? $fallback_urls[0];
      $idx++;

      if ($title === 'Xưởng sản xuất trực tiếp 2.000m² — Dây chuyền CNC & Sơn Inchem khép kín') {
        $title = 'Nhà xưởng sản xuất trực tiếp 2.000m² gần Ocean Park — Dây chuyền CNC & Buồng sơn Inchem khép kín';
      } elseif ($title === 'Thi công thực tế chuẩn Vinhomes & Masterise Homes') {
        $title = 'Kinh nghiệm thực chiến 1.500+ căn hộ chuẩn kỹ thuật Vinhomes & Masterise Homes';
      }

      $mapped[] = [
        'title' => $title,
        'description' => trim((string) ($row['description'] ?? '')),
        'checkpoints' => $checkpoints,
        'image' => eai_ocean_park_advantages_map_row_media($row, ['width' => 800, 'height' => 350], $fallback_url),
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

    $eyebrow = trim((string) ($settings['eyebrow'] ?? ''));
    if ($eyebrow === '3 LỢI THẾ ĐỊA LÝ ĐỘC QUYỀN CỦA IC HOUSE TẠI VINHOMES OCEAN PARK') {
      $eyebrow = '3 LỢI THẾ ĐỊA LÝ ĐỘC QUYỀN CHỈ CÓ TẠI IC HOUSE — PHỤC VỤ TRỰC TIẾP CƯ DÂN VINHOMES OCEAN PARK';
    }

    $title = trim((string) ($settings['title'] ?? ''));
    if ($title === 'Nhà Mẫu Trong Khu Đô Thị • Văn Phòng Kế Cận • Nhà Máy Sản Xuất Ngay Sát Ocean Park') {
      $title = 'Có Nhà Mẫu Trong Ocean Park • Văn Phòng Công Ty Gần Khu Đô Thị • Nhà Xưởng 2.000m² Ngay Sát Ocean Park';
    }

    $description = trim((string) ($settings['description'] ?? ''));
    if (str_starts_with($description, 'Không phải di chuyển xa vào nội thành hay chờ đợi đội thợ từ nơi khác')) {
      $description = 'Không phải di chuyển xa vào nội thành Hà Nội hay lo lắng thuê phải đơn vị thương mại trung gian không có xưởng, cư dân thiết kế & thi công nội thất Vinhomes Ocean Park 1, Ocean Park 2 và Ocean Park 3 được IC HOUSE phục vụ trực tiếp bằng hệ sinh thái 3 cơ sở hiện hữu ngay tại địa phương: Nhà mẫu trong KĐT + Văn phòng KTS gần KĐT + Nhà xưởng sản xuất sát vách đại đô thị.';
    }

    $props = [
      'eyebrow' => $eyebrow,
      'title' => $title,
      'description' => $description,
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
      'eyebrow' => trim((string) ($settings['eyebrow'] ?? '')) ?: '3 LỢI THẾ ĐỊA LÝ ĐỘC QUYỀN CHỈ CÓ TẠI IC HOUSE — PHỤC VỤ TRỰC TIẾP CƯ DÂN VINHOMES OCEAN PARK',
      'title' => trim((string) ($settings['title'] ?? '')) ?: 'Có Nhà Mẫu Trong Ocean Park • Văn Phòng Công Ty Gần Khu Đô Thị • Nhà Xưởng 2.000m² Ngay Sát Ocean Park',
      'description' => trim((string) ($settings['description'] ?? '')) ?: 'Không phải di chuyển xa vào nội thành Hà Nội hay lo lắng thuê phải đơn vị thương mại trung gian không có xưởng, cư dân thiết kế & thi công nội thất Vinhomes Ocean Park 1, Ocean Park 2 và Ocean Park 3 được IC HOUSE phục vụ trực tiếp bằng hệ sinh thái 3 cơ sở hiện hữu ngay tại địa phương.',
      'localStrengths' => [
        [
          'badge' => '01 • NHÀ MẪU NỘI THẤT TRONG OCEAN PARK',
          'title' => 'Nhà Mẫu Nội Thất Thực Tế Nằm Ngay Trong KĐT Vinhomes Ocean Park',
          'address' => 'Sao Biển 23, KĐT Vinhomes Ocean Park, Gia Lâm, Hà Nội',
          'distanceNote' => 'Chỉ 3 phút di chuyển nội khu',
          'description' => 'Gia chủ có thể ghé ngay Nhà mẫu IC HOUSE tại Sao Biển 23 trong lòng khu đô thị để mắt thấy – tay chạm từng chi tiết hoàn thiện thực tế.',
          'highlightPoints' => [
            'Trải nghiệm trực tiếp chất lượng tủ bếp cánh kính Anode, gỗ An Cường, sơn Inchem và đá Vicostone.',
            'Kiểm chứng độ êm của phụ kiện Blum/Hafele và chi tiết bo cạnh, chỉ nẹp tinh xảo.',
          ],
          'image' => [
            'url' => 'https://noithat.ichouse.vn/wp-content/uploads/2026/05/DSC08300-3-HDR-scaled.jpg',
            'alt' => 'Nhà mẫu nội thất IC HOUSE tại Sao Biển 23, KĐT Vinhomes Ocean Park, Gia Lâm, Hà Nội',
            'display_dimensions' => ['width' => 800, 'height' => 450],
          ],
        ],
        [
          'badge' => '02 • VĂN PHÒNG CÔNG TY GẦN KHU ĐÔ THỊ',
          'title' => 'Trụ Sở Văn Phòng Công Ty & Đội Ngũ KTS Ngay Gần Khu Đô Thị Ocean Park',
          'address' => 'Số 01 Voi Phục, Gia Lâm, Hà Nội',
          'distanceNote' => '5 phút tới các tòa căn hộ',
          'description' => 'Đội ngũ 30+ Kiến trúc sư trưởng và Kỹ sư giám sát làm việc thường trực ngay tại Gia Lâm, có mặt tại tòa chỉ sau 15 phút.',
          'highlightPoints' => [
            'Khảo sát laser hiện trạng căn hộ miễn phí ngay trong ngày nhận bàn giao nhà.',
            'Phản ứng bảo hành – bảo trì siêu tốc trong vòng 2 – 24 giờ ngay tại địa phương.',
          ],
          'image' => [
            'url' => 'https://noithat.ichouse.vn/wp-content/uploads/2026/06/doi-ngu-kts-1024x400.jpg',
            'alt' => 'Văn phòng công ty nội thất IC HOUSE tại Số 01 Voi Phục, Gia Lâm, Hà Nội',
            'display_dimensions' => ['width' => 800, 'height' => 450],
          ],
        ],
        [
          'badge' => '03 • NHÀ XƯỞNG 2.000M² GẦN OCEAN PARK',
          'title' => 'Nhà Xưởng / Nhà Máy Sản Xuất Nội Thất 2.000m² Ngay Gần Ocean Park 1 • 2 • 3',
          'address' => 'Thôn Ngọc Trai, Xã Như Quỳnh, Tỉnh Hưng Yên',
          'distanceNote' => '10 phút vận chuyển tới chân tòa',
          'description' => 'Nhà xưởng sản xuất trực tiếp rộng 2.000m² đặt ngay tại Ngọc Trai – Như Quỳnh sát vách Vinhomes Ocean Park 1, 2, 3.',
          'highlightPoints' => [
            'Gia chủ được mời tới xưởng nghiệm thu cốt gỗ MDF lõi xanh An Cường & phôi mộc trước khi sơn phủ.',
            'Giá gốc trực tiếp từ nhà xưởng sản xuất IC HOUSE — tiết kiệm 15–20%.',
          ],
          'image' => [
            'url' => 'https://noithat.ichouse.vn/wp-content/uploads/2024/02/1-1.jpg',
            'alt' => 'Nhà xưởng sản xuất nội thất IC HOUSE tại Thôn Ngọc Trai, Xã Như Quỳnh',
            'display_dimensions' => ['width' => 800, 'height' => 450],
          ],
        ],
      ],
      'capabilityPillars' => [
        [
          'title' => 'Nhà xưởng sản xuất trực tiếp 2.000m² gần Ocean Park — Dây chuyền CNC & Buồng sơn Inchem khép kín',
          'description' => 'Hệ thống máy cắt CNC trung tâm, máy dán cạnh tự động 8 chức năng không đường line và phòng sơn Inchem tiêu chuẩn.',
          'checkpoints' => [
            'Kiểm soát 100% nguồn gốc cốt gỗ An Cường, Plywood phủ Veneer tự nhiên và đá Vicostone.',
            'Độ chính xác kích thước đến từng milimet, lắp đặt khít tuyệt đối với tường và trần căn hộ.',
          ],
          'image' => [
            'url' => 'https://noithat.ichouse.vn/wp-content/uploads/2024/02/2-1.jpg',
            'alt' => 'Nhà xưởng sản xuất trực tiếp 2.000m² IC HOUSE gần Ocean Park',
            'display_dimensions' => ['width' => 800, 'height' => 350],
          ],
        ],
        [
          'title' => 'Kinh nghiệm thực chiến 1.500+ căn hộ chuẩn kỹ thuật Vinhomes & Masterise Homes',
          'description' => 'Am hiểu sâu sắc quy định thi công, hệ thống kỹ thuật MEP, điều hòa âm trần và kết cấu tường chịu lực.',
          'checkpoints' => [
            'Bao trọn thủ tục xin cấp phép thi công, bảo lãnh và nghiệm thu với Ban quản lý tòa nhà.',
            'Thi công sạch sẽ, bọc lót thang máy – hành lang đúng quy chuẩn.',
          ],
          'image' => [
            'url' => 'https://noithat.ichouse.vn/wp-content/uploads/2026/05/Du-an-noi-that-can-ho-toa-H2-masteri-OCP-2.jpg',
            'alt' => 'Thi công thực tế chuẩn Vinhomes & Masterise Homes',
            'display_dimensions' => ['width' => 800, 'height' => 350],
          ],
        ],
      ],
      'proofBanner' => [
        'title' => trim((string) ($settings['proof_banner_title'] ?? '')) ?: 'Đặt lịch tham quan Nhà mẫu trong Ocean Park (Sao Biển 23), Văn phòng Voi Phục & Nhà xưởng Như Quỳnh',
        'description' => trim((string) ($settings['proof_banner_description'] ?? '')) ?: 'IC HOUSE có xe ô tô đưa đón miễn phí quý cư dân từ tất cả các tòa căn hộ Vinhomes Ocean Park 1, 2, 3 sang tham quan trực tiếp Nhà mẫu nội thất và Nhà xưởng sản xuất 2.000m².',
        'buttonLabel' => trim((string) ($settings['proof_banner_button_label'] ?? '')) ?: 'Đặt lịch tham quan Nhà mẫu & Nhà xưởng',
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
