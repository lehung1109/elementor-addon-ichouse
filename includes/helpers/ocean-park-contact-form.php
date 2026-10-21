<?php
if (! defined('ABSPATH')) {
  exit;
}

if (! function_exists('eai_ocean_park_contact_form_normalize_key')) {
  /**
   * Normalize formKey for OceanParkContactFormWrapper.
   * Ensures multiple instances on the same page using blank or default key get unique keys per widget ID.
   */
  function eai_ocean_park_contact_form_normalize_key(string $raw_key, string $widget_id = ''): string
  {
    $clean_widget_id = sanitize_title($widget_id);
    if ($clean_widget_id === '') {
      $clean_widget_id = 'default';
    }

    $clean_key = sanitize_title(trim($raw_key));
    if ($clean_key === '') {
      return 'ocean-park-contact-' . $clean_widget_id;
    }

    if ($clean_key === 'ocean-park-consultation' && trim($widget_id) !== '') {
      return 'ocean-park-consultation-' . $clean_widget_id;
    }

    return $clean_key;
  }
}

if (! function_exists('eai_ocean_park_contact_form_map_locations')) {
  /**
   * Map locations repeater to OceanParkLocationItemModel[].
   *
   * @param mixed $raw_rows
   * @return array<int,array{label:string,address:string}>
   */
  function eai_ocean_park_contact_form_map_locations($raw_rows): array
  {
    if (! is_array($raw_rows)) {
      return [];
    }

    $items = [];
    foreach ($raw_rows as $row) {
      if (! is_array($row)) {
        continue;
      }

      $label = sanitize_text_field((string) ($row['label'] ?? ''));
      $address = sanitize_text_field((string) ($row['address'] ?? ''));
      if ($label === '' || $address === '') {
        continue;
      }

      $items[] = [
        'label' => $label,
        'address' => $address,
      ];
    }

    return $items;
  }
}

if (! function_exists('eai_ocean_park_contact_form_map_reference_links')) {
  /**
   * Map reference_links repeater to OceanParkReferenceLinkModel[].
   *
   * @param mixed $raw_rows
   * @return array<int,array{label:string,url:string}>
   */
  function eai_ocean_park_contact_form_map_reference_links($raw_rows): array
  {
    if (! is_array($raw_rows)) {
      return [];
    }

    $items = [];
    foreach ($raw_rows as $row) {
      if (! is_array($row)) {
        continue;
      }

      $label = sanitize_text_field((string) ($row['label'] ?? ''));
      $raw_url = $row['url'] ?? '';
      $url = is_array($raw_url)
        ? trim((string) ($raw_url['url'] ?? ''))
        : trim((string) $raw_url);
      if ($label === '' || $url === '') {
        continue;
      }

      $items[] = [
        'label' => $label,
        'url' => $url,
      ];
    }

    return $items;
  }
}

if (! function_exists('eai_ocean_park_contact_form_map_zone_options')) {
  /**
   * Map zone options from textarea (`zone_options_text`) or repeater (`zone_options`) to string[].
   *
   * @param array<string,mixed> $settings
   * @return array<int,string>
   */
  function eai_ocean_park_contact_form_map_zone_options(array $settings): array
  {
    if (isset($settings['zone_options_text']) && is_string($settings['zone_options_text'])) {
      $lines = preg_split('/\R/u', $settings['zone_options_text']) ?: [];
      return array_values(array_filter(array_map(
        static fn($line): string => sanitize_text_field((string) $line),
        $lines
      )));
    }

    if (isset($settings['zone_options']) && is_array($settings['zone_options'])) {
      $options = [];
      foreach ($settings['zone_options'] as $item) {
        $val = is_array($item)
          ? sanitize_text_field((string) ($item['label'] ?? $item['text'] ?? ''))
          : sanitize_text_field((string) $item);
        if ($val !== '') {
          $options[] = $val;
        }
      }
      return $options;
    }

    return [];
  }
}

if (! function_exists('eai_ocean_park_contact_form_get_rc_props')) {
  /**
   * Build OceanParkContactFormModel props from Elementor widget settings.
   *
   * @param array<string,mixed> $settings
   * @return array<string,mixed>
   */
  function eai_ocean_park_contact_form_get_rc_props(array $settings, string $widget_id = ''): array
  {
    $section_id = sanitize_title((string) ($settings['section_id'] ?? ''));
    if ($section_id === '') {
      $section_id = 'dang-ky-tu-van';
    }

    return [
      'className' => sanitize_text_field((string) ($settings['class_name'] ?? '')),
      'sectionId' => $section_id,
      'formKey' => eai_ocean_park_contact_form_normalize_key((string) ($settings['form_key'] ?? ''), $widget_id),
      'eyebrow' => sanitize_text_field((string) ($settings['eyebrow'] ?? '')),
      'title' => sanitize_text_field((string) ($settings['title'] ?? '')),
      'description' => sanitize_text_field((string) ($settings['description'] ?? '')),
      'hotline' => sanitize_text_field((string) ($settings['hotline'] ?? '')),
      'zaloPhone' => sanitize_text_field((string) ($settings['zalo_phone'] ?? '')),
      'email' => sanitize_text_field((string) ($settings['email'] ?? '')),
      'website' => sanitize_text_field((string) ($settings['website'] ?? '')),
      'locations' => eai_ocean_park_contact_form_map_locations($settings['locations'] ?? []),
      'referenceLinks' => eai_ocean_park_contact_form_map_reference_links($settings['reference_links'] ?? []),
      'formTitle' => sanitize_text_field((string) ($settings['form_title'] ?? '')),
      'formSubtitle' => sanitize_text_field((string) ($settings['form_subtitle'] ?? '')),
      'zoneOptions' => eai_ocean_park_contact_form_map_zone_options($settings),
      'submitLabel' => sanitize_text_field((string) ($settings['submit_label'] ?? '')),
      'successMessage' => sanitize_text_field((string) ($settings['success_message'] ?? '')),
    ];
  }
}

if (! function_exists('eai_ocean_park_contact_form_props_are_empty')) {
  /**
   * Whether contact form props are empty.
   *
   * @param array<string,mixed> $props
   */
  function eai_ocean_park_contact_form_props_are_empty(array $props): bool
  {
    return ($props['eyebrow'] ?? '') === ''
      && ($props['title'] ?? '') === ''
      && ($props['formTitle'] ?? '') === ''
      && ($props['hotline'] ?? '') === ''
      && empty($props['locations'])
      && empty($props['zoneOptions']);
  }
}

if (! function_exists('eai_ocean_park_contact_form_get_editor_sample_props')) {
  /**
   * Editor sample props matching `src/data/ocean-park-contact-form-wrapper.ts`.
   *
   * @param array<string,mixed> $settings
   * @return array<string,mixed>
   */
  function eai_ocean_park_contact_form_get_editor_sample_props(array $settings = [], string $widget_id = ''): array
  {
    return eai_ocean_park_contact_form_get_rc_props([
      'class_name' => $settings['class_name'] ?? '',
      'section_id' => $settings['section_id'] ?? 'dang-ky-tu-van',
      'form_key' => $settings['form_key'] ?? 'ocean-park-consultation',
      'eyebrow' => 'ĐĂNG KÝ TƯ VẤN & THAM QUAN NHÀ MẪU THỰC TẾ',
      'title' => 'Nhận Bản Vẽ Mặt Bằng 2D, Phối Cảnh 3D & Dự Toán Chi Tiết Theo Mã Căn Hộ Ocean Park',
      'description' => 'Đội ngũ Kiến trúc sư trưởng IC HOUSE trực tiếp khảo sát tại các tòa The London (LD1, LD2, LD3), The Beverly (BE1, BE2, BE3, BE4), The Paris Ocean Park 1, LUMIÈRE SpringBay Ocean Park 2, Masteri Trinity Square Ocean Park 2 và Căn hộ cao tầng Ocean Park 3.',
      'hotline' => '0869 855 885',
      'zalo_phone' => '0978 305 740',
      'email' => 'noithatichouse@gmail.com',
      'website' => 'noithat.ichouse.vn',
      'locations' => [
        [
          'label' => 'Nhà mẫu nội thất trong khu đô thị Vinhomes Ocean Park',
          'address' => 'Sao Biển 23, KĐT Vinhomes Ocean Park, Gia Lâm, Hà Nội',
        ],
        [
          'label' => 'Trụ sở Văn phòng thiết kế IC HOUSE (Kế cận đại đô thị)',
          'address' => 'Số 01 Voi Phục, Gia Lâm, Hà Nội',
        ],
        [
          'label' => 'Nhà máy sản xuất trực tiếp 2.000m² (Cách Ocean Park 10 phút)',
          'address' => 'Thôn Ngọc Trai, Xã Như Quỳnh, Tỉnh Hưng Yên',
        ],
      ],
      'reference_links' => [
        [
          'label' => 'Nội thất The London Ocean Park 1 (Tòa LD1 • LD2 • LD3)',
          'url' => '#du-an-mau-ocean-park',
        ],
        [
          'label' => 'Nội thất The Beverly Ocean Park 1 (Tòa BE1 • BE2 • BE3 • BE4)',
          'url' => '#du-an-mau-ocean-park',
        ],
        [
          'label' => 'Nội thất The Paris Ocean Park 1 — Chung cư Ocean Park 1',
          'url' => '#du-an-mau-ocean-park',
        ],
        [
          'label' => 'Nội thất Căn hộ LUMIÈRE SpringBay Ocean Park 2',
          'url' => '#du-an-mau-ocean-park',
        ],
        [
          'label' => 'Nội thất Dự án Masteri Trinity Square Ocean Park 2',
          'url' => '#du-an-mau-ocean-park',
        ],
        [
          'label' => 'Nội thất Căn hộ cao tầng Ocean Park 3 — Dự án căn hộ Ocean Park 3',
          'url' => '#du-an-mau-ocean-park',
        ],
      ],
      'form_title' => 'Đặt Lịch Tư Vấn 1:1 & Nhận Báo Giá Theo Mã Căn',
      'form_subtitle' => 'Miễn phí 100% chi phí khảo sát hiện trạng, đo đạc laser và lên phương án mặt bằng 2D tối ưu công năng cho cư dân Vinhomes Ocean Park 1, 2, 3.',
      'zone_options_text' => implode("\n", [
        'The London Ocean Park 1 — Tòa LD1 / Tòa LD2 / Tòa LD3',
        'The Beverly Ocean Park 1 — Tòa BE1 / Tòa BE2 / Tòa BE3 / Tòa BE4',
        'The Paris Ocean Park 1 — Chung cư Ocean Park 1',
        'LUMIÈRE SpringBay Ocean Park 2 — Căn hộ LUMIÈRE SpringBay',
        'Masteri Trinity Square Ocean Park 2 — Dự án Masteri Trinity Square',
        'Căn hộ cao tầng Ocean Park 3 — Dự án căn hộ Ocean Park 3',
        'Phân khu khác tại Vinhomes Ocean Park 1 • 2 • 3',
      ]),
      'submit_label' => 'Nhận Bản Vẽ 3D & Báo Giá Chi Tiết',
      'success_message' => 'Cảm ơn Quý cư dân! Kiến trúc sư trưởng IC HOUSE tại Sao Biển 23 Vinhomes Ocean Park đã tiếp nhận yêu cầu và sẽ liên hệ qua số điện thoại/Zalo trong vòng 15 phút.',
    ], $widget_id);
  }
}
