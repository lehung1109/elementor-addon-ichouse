<?php
if (! defined('ABSPATH')) {
  exit;
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

    return array_values(array_filter(array_map(
      static fn($line): string => trim((string) $line),
      $lines
    )));
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

    $props = [
      'eyebrow' => trim((string) ($settings['eyebrow'] ?? '')),
      'title' => trim((string) ($settings['title'] ?? '')),
      'leadHtml' => (string) ($settings['lead_html'] ?? ''),
      'highlights' => eai_ocean_park_intro_map_highlights((array) ($settings['highlights'] ?? [])),
      'subdivisionBadges' => eai_ocean_park_intro_map_subdivision_badges($settings['subdivision_badges'] ?? ''),
      'steps' => eai_ocean_park_intro_map_steps((array) ($settings['steps'] ?? [])),
      'primaryCta' => [
        'label' => trim((string) ($settings['primary_cta_label'] ?? '')),
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
      'eyebrow' => trim((string) ($settings['eyebrow'] ?? '')) ?: 'IC HOUSE LUXURY — NỘI THẤT MAY ĐO ĐẠI ĐÔ THỊ OCEAN PARK 1 • 2 • 3',
      'title' => trim((string) ($settings['title'] ?? '')) ?: 'Chuyên Thiết Kế & Thi Công Nội Thất Cao Cấp Tại Vinhomes Ocean Park',
      'leadHtml' => trim((string) ($settings['lead_html'] ?? '')) ?: '<p><strong>IC HOUSE</strong> định vị giải pháp tư vấn mặt bằng chuyên sâu, thiết kế 3D Bespoke và sản xuất – thi công nội thất trọn gói phân khúc <strong>Trung – Cao cấp & Luxury</strong> cho các tòa căn hộ tại <strong>Ocean Park 1</strong>, <strong>Ocean Park 2</strong> và <strong>Ocean Park 3</strong>.</p>',
      'highlights' => [
        ['label' => 'Thiết kế 3D Bespoke', 'value' => '250.000đ – 350.000đ/m²', 'note' => 'KTS trưởng 5–15 năm kinh nghiệm chủ trì'],
        ['label' => 'Thi công trọn gói Cao cấp', 'value' => 'Từ 120 triệu/căn', 'note' => 'Gói Luxury đồng bộ: 180 – 260 triệu/căn'],
        ['label' => 'Ưu đãi ký HĐ thi công', 'value' => 'Miễn phí 100% TK', 'note' => 'Hoàn trả toàn bộ phí thiết kế 3D & hồ sơ kỹ thuật'],
        ['label' => 'Nhà mẫu & Xưởng tại chỗ', 'value' => 'Sao Biển 23 OCP', 'note' => 'Bảo hành 2 năm • Bảo trì 5 năm tận căn hộ'],
      ],
      'subdivisionBadges' => [
        'The London (Tòa LD1 • LD2 • LD3)',
        'The Beverly (Tòa BE1 • BE2 • BE3 • BE4)',
        'The Paris Ocean Park 1',
        'LUMIÈRE SpringBay Ocean Park 2',
        'Masteri Trinity Square Ocean Park 2',
        'Căn hộ cao tầng Ocean Park 3',
      ],
      'steps' => [
        ['number' => '01', 'title' => 'Khảo sát thực tế tại tòa trong 15 phút', 'description' => 'KTS từ văn phòng Số 01 Voi Phục có mặt đo đạc trực tiếp từng góc căn hộ Chung cư Ocean Park 1, Ocean Park 2, Ocean Park 3 và tư vấn công năng tại chỗ.'],
        ['number' => '02', 'title' => 'Thiết kế 3D & Trải nghiệm Nhà mẫu Sao Biển 23', 'description' => 'Cá nhân hóa phong cách Modern Luxury, Japandi, Neoclassic, Wabi Sabi; mời gia chủ kiểm chứng trực tiếp cốt gỗ An Cường, đá Vicostone và sơn Inchem tại nhà mẫu.'],
        ['number' => '03', 'title' => 'Sản xuất giá xưởng & Thi công đúng cam kết', 'description' => 'Gia công CNC tự động tại nhà máy Ngọc Trai – Như Quỳnh ngay sát dự án, báo giá minh bạch không phát sinh, hỗ trợ trọn gói thủ tục ban quản lý tòa nhà.'],
      ],
      'primaryCta' => [
        'label' => trim((string) ($settings['primary_cta_label'] ?? '')) ?: 'Gọi tư vấn: 0869 855 885',
        'link' => eai_rc_map_link(
          is_array($settings['primary_cta_link'] ?? null) ? $settings['primary_cta_link'] : ['url' => 'tel:0869855885']
        ),
      ],
      'secondaryCta' => [
        'label' => trim((string) ($settings['secondary_cta_label'] ?? '')) ?: 'Nhận báo giá theo mã căn',
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
