<?php
if (! defined('ABSPATH')) {
  exit;
}

if (! function_exists('eai_ocean_park_normalize_legacy_phone')) {
  function eai_ocean_park_normalize_legacy_phone(string $value): string
  {
    return str_replace(
      ['0869 855 885', '0869855885'],
      ['0978 305 740', '0978305740'],
      $value
    );
  }
}

if (! function_exists('eai_ocean_park_intro_map_highlights')) {
  /**
   * @param array<int, mixed> $rows
   * @return array<int, array{label: string, value: string, note?: string}>
   */
  function eai_ocean_park_intro_map_highlights(array $rows): array
  {
    $mapped = [];
    foreach ($rows as $row) {
      if (! is_array($row)) {
        continue;
      }
      $label = trim((string) ($row['label'] ?? ''));
      $value = trim((string) ($row['value'] ?? ''));
      if ($label === '' || $value === '') {
        continue;
      }
      $item = [
        'label' => $label,
        'value' => $value,
      ];
      $note = trim((string) ($row['note'] ?? ''));
      if ($note !== '') {
        $item['note'] = $note;
      }
      $mapped[] = $item;
    }

    return $mapped;
  }
}

if (! function_exists('eai_ocean_park_intro_map_subdivision_badges')) {
  /**
   * @return array<int, string>
   */
  function eai_ocean_park_intro_map_subdivision_badges(mixed $raw): array
  {
    if (is_array($raw)) {
      $lines = [];
      foreach ($raw as $item) {
        if (is_array($item)) {
          $lines[] = (string) ($item['text'] ?? $item['label'] ?? '');
        } elseif (is_scalar($item)) {
          $lines[] = (string) $item;
        }
      }
    } else {
      $lines = preg_split('/\R/u', (string) $raw) ?: [];
    }

    $badges = array_values(array_filter(array_map(
      static fn($line): string => trim((string) $line),
      $lines
    )));

    return $badges;
  }
}

if (! function_exists('eai_ocean_park_intro_map_steps')) {
  /**
   * @param array<int, mixed> $rows
   * @return array<int, array{number: string, title: string, description: string}>
   */
  function eai_ocean_park_intro_map_steps(array $rows): array
  {
    $mapped = [];
    foreach ($rows as $row) {
      if (! is_array($row)) {
        continue;
      }
      $number = trim((string) ($row['number'] ?? ''));
      $title = trim((string) ($row['title'] ?? ''));
      $description = trim((string) ($row['description'] ?? ''));
      if ($title === '' || $description === '') {
        continue;
      }

      $mapped[] = [
        'number' => $number !== '' ? $number : '01',
        'title' => $title,
        'description' => $description,
      ];
    }

    return $mapped;
  }
}

if (! function_exists('eai_ocean_park_intro_get_rc_props')) {
  /**
   * @param array<string, mixed> $settings
   * @return array<string, mixed>
   */
  function eai_ocean_park_intro_get_rc_props(array $settings): array
  {
    $primary_link = is_array($settings['primary_cta_link'] ?? null)
      ? $settings['primary_cta_link']
      : [];
    $secondary_link = is_array($settings['secondary_cta_link'] ?? null)
      ? $settings['secondary_cta_link']
      : [];

    $eyebrow = trim((string) ($settings['eyebrow'] ?? ''));
    $title = trim((string) ($settings['title'] ?? ''));
    $lead_html = (string) ($settings['lead_html'] ?? '');

    if (isset($primary_link['url']) && is_string($primary_link['url'])) {
      $primary_link['url'] = eai_ocean_park_normalize_legacy_phone($primary_link['url']);
    }

    $props = [
      'eyebrow' => $eyebrow,
      'title' => $title,
      'leadHtml' => $lead_html,
      'highlights' => eai_ocean_park_intro_map_highlights((array) ($settings['highlights'] ?? [])),
      'subdivisionBadges' => eai_ocean_park_intro_map_subdivision_badges($settings['subdivision_badges'] ?? ''),
      'steps' => eai_ocean_park_intro_map_steps((array) ($settings['steps'] ?? [])),
      'primaryCta' => [
        'label' => eai_ocean_park_normalize_legacy_phone(trim((string) ($settings['primary_cta_label'] ?? ''))),
        'link' => eai_rc_map_link($primary_link),
      ],
      'secondaryCta' => [
        'label' => trim((string) ($settings['secondary_cta_label'] ?? '')),
        'link' => eai_rc_map_link($secondary_link),
      ],
    ];

    $class_name = trim((string) ($settings['class_name'] ?? ''));
    if ($class_name !== '') {
      $props['className'] = $class_name;
    }

    return $props;
  }
}

if (! function_exists('eai_ocean_park_intro_props_are_empty')) {
  /**
   * @param array<string, mixed> $props
   */
  function eai_ocean_park_intro_props_are_empty(array $props): bool
  {
    return trim((string) ($props['eyebrow'] ?? '')) === ''
      && trim((string) ($props['title'] ?? '')) === ''
      && trim((string) ($props['leadHtml'] ?? '')) === ''
      && empty($props['highlights'])
      && empty($props['subdivisionBadges'])
      && empty($props['steps']);
  }
}

if (! function_exists('eai_ocean_park_intro_get_editor_sample_props')) {
  /**
   * @param array<string, mixed> $settings
   * @return array<string, mixed>
   */
  function eai_ocean_park_intro_get_editor_sample_props(array $settings): array
  {
    $props = [
      'eyebrow' => trim((string) ($settings['eyebrow'] ?? '')) ?: 'IC HOUSE LUXURY — ĐƠN VỊ CÓ NHÀ MẪU TRONG OCEAN PARK • VĂN PHÒNG & NHÀ XƯỞNG SÁT KHU ĐÔ THỊ',
      'title' => trim((string) ($settings['title'] ?? '')) ?: 'Thiết Kế & Thi Công Nội Thất Vinhomes Ocean Park Trọn Gói Cao Cấp — Chuẩn May Đo Bespoke',
      'leadHtml' => trim((string) ($settings['lead_html'] ?? '')) ?: '<p><strong>IC HOUSE</strong> là đơn vị <strong>thiết kế và thi công nội thất Vinhomes Ocean Park trọn gói cao cấp</strong> uy tín hàng đầu tại Gia Lâm – Hà Nội, sở hữu <strong>Nhà mẫu thực tế ngay TRONG khu đô thị (Sao Biển 23)</strong>, <strong>Văn phòng KTS gần khu đô thị (Số 01 Voi Phục)</strong> và <strong>Nhà xưởng sản xuất 2.000m² sát Ocean Park (Ngọc Trai – Như Quỳnh)</strong>.</p>',
      'highlights' => [
        ['label' => 'Nhà mẫu TRONG Ocean Park', 'value' => 'Sao Biển 23 OCP', 'note' => 'Xem thực tế gỗ An Cường, đá Vicostone, tủ bếp cánh kính ngay nội khu'],
        ['label' => 'Văn phòng GẦN khu đô thị', 'value' => 'Số 01 Voi Phục', 'note' => 'KTS trưởng 5–15 năm kinh nghiệm có mặt khảo sát tại tòa sau 15 phút'],
        ['label' => 'Nhà xưởng 2.000m² GẦN OCP', 'value' => 'Giá gốc tại xưởng', 'note' => 'Xưởng Ngọc Trai – Như Quỳnh cách 10 phút, tiết kiệm 15–20% chi phí'],
        ['label' => 'Thi công trọn gói Luxury', 'value' => 'Miễn phí 100% TK', 'note' => 'Trọn gói từ 120 triệu/căn (Luxury 180–260tr) • Bảo hành 2 năm, bảo trì 5 năm'],
      ],
      'subdivisionBadges' => [
        'Nhà mẫu: Sao Biển 23 Vinhomes Ocean Park',
        'Văn phòng: Số 01 Voi Phục – Gia Lâm',
        'Nhà xưởng 2.000m²: Ngọc Trai – Như Quỳnh (Cách OCP 10 phút)',
        'The London Ocean Park 1 (Tòa LD1 • LD2 • LD3)',
        'The Beverly Ocean Park 1 (Tòa BE1 • BE2 • BE3 • BE4)',
        'The Paris Ocean Park 1 — Chung cư Ocean Park 1',
        'LUMIÈRE SpringBay Ocean Park 2',
        'Masteri Trinity Square Ocean Park 2',
        'Căn hộ cao tầng Ocean Park 3',
      ],
      'steps' => [
        ['number' => '01', 'title' => 'Văn phòng Số 01 Voi Phục gần KĐT — Khảo sát căn hộ trong 15 phút', 'description' => 'KTS trưởng từ văn phòng Số 01 Voi Phục Gia Lâm có mặt đo đạc trực tiếp từng góc căn hộ Chung cư Ocean Park 1, Ocean Park 2, Ocean Park 3 và tư vấn công năng tại chỗ.'],
        ['number' => '02', 'title' => 'Thiết kế 3D Bespoke & Trải nghiệm Nhà mẫu TRONG Ocean Park (Sao Biển 23)', 'description' => 'Cá nhân hóa phong cách Modern Luxury, Japandi, Neoclassic, Wabi Sabi; mời gia chủ kiểm chứng trực tiếp cốt gỗ An Cường, đá Vicostone và sơn Inchem tại nhà mẫu Sao Biển 23.'],
        ['number' => '03', 'title' => 'Sản xuất tại Nhà xưởng 2.000m² gần Ocean Park & Thi công trọn gói chuẩn BQL', 'description' => 'Gia công CNC tự động tại nhà xưởng Ngọc Trai – Như Quỳnh ngay sát dự án, báo giá gốc tại xưởng minh bạch không phát sinh, bao trọn thủ tục Ban quản lý tòa nhà.'],
      ],
      'primaryCta' => [
        'label' => eai_ocean_park_normalize_legacy_phone(trim((string) ($settings['primary_cta_label'] ?? '')) ?: 'Gọi KTS Trưởng: 0978 305 740'),
        'link' => eai_rc_map_link(
          is_array($settings['primary_cta_link'] ?? null) ? $settings['primary_cta_link'] : ['url' => 'tel:0978305740']
        ),
      ],
      'secondaryCta' => [
        'label' => trim((string) ($settings['secondary_cta_label'] ?? '')) ?: 'Nhận báo giá & Đặt lịch xem Nhà mẫu',
        'link' => eai_rc_map_link(
          is_array($settings['secondary_cta_link'] ?? null) ? $settings['secondary_cta_link'] : ['url' => '#dang-ky-tu-van']
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
