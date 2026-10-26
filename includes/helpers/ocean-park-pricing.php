<?php
if (! defined('ABSPATH')) {
  exit;
}

if (! function_exists('eai_ocean_park_pricing_map_unit_estimates')) {
  /**
   * @param array<int, mixed> $rows
   * @return array<int, array<string, mixed>>
   */
  function eai_ocean_park_pricing_map_unit_estimates(array $rows): array
  {
    $mapped = [];
    foreach ($rows as $row) {
      if (! is_array($row)) {
        continue;
      }
      $unit_type = trim((string) ($row['unit_type'] ?? ''));
      $price_range = trim((string) ($row['price_range'] ?? ''));
      if ($unit_type === '' || $price_range === '') {
        continue;
      }

      $item = [
        'unitType' => $unit_type,
        'areaRange' => trim((string) ($row['area_range'] ?? '')),
        'priceRange' => $price_range,
        'highlight' => ($row['highlight'] ?? '') === 'yes',
      ];
      $target_zones = trim((string) ($row['target_zones'] ?? ''));
      if ($target_zones !== '') {
        $item['targetZones'] = $target_zones;
      }
      $mapped[] = $item;
    }

    return $mapped;
  }
}

if (! function_exists('eai_ocean_park_pricing_map_room_estimates')) {
  /**
   * @param array<int, mixed> $rows
   * @return array<int, array{roomName: string, englishName: string, priceRange: string}>
   */
  function eai_ocean_park_pricing_map_room_estimates(array $rows): array
  {
    $mapped = [];
    foreach ($rows as $row) {
      if (! is_array($row)) {
        continue;
      }
      $room_name = trim((string) ($row['room_name'] ?? ''));
      $price_range = trim((string) ($row['price_range'] ?? ''));
      if ($room_name === '' || $price_range === '') {
        continue;
      }

      $mapped[] = [
        'roomName' => $room_name,
        'englishName' => trim((string) ($row['english_name'] ?? '')),
        'priceRange' => $price_range,
      ];
    }

    return $mapped;
  }
}

if (! function_exists('eai_ocean_park_pricing_map_commitments')) {
  /**
   * @param array<int, mixed> $rows
   * @return array<int, array{title: string, items: array<int, array{label: string, detail: string}>}>
   */
  function eai_ocean_park_pricing_map_commitments(array $rows): array
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

      $items = [];
      if (is_array($row['items'] ?? null)) {
        foreach ($row['items'] as $sub) {
          if (! is_array($sub)) {
            continue;
          }
          $label = trim((string) ($sub['label'] ?? ''));
          $detail = trim((string) ($sub['detail'] ?? ''));
          if ($label !== '' && $detail !== '') {
            $items[] = ['label' => $label, 'detail' => $detail];
          }
        }
      } else {
        $lines = preg_split('/\R/u', (string) ($row['items_text'] ?? '')) ?: [];
        foreach ($lines as $line) {
          $line = trim((string) $line);
          if ($line === '') {
            continue;
          }
          $parts = array_map('trim', explode('|', $line, 2));
          if (count($parts) === 2 && $parts[0] !== '' && $parts[1] !== '') {
            $items[] = ['label' => $parts[0], 'detail' => $parts[1]];
          }
        }
      }

      if ($items === []) {
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

if (! function_exists('eai_ocean_park_pricing_get_rc_props')) {
  /**
   * @param array<string, mixed> $settings
   * @return array<string, mixed>
   */
  function eai_ocean_park_pricing_get_rc_props(array $settings): array
  {
    $primary_link = is_array($settings['primary_cta_link'] ?? null)
      ? $settings['primary_cta_link']
      : [];
    $secondary_link = is_array($settings['secondary_cta_link'] ?? null)
      ? $settings['secondary_cta_link']
      : [];
    $bottom_link = is_array($settings['bottom_prompt_button_link'] ?? null)
      ? $settings['bottom_prompt_button_link']
      : [];

    $eyebrow = trim((string) ($settings['eyebrow'] ?? ''));
    $title = trim((string) ($settings['title'] ?? ''));
    $description = trim((string) ($settings['description'] ?? ''));
    $featured_badge = trim((string) ($settings['featured_badge'] ?? ''));
    $featured_title = trim((string) ($settings['featured_title'] ?? ''));

    $props = [
      'eyebrow' => $eyebrow,
      'title' => $title,
      'description' => $description,
      'featuredPackage' => [
        'badge' => $featured_badge,
        'title' => $featured_title,
        'price' => trim((string) ($settings['featured_price'] ?? '')),
        'unit' => trim((string) ($settings['featured_unit'] ?? '')),
        'note' => trim((string) ($settings['featured_note'] ?? '')),
        'handoverSavingNote' => trim((string) ($settings['featured_handover_saving_note'] ?? '')),
      ],
      'primaryCta' => [
        'label' => trim((string) ($settings['primary_cta_label'] ?? '')),
        'link' => eai_rc_map_link($primary_link),
      ],
      'secondaryCta' => [
        'label' => trim((string) ($settings['secondary_cta_label'] ?? '')),
        'link' => eai_rc_map_link($secondary_link),
      ],
      'unitEstimatesTitle' => trim((string) ($settings['unit_estimates_title'] ?? '')),
      'unitEstimatesSubtitle' => trim((string) ($settings['unit_estimates_subtitle'] ?? '')),
      'unitEstimates' => eai_ocean_park_pricing_map_unit_estimates((array) ($settings['unit_estimates'] ?? [])),
      'roomEstimates' => eai_ocean_park_pricing_map_room_estimates((array) ($settings['room_estimates'] ?? [])),
      'commitments' => eai_ocean_park_pricing_map_commitments((array) ($settings['commitments'] ?? [])),
      'bottomPrompt' => [
        'title' => trim((string) ($settings['bottom_prompt_title'] ?? '')),
        'description' => trim((string) ($settings['bottom_prompt_description'] ?? '')),
        'buttonLabel' => trim((string) ($settings['bottom_prompt_button_label'] ?? '')),
        'buttonLink' => eai_rc_map_link($bottom_link),
      ],
    ];

    $class_name = trim((string) ($settings['class_name'] ?? ''));
    if ($class_name !== '') {
      $props['className'] = $class_name;
    }

    return $props;
  }
}

if (! function_exists('eai_ocean_park_pricing_props_are_empty')) {
  /**
   * @param array<string, mixed> $props
   */
  function eai_ocean_park_pricing_props_are_empty(array $props): bool
  {
    return trim((string) ($props['featuredPackage']['price'] ?? '')) === ''
      && empty($props['unitEstimates'])
      && empty($props['roomEstimates'])
      && empty($props['commitments']);
  }
}

if (! function_exists('eai_ocean_park_pricing_get_editor_sample_props')) {
  /**
   * @param array<string, mixed> $settings
   * @return array<string, mixed>
   */
  function eai_ocean_park_pricing_get_editor_sample_props(array $settings): array
  {
    $props = [
      'eyebrow' => trim((string) ($settings['eyebrow'] ?? '')) ?: 'BẢNG BÁO GIÁ GỐC TẠI NHÀ XƯỞNG 2.000M² GẦN OCEAN PARK — MINH BẠCH TỪNG HẠNG MỤC',
      'title' => trim((string) ($settings['title'] ?? '')) ?: 'Báo Giá Thiết Kế & Thi Công Nội Thất Vinhomes Ocean Park Trọn Gói Cao Cấp 1 • 2 • 3',
      'description' => trim((string) ($settings['description'] ?? '')) ?: 'Nhờ sở hữu Nhà máy sản xuất nội thất 2.000m² ngay tại Ngọc Trai – Như Quỳnh (cách Ocean Park chỉ 10 phút), Nhà mẫu nội khu tại Sao Biển 23 và Văn phòng KTS tại Số 01 Voi Phục Gia Lâm, IC HOUSE mang đến báo giá gốc tại xưởng tiết kiệm 15–20%.',
      'featuredPackage' => [
        'badge' => trim((string) ($settings['featured_badge'] ?? '')) ?: 'GÓI THI CÔNG NỘI THẤT TRỌN GÓI BESPOKE LUXURY — GIÁ XƯỞNG TRỰC TIẾP SÁT OCEAN PARK',
        'title' => trim((string) ($settings['featured_title'] ?? '')) ?: 'Hoàn Thiện Nội Thất Căn Hộ Vinhomes Ocean Park Trọn Gói Cao Cấp (Từ 120 triệu/căn • Phổ biến 180 – 260 triệu/căn)',
        'price' => trim((string) ($settings['featured_price'] ?? '')) ?: '180 – 260 triệu',
        'unit' => trim((string) ($settings['featured_unit'] ?? '')) ?: '/ căn hộ 2PN – 2PN+1 hoàn thiện đồng bộ chuẩn Luxury tại xưởng IC HOUSE',
        'note' => trim((string) ($settings['featured_note'] ?? '')) ?: 'Gói khởi điểm từ 120 triệu/căn (Studio/1PN). Miễn phí 100% chi phí thiết kế 3D (250.000đ – 350.000đ/m²) khi ký hợp đồng thi công trọn gói tại IC HOUSE.',
        'handoverSavingNote' => trim((string) ($settings['featured_handover_saving_note'] ?? '')) ?: 'Đặc quyền tối ưu ngân sách: Tiết kiệm ngay 35 – 60 triệu/căn cho các tòa đã bàn giao sẵn hệ tủ bếp & thiết bị cao cấp.',
      ],
      'primaryCta' => [
        'label' => trim((string) ($settings['primary_cta_label'] ?? '')) ?: 'Gọi nhận báo giá xưởng: 0869 855 885',
        'link' => eai_rc_map_link(is_array($settings['primary_cta_link'] ?? null) ? $settings['primary_cta_link'] : ['url' => 'tel:0869855885']),
      ],
      'secondaryCta' => [
        'label' => trim((string) ($settings['secondary_cta_label'] ?? '')) ?: 'Nhận bảng dự toán BOQ theo mã căn',
        'link' => eai_rc_map_link(is_array($settings['secondary_cta_link'] ?? null) ? $settings['secondary_cta_link'] : ['url' => '#dang-ky-tu-van']),
      ],
      'unitEstimatesTitle' => trim((string) ($settings['unit_estimates_title'] ?? '')) ?: 'Bảng Giá Dự Toán Thi Công Nội Thất Trọn Gói Theo Loại Căn Hộ Ocean Park',
      'unitEstimatesSubtitle' => trim((string) ($settings['unit_estimates_subtitle'] ?? '')) ?: 'Báo giá sản xuất trực tiếp tại Nhà xưởng IC HOUSE gần Ocean Park — Áp dụng cho Căn hộ The London, Căn hộ The Beverly, The Paris, Căn hộ LUMIÈRE SpringBay, Dự án Masteri Trinity Square & Căn hộ cao tầng Ocean Park 3',
      'unitEstimates' => [
        ['unitType' => 'Studio / 1PN', 'areaRange' => '28m² – 43m²', 'priceRange' => '120 – 155 triệu', 'targetZones' => 'The London (LD1–LD3), The Paris, Cao tầng Ocean Park 3'],
        ['unitType' => '1PN+1', 'areaRange' => '45m² – 54m²', 'priceRange' => '145 – 185 triệu', 'targetZones' => 'The Beverly (BE1–BE4), The London, LUMIÈRE SpringBay'],
        ['unitType' => '2PN / 2PN+1', 'areaRange' => '58m² – 76m²', 'priceRange' => '180 – 260 triệu', 'highlight' => true, 'targetZones' => 'The Beverly, The London, LUMIÈRE SpringBay, Masteri Trinity Square'],
        ['unitType' => '3PN / Căn góc VIP', 'areaRange' => '82m² – 108m²', 'priceRange' => '260 – 380 triệu', 'targetZones' => 'Căn góc Tòa BE1–BE4, Tòa LD1–LD3, Masteri Trinity Square'],
        ['unitType' => 'Duplex / Penthouse / Ghép căn', 'areaRange' => '115m² – 180m²+', 'priceRange' => '420 – 750+ triệu', 'targetZones' => 'LUMIÈRE SpringBay, Masteri Trinity Square, Ocean Park 1-2-3'],
      ],
      'roomEstimates' => [
        ['roomName' => 'Nội thất Phòng khách liền Bếp', 'englishName' => 'Living & Kitchen Bespoke', 'priceRange' => '75 – 125 triệu'],
        ['roomName' => 'Nội thất Phòng ngủ Master Luxury', 'englishName' => 'Master Bedroom Suite', 'priceRange' => '45 – 75 triệu'],
        ['roomName' => 'Nội thất Phòng ngủ phụ / Trẻ em', 'englishName' => 'Second / Kids Bedroom', 'priceRange' => '30 – 50 triệu'],
        ['roomName' => 'Không gian +1 Đa năng & Logia', 'englishName' => 'Multi-function +1 & Balcony', 'priceRange' => '18 – 35 triệu'],
      ],
      'commitments' => [
        [
          'title' => 'Hạng mục thi công trọn gói từ A–Z',
          'items' => [
            ['label' => 'Cải tạo trần thạch cao – sàn gỗ – tường & điện ánh sáng', 'detail' => 'Trần thạch cao Vĩnh Tường chống ẩm, hệ đèn LED ray nam châm / âm trần CRI>90, phào chỉ PU/PS và sàn gỗ xương cá cao cấp.'],
            ['label' => 'Hệ tủ bếp cánh kính & Đảo bếp đá Vicostone', 'detail' => 'Tủ bếp kịch trần chống ẩm tuyệt đối, mặt đá thạch anh Vicostone chống thấm ố và phụ kiện thông minh.'],
            ['label' => 'Nội thất liền tường & Đồ rời may đo đồng bộ', 'detail' => 'Tủ áo cánh kính khung nhôm Anode tích hợp đèn cảm biến, giường bọc da/nỉ cao cấp, sofa may đo.'],
          ],
        ],
        [
          'title' => 'Cam kết vật liệu chính hãng tại Xưởng & Nhà mẫu',
          'items' => [
            ['label' => '100% cốt gỗ An Cường & Sơn Inchem 5 lớp', 'detail' => '100% gỗ MDF lõi xanh chống ẩm An Cường, Plywood chịu nước phủ Veneer óc chó/sồi tự nhiên và sơn Inchem 5 lớp.'],
            ['label' => 'Đá thạch anh Vicostone & Nhôm kính Anode', 'detail' => 'Đá thạch anh Vicostone bảo hành chính hãng, khung nhôm Anode mạ Champagne và inox 304 mạ PVD.'],
            ['label' => 'Phụ kiện Blum (Áo) / Hafele (Đức) chính hãng', 'detail' => 'Bản lề, ray âm giảm chấn, tay nâng Blum (Áo) hoặc Hafele (Đức).'],
          ],
        ],
        [
          'title' => 'Đặc quyền 3 vị trí địa lý sát vách Ocean Park',
          'items' => [
            ['label' => 'Xem Nhà mẫu tại Sao Biển 23 & Nghiệm thu mộc tại Xưởng', 'detail' => 'Tham quan trực tiếp Nhà mẫu IC HOUSE tại Sao Biển 23 Vinhomes Ocean Park và Nhà xưởng Như Quỳnh (2.000m²).'],
            ['label' => 'Văn phòng Số 01 Voi Phục bao trọn thủ tục Ban quản lý', 'detail' => 'IC HOUSE đại diện chuẩn bị toàn bộ bản vẽ kỹ thuật MEP và hồ sơ xin cấp phép thi công.'],
            ['label' => 'Bảo hành 2 năm • Bảo trì 5 năm có mặt trong 2 giờ', 'detail' => 'Đội ngũ kỹ thuật túc trực ngay tại Văn phòng Số 01 Voi Phục và Sao Biển 23.'],
          ],
        ],
      ],
      'bottomPrompt' => [
        'title' => trim((string) ($settings['bottom_prompt_title'] ?? '')) ?: 'Nhận bảng dự toán BOQ bóc tách từng hạng mục giá xưởng theo đúng mã căn hộ của bạn',
        'description' => trim((string) ($settings['bottom_prompt_description'] ?? '')) ?: 'Chỉ cần gửi mã tòa, KTS Trưởng IC HOUSE tại Số 01 Voi Phục & Sao Biển 23 sẽ gửi bảng báo giá chi tiết trong 30 phút.',
        'buttonLabel' => trim((string) ($settings['bottom_prompt_button_label'] ?? '')) ?: 'Yêu cầu bảng báo giá chi tiết',
        'buttonLink' => eai_rc_map_link(
          is_array($settings['bottom_prompt_button_link'] ?? null)
            ? $settings['bottom_prompt_button_link']
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
