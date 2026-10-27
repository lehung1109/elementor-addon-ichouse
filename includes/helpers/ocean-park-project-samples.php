<?php
if (! defined('ABSPATH')) {
  exit;
}

if (! function_exists('eai_ocean_park_project_samples_resolve_post_ids')) {
  /**
   * @param array<string, mixed> $settings
   * @return array<int, int>
   */
  function eai_ocean_park_project_samples_resolve_post_ids(array $settings): array
  {
    $post_type = sanitize_key((string) ($settings['post_type'] ?? 'post')) ?: 'post';
    if (! eai_featured_projects_is_public_post_type($post_type)) {
      return [];
    }

    $dynamic_key = 'selected_posts_' . $post_type;
    $selected_posts = $settings[$dynamic_key] ?? $settings['selected_posts'] ?? [];
    $selected = array_values(array_unique(array_filter(array_map(
      'intval',
      (array) $selected_posts
    ))));
    if (empty($selected)) {
      return [];
    }

    $valid = get_posts([
      'post_type' => $post_type,
      'post_status' => 'publish',
      'post__in' => $selected,
      'posts_per_page' => -1,
      'orderby' => 'post__in',
      'fields' => 'ids',
    ]);
    $valid_map = array_fill_keys(array_map('intval', $valid), true);
    $resolved = array_values(array_filter(
      $selected,
      static fn(int $post_id): bool => isset($valid_map[$post_id])
    ));

    return array_slice($resolved, 0, 24);
  }
}

if (! function_exists('eai_ocean_park_project_samples_assigned_terms')) {
  /**
   * @return array<int, \WP_Term>
   */
  function eai_ocean_park_project_samples_assigned_terms(int $post_id, string $taxonomy): array
  {
    $taxonomy = sanitize_key($taxonomy);
    $taxonomy_object = $taxonomy !== '' ? get_taxonomy($taxonomy) : false;
    if ($post_id <= 0 || ! is_object($taxonomy_object) || empty($taxonomy_object->public)) {
      return [];
    }

    $post_type = get_post_type($post_id);
    if (! is_string($post_type) || $post_type === '' || ! is_object_in_taxonomy($post_type, $taxonomy)) {
      return [];
    }

    $terms = get_the_terms($post_id, $taxonomy);
    if (is_wp_error($terms) || ! is_array($terms)) {
      return [];
    }

    $terms = array_values(array_filter(
      $terms,
      static fn($term): bool => $term instanceof \WP_Term
        && ! eai_related_posts_is_excluded_term($term, $taxonomy)
    ));

    return eai_related_posts_sort_terms($terms);
  }
}

if (! function_exists('eai_ocean_park_project_samples_first_term')) {
  /**
   * @return array{slug: string, name: string}
   */
  function eai_ocean_park_project_samples_first_term(int $post_id, string $taxonomy): array
  {
    $terms = eai_ocean_park_project_samples_assigned_terms($post_id, $taxonomy);
    if (empty($terms)) {
      return ['slug' => '', 'name' => ''];
    }

    return [
      'slug' => trim((string) $terms[0]->slug),
      'name' => trim((string) $terms[0]->name),
    ];
  }
}

if (! function_exists('eai_ocean_park_project_samples_scalar_meta')) {
  function eai_ocean_park_project_samples_scalar_meta(
    int $post_id,
    string $taxonomy,
    string $acf_field_key
  ): string {
    $names = eai_project_category_gallery_assigned_term_names($post_id, $taxonomy);
    if (! empty($names)) {
      return implode(', ', $names);
    }

    if ($acf_field_key !== '' && function_exists('eai_job_listing_list_location')) {
      return eai_job_listing_list_location($post_id, $acf_field_key);
    }

    return '';
  }
}

if (! function_exists('eai_ocean_park_project_samples_summary')) {
  function eai_ocean_park_project_samples_summary(\WP_Post $post): string
  {
    $excerpt = trim((string) get_the_excerpt($post));
    if ($excerpt !== '') {
      return $excerpt;
    }

    if (function_exists('eai_job_listing_list_description')) {
      return eai_job_listing_list_description($post);
    }

    return '';
  }
}

if (! function_exists('eai_rc_map_ocean_park_project_sample_from_post')) {
  /**
   * @param array<string, mixed> $config
   * @return array<string, mixed>|null
   */
  function eai_rc_map_ocean_park_project_sample_from_post(int $post_id, array $config): ?array
  {
    $post = get_post($post_id);
    if (! $post instanceof \WP_Post || get_post_status($post) !== 'publish') {
      return null;
    }

    $thumbnail_id = (int) get_post_thumbnail_id($post);
    $permalink = trim((string) get_permalink($post));
    if ($thumbnail_id <= 0 || $permalink === '') {
      return null;
    }

    $image_size = array_key_exists('image_resolution', $config)
      ? sanitize_key((string) $config['image_resolution'])
      : 'large';
    if ($image_size === '') {
      $image_size = 'full';
    }
    $image = eai_rc_map_media_model(['id' => $thumbnail_id], [], null, $image_size);
    if (trim((string) ($image['url'] ?? '')) === '') {
      return null;
    }

    $zone = eai_ocean_park_project_samples_first_term(
      $post_id,
      (string) ($config['zone_taxonomy'] ?? '')
    );
    $unit_type = eai_ocean_park_project_samples_first_term(
      $post_id,
      (string) ($config['unit_type_taxonomy'] ?? '')
    );

    return [
      'title' => (string) get_the_title($post),
      'zoneId' => $zone['slug'] !== '' ? $zone['slug'] : 'all',
      'zoneLabel' => $zone['name'],
      'towerLabel' => eai_ocean_park_project_samples_scalar_meta(
        $post_id,
        (string) ($config['tower_taxonomy'] ?? ''),
        (string) ($config['tower_field'] ?? '')
      ),
      'unitTypeId' => $unit_type['slug'] !== '' ? $unit_type['slug'] : 'all',
      'unitTypeLabel' => $unit_type['name'],
      'area' => eai_ocean_park_project_samples_scalar_meta(
        $post_id,
        (string) ($config['area_taxonomy'] ?? ''),
        (string) ($config['area_field'] ?? '')
      ),
      'apartmentCode' => eai_ocean_park_project_samples_scalar_meta(
        $post_id,
        (string) ($config['apartment_code_taxonomy'] ?? ''),
        (string) ($config['apartment_code_field'] ?? '')
      ),
      'style' => eai_ocean_park_project_samples_scalar_meta(
        $post_id,
        (string) ($config['style_taxonomy'] ?? ''),
        (string) ($config['style_field'] ?? '')
      ),
      'budget' => eai_ocean_park_project_samples_scalar_meta(
        $post_id,
        (string) ($config['budget_taxonomy'] ?? ''),
        (string) ($config['budget_field'] ?? '')
      ),
      'summary' => eai_ocean_park_project_samples_summary($post),
      'image' => $image,
      'detailLink' => eai_rc_map_link(['url' => $permalink]),
      'isRealProject' => ($config['mark_as_real_project'] ?? true) === true,
    ];
  }
}

if (! function_exists('eai_ocean_park_project_samples_collect_filter_tabs')) {
  /**
   * @param array<int, array<string, mixed>> $items
   * @return array<int, array{id: string, label: string}>
   */
  function eai_ocean_park_project_samples_collect_filter_tabs(
    array $items,
    string $id_key,
    string $label_key,
    string $all_label
  ): array {
    $tabs = [
      ['id' => 'all', 'label' => $all_label],
    ];
    $seen = ['all' => true];

    foreach ($items as $item) {
      $id = trim((string) ($item[$id_key] ?? ''));
      $label = trim((string) ($item[$label_key] ?? ''));
      if ($id === '' || $label === '' || isset($seen[$id])) {
        continue;
      }
      $seen[$id] = true;
      $tabs[] = [
        'id' => $id,
        'label' => $label,
      ];
    }

    return $tabs;
  }
}

if (! function_exists('eai_ocean_park_project_samples_get_rc_props')) {
  /**
   * @param array<string, mixed> $settings
   * @return array<string, mixed>
   */
  function eai_ocean_park_project_samples_get_rc_props(array $settings): array
  {
    $image_resolution = array_key_exists('image_resolution', $settings)
      ? sanitize_key((string) $settings['image_resolution'])
      : 'large';
    $config = [
      'image_resolution' => $image_resolution,
      'zone_taxonomy' => sanitize_key((string) ($settings['zone_taxonomy'] ?? '')),
      'unit_type_taxonomy' => sanitize_key((string) ($settings['unit_type_taxonomy'] ?? '')),
      'tower_taxonomy' => sanitize_key((string) ($settings['tower_taxonomy'] ?? '')),
      'tower_field' => sanitize_key((string) ($settings['tower_field'] ?? '')),
      'area_taxonomy' => sanitize_key((string) ($settings['area_taxonomy'] ?? '')),
      'area_field' => sanitize_key((string) ($settings['area_field'] ?? '')),
      'apartment_code_taxonomy' => sanitize_key((string) ($settings['apartment_code_taxonomy'] ?? '')),
      'apartment_code_field' => sanitize_key((string) ($settings['apartment_code_field'] ?? '')),
      'style_taxonomy' => sanitize_key((string) ($settings['style_taxonomy'] ?? '')),
      'style_field' => sanitize_key((string) ($settings['style_field'] ?? '')),
      'budget_taxonomy' => sanitize_key((string) ($settings['budget_taxonomy'] ?? '')),
      'budget_field' => sanitize_key((string) ($settings['budget_field'] ?? '')),
      'mark_as_real_project' => ($settings['mark_as_real_project'] ?? 'yes') === 'yes',
    ];

    $post_ids = eai_ocean_park_project_samples_resolve_post_ids($settings);
    $featured_projects = [];
    foreach ($post_ids as $post_id) {
      $item = eai_rc_map_ocean_park_project_sample_from_post((int) $post_id, $config);
      if ($item !== null) {
        $featured_projects[] = $item;
      }
    }

    $all_zones_label = trim((string) ($settings['all_zones_label'] ?? '')) ?: 'Tất cả phân khu';
    $all_unit_types_label = trim((string) ($settings['all_unit_types_label'] ?? '')) ?: 'Tất cả loại căn';

    $cta_button_link = is_array($settings['cta_banner_button_link'] ?? null)
      ? $settings['cta_banner_button_link']
      : [];
    $props = [
      'eyebrow' => trim((string) ($settings['eyebrow'] ?? '')),
      'title' => trim((string) ($settings['title'] ?? '')),
      'description' => trim((string) ($settings['description'] ?? '')),
      'zoneTabs' => eai_ocean_park_project_samples_collect_filter_tabs(
        $featured_projects,
        'zoneId',
        'zoneLabel',
        $all_zones_label
      ),
      'unitTypeTabs' => eai_ocean_park_project_samples_collect_filter_tabs(
        $featured_projects,
        'unitTypeId',
        'unitTypeLabel',
        $all_unit_types_label
      ),
      'featuredProjects' => $featured_projects,
      'ctaBanner' => [
        'title' => trim((string) ($settings['cta_banner_title'] ?? '')),
        'description' => trim((string) ($settings['cta_banner_description'] ?? '')),
        'buttonLabel' => trim((string) ($settings['cta_banner_button_label'] ?? '')),
        'buttonLink' => eai_rc_map_link($cta_button_link),
      ],
    ];

    $class_name = trim((string) ($settings['class_name'] ?? ''));
    if ($class_name !== '') {
      $props['className'] = $class_name;
    }

    return $props;
  }
}

if (! function_exists('eai_ocean_park_project_samples_default_locked_entries')) {
  /**
   * Default locked project repeater rows appended to Ocean Park project samples.
   *
   * @return array<int, array<string, mixed>>
   */
  function eai_ocean_park_project_samples_default_locked_entries(): array
  {
    return [
      [
        'image'          => ['url' => 'https://noithat.ichouse.vn/wp-content/uploads/2026/05/Du-an-noi-that-can-ho-toa-H2-masteri-OCP-2.jpg', 'id' => ''],
        'title'          => 'Thiết kế & Thi công nội thất Căn góc 3PN The London Ocean Park 1 — Tòa LD2',
        'summary'        => 'Hồ sơ mặt bằng bố trí nội thất 2D, phối cảnh 3D Bespoke và bảng dự toán BOQ chi tiết căn góc 3PN Tòa LD2 phân khu The London Ocean Park 1 gia công tại xưởng IC HOUSE Như Quỳnh.',
        'link'           => ['url' => '#dang-ky-tu-van', 'is_external' => false, 'nofollow' => false],
        'zone_id'        => 'the-london',
        'zone_label'     => 'The London Ocean Park 1',
        'unit_type_id'   => '3pn',
        'unit_type_label'=> 'Căn góc 3PN',
        'tower_label'    => 'Tòa LD2',
        'area'           => '84.2m²',
        'apartment_code' => 'LD2-15',
        'style'          => 'Modern British Bespoke',
        'budget'         => '245 – 310 triệu',
        'is_locked'      => 'yes',
      ],
      [
        'image'          => ['url' => 'https://noithat.ichouse.vn/wp-content/uploads/2026/05/thiet-ke-noi-that-chung-cu-tone-den-trang-hm18_1.jpg', 'id' => ''],
        'title'          => 'Thiết kế & Thi công nội thất Căn hộ 3PN VIP LUMIÈRE SpringBay Ocean Park 2',
        'summary'        => 'Trọn bộ hồ sơ phối cảnh 3D, bản vẽ kỹ thuật hệ tủ cong kịch trần Low-E và bảng bóc tách khối lượng thi công thực tế căn hộ 3PN VIP LUMIÈRE SpringBay Ocean Park 2.',
        'link'           => ['url' => '#dang-ky-tu-van', 'is_external' => false, 'nofollow' => false],
        'zone_id'        => 'lumiere-springbay',
        'zone_label'     => 'LUMIÈRE SpringBay Ocean Park 2',
        'unit_type_id'   => '3pn',
        'unit_type_label'=> 'Căn góc 3PN Panorama',
        'tower_label'    => 'Tòa Feliz • Tòa Vida',
        'area'           => '96.8m²',
        'apartment_code' => 'LSB-18',
        'style'          => 'Quiet Luxury & Bespoke',
        'budget'         => '280 – 360 triệu',
        'is_locked'      => 'yes',
      ],
    ];
  }
}

if (! function_exists('eai_ocean_park_project_samples_prepare_manual_entries')) {
  /**
   * Append the 2 default locked entries when a saved repeater still has the legacy 7-item list without `is_locked` keys.
   *
   * @param array<int, mixed> $entries
   * @return array<int, mixed>
   */
  function eai_ocean_park_project_samples_prepare_manual_entries(array $entries): array
  {
    $has_is_locked_key = false;
    foreach ($entries as $entry) {
      if (is_array($entry) && array_key_exists('is_locked', $entry)) {
        $has_is_locked_key = true;
        break;
      }
    }
    if (count($entries) === 7 && ! $has_is_locked_key) {
      return array_merge($entries, eai_ocean_park_project_samples_default_locked_entries());
    }

    return $entries;
  }
}

if (! function_exists('eai_ocean_park_project_samples_get_editor_sample_props')) {
  /**
   * @param array<string, mixed> $settings
   * @return array<string, mixed>
   */
  function eai_ocean_park_project_samples_get_editor_sample_props(array $settings): array
  {
    $sample_rows = [
      [
        'title' => 'Thiết kế & Thi công nội thất Căn hộ The London Ocean Park 1 — Tòa LD1 • Tòa LD2 • Tòa LD3',
        'zoneId' => 'the-london',
        'zoneLabel' => 'The London Ocean Park 1',
        'towerLabel' => 'Tòa LD1 • Tòa LD2 • Tòa LD3',
        'unitTypeId' => '2pn',
        'unitTypeLabel' => 'Căn hộ 2PN',
        'area' => '62.8m²',
        'apartmentCode' => 'LD1-08',
        'style' => 'Modern British Luxury',
        'budget' => '185 – 230 triệu',
        'summary' => 'Phương án thiết kế thi công nội thất trọn gói Căn hộ The London tại Tòa LD1, Tòa LD2, Tòa LD3 sản xuất trực tiếp tại nhà xưởng IC HOUSE Như Quỳnh: hệ tủ bếp kịch trần MDF lõi xanh An Cường, tủ áo cánh kính khung nhôm Anode và bàn đảo đá thạch anh Vicostone.',
        'imageUrl' => 'https://noithat.ichouse.vn/wp-content/uploads/2026/05/Du-an-noi-that-can-ho-toa-H2-masteri-OCP-2.jpg',
        'isRealProject' => true,
      ],
      [
        'title' => 'Hoàn thiện nội thất Căn hộ The Beverly Ocean Park 1 — Tòa BE1 • Tòa BE2',
        'zoneId' => 'the-beverly',
        'zoneLabel' => 'The Beverly Ocean Park 1',
        'towerLabel' => 'Tòa BE1 • Tòa BE2',
        'unitTypeId' => '2pn1',
        'unitTypeLabel' => 'Căn hộ 2PN+1',
        'area' => '74.5m²',
        'apartmentCode' => 'BE1-12A',
        'style' => 'American Bespoke Luxury',
        'budget' => '210 – 260 triệu',
        'summary' => 'Thiết kế thi công nội thất Căn hộ The Beverly tại Tòa BE1 và Tòa BE2 giữ nguyên hệ bếp bàn giao cao cấp của chủ đầu tư (tiết kiệm 45 triệu), nâng cấp phòng khách liền bếp, tủ rượu cánh kính LED và không gian +1 làm việc sang trọng như tại Nhà mẫu Sao Biển 23.',
        'imageUrl' => 'https://noithat.ichouse.vn/wp-content/uploads/2026/05/DSC08300-3-HDR-scaled.jpg',
        'isRealProject' => true,
      ],
      [
        'title' => 'Thi công nội thất Căn hộ góc 3PN The Beverly Ocean Park 1 — Tòa BE3 • Tòa BE4',
        'zoneId' => 'the-beverly',
        'zoneLabel' => 'The Beverly Ocean Park 1',
        'towerLabel' => 'Tòa BE3 • Tòa BE4',
        'unitTypeId' => '3pn',
        'unitTypeLabel' => 'Căn góc 3PN VIP',
        'area' => '91.2m²',
        'apartmentCode' => 'BE3-05',
        'style' => 'Neoclassic Contemporary',
        'budget' => '260 – 340 triệu',
        'summary' => 'Hoàn thiện nội thất cao cấp căn góc 3PN tại Tòa BE3 và Tòa BE4 phân khu The Beverly Ocean Park 1 với bề mặt sơn Inchem 5 lớp gia công tại xưởng 2.000m² Như Quỳnh, vách ốp Veneer óc chó tự nhiên, đá Vicostone và phụ kiện giảm chấn Blum đồng bộ.',
        'imageUrl' => 'https://noithat.ichouse.vn/wp-content/uploads/2026/05/du-an-noi-that-nha-pho-100m2-nha-a-chi-gia-lam-ha-noi-4.jpg',
        'isRealProject' => true,
      ],
      [
        'title' => 'Thiết kế & Thi công nội thất The Paris Ocean Park 1 — Chung cư Ocean Park 1',
        'zoneId' => 'the-paris',
        'zoneLabel' => 'The Paris Ocean Park 1',
        'towerLabel' => 'PR1 • PR2 • PR3 • PR5',
        'unitTypeId' => '1pn1',
        'unitTypeLabel' => 'Căn hộ 1PN+1',
        'area' => '48.6m²',
        'apartmentCode' => 'PR2-15',
        'style' => 'Parisian Chic & Japandi',
        'budget' => '135 – 175 triệu',
        'summary' => 'Tinh thần Pháp thanh lịch kết hợp đường nét Japandi tối giản cho căn hộ 1PN+1 tại phân khu The Paris Ocean Park 1 (Chung cư Ocean Park 1), tối ưu hệ tủ âm tường kịch trần, bàn ăn thông minh và ánh sáng tự nhiên hướng hồ San Hô.',
        'imageUrl' => 'https://noithat.ichouse.vn/wp-content/uploads/2026/05/du-an-thiet-ke-noi-that-nha-pho-80m2-tai-lac-dao-hung-yen-15.jpg',
        'isRealProject' => false,
      ],
      [
        'title' => 'Thiết kế thi công nội thất Căn hộ LUMIÈRE SpringBay Ocean Park 2 — Chuẩn Masterise',
        'zoneId' => 'lumiere-springbay',
        'zoneLabel' => 'LUMIÈRE SpringBay Ocean Park 2',
        'towerLabel' => 'Vida • Feliz • Fira • Amor',
        'unitTypeId' => '2pn',
        'unitTypeLabel' => 'Căn hộ 2PN Luxury',
        'area' => '68.4m²',
        'apartmentCode' => 'LSB-09',
        'style' => 'Organic Modern Luxury',
        'budget' => '190 – 250 triệu',
        'summary' => 'May đo nội thất rời & hệ tủ âm tường chuẩn xác từng milimet từ nhà máy IC HOUSE Như Quỳnh (chỉ 10 phút vận chuyển sang Ocean Park 2), khớp nối hoàn hảo với hệ kính Low-E kịch trần cong của Căn hộ LUMIÈRE SpringBay.',
        'imageUrl' => 'https://noithat.ichouse.vn/wp-content/uploads/2026/05/thiet-ke-noi-that-chung-cu-tone-den-trang-hm18_1.jpg',
        'isRealProject' => true,
      ],
      [
        'title' => 'Thiết kế & Thi công nội thất Dự án Masteri Trinity Square Ocean Park 2',
        'zoneId' => 'masteri-trinity-square',
        'zoneLabel' => 'Masteri Trinity Square Ocean Park 2',
        'towerLabel' => 'Southern • Central • Northern',
        'unitTypeId' => 'duplex',
        'unitTypeLabel' => 'Duplex / Ghép căn',
        'area' => '112.0m²',
        'apartmentCode' => 'MTS-18',
        'style' => 'Bespoke Penthouse Luxury',
        'budget' => '320 – 450 triệu',
        'summary' => 'Không gian thông tầng & ghép căn đẳng cấp tại Dự án Masteri Trinity Square (Masteri Trinity Square Ocean Park 2) với đảo bếp đá thạch anh Vicostone nguyên khối, vách tivi ốp đá Porcelain khổ lớn, sofa da bò Ý và hệ đèn ray nam châm CRI>90.',
        'imageUrl' => 'https://noithat.ichouse.vn/wp-content/uploads/2026/06/Phong-khach-su-dung-gach-Porcelain-kho-lon.png',
        'isRealProject' => false,
      ],
      [
        'title' => 'Mẫu thiết kế thi công nội thất Căn hộ cao tầng Ocean Park 3 — Dự án căn hộ Ocean Park 3',
        'zoneId' => 'ocean-park-3',
        'zoneLabel' => 'Ocean Park 3',
        'towerLabel' => 'Phân khu Cao tầng OCP3',
        'unitTypeId' => 'studio',
        'unitTypeLabel' => 'Studio / 1PN Resort',
        'area' => '36.5m²',
        'apartmentCode' => 'OCP3-06',
        'style' => 'Modern Resort & Wabi Sabi',
        'budget' => '120 – 150 triệu',
        'summary' => 'Giải pháp thiết kế nội thất nghỉ dưỡng thông minh cho Căn hộ cao tầng Ocean Park 3 thuộc Dự án căn hộ Ocean Park 3, sản xuất trực tiếp tại xưởng IC HOUSE giáp ranh dự án, tối ưu công năng ở thực lẫn cho thuê Homestay cao cấp.',
        'imageUrl' => 'https://noithat.ichouse.vn/wp-content/uploads/2026/05/du-an-noi-that-nha-pho-45m2-Gia-Lam-10.jpg',
        'isRealProject' => false,
      ],
      [
        'title' => 'Thiết kế & Thi công nội thất Căn góc 3PN The London Ocean Park 1 — Tòa LD2',
        'zoneId' => 'the-london',
        'zoneLabel' => 'The London Ocean Park 1',
        'towerLabel' => 'Tòa LD2',
        'unitTypeId' => '3pn',
        'unitTypeLabel' => 'Căn góc 3PN',
        'area' => '84.2m²',
        'apartmentCode' => 'LD2-15',
        'style' => 'Modern British Bespoke',
        'budget' => '245 – 310 triệu',
        'summary' => 'Hồ sơ mặt bằng bố trí nội thất 2D, phối cảnh 3D Bespoke và bảng dự toán BOQ chi tiết căn góc 3PN Tòa LD2 phân khu The London Ocean Park 1 gia công tại xưởng IC HOUSE Như Quỳnh.',
        'imageUrl' => 'https://noithat.ichouse.vn/wp-content/uploads/2026/05/Du-an-noi-that-can-ho-toa-H2-masteri-OCP-2.jpg',
        'isRealProject' => true,
        'isLocked' => true,
      ],
      [
        'title' => 'Thiết kế & Thi công nội thất Căn hộ 3PN VIP LUMIÈRE SpringBay Ocean Park 2',
        'zoneId' => 'lumiere-springbay',
        'zoneLabel' => 'LUMIÈRE SpringBay Ocean Park 2',
        'towerLabel' => 'Tòa Feliz • Tòa Vida',
        'unitTypeId' => '3pn',
        'unitTypeLabel' => 'Căn góc 3PN Panorama',
        'area' => '96.8m²',
        'apartmentCode' => 'LSB-18',
        'style' => 'Quiet Luxury & Bespoke',
        'budget' => '280 – 360 triệu',
        'summary' => 'Trọn bộ hồ sơ phối cảnh 3D, bản vẽ kỹ thuật hệ tủ cong kịch trần Low-E và bảng bóc tách khối lượng thi công thực tế căn hộ 3PN VIP LUMIÈRE SpringBay Ocean Park 2.',
        'imageUrl' => 'https://noithat.ichouse.vn/wp-content/uploads/2026/05/thiet-ke-noi-that-chung-cu-tone-den-trang-hm18_1.jpg',
        'isRealProject' => true,
        'isLocked' => true,
      ],
    ];

    $featured_projects = [];
    foreach ($sample_rows as $row) {
      $image_url = $row['imageUrl'];
      $is_locked = ($row['isLocked'] ?? false) === true;
      unset($row['imageUrl']);
      $featured_projects[] = array_merge($row, [
        'image' => [
          'url' => $image_url,
          'alt' => $row['title'],
          'display_dimensions' => ['width' => 800, 'height' => 500],
        ],
        'detailLink' => eai_rc_map_link(['url' => '#dang-ky-tu-van']),
        'isLocked' => $is_locked,
      ]);
    }

    $eyebrow = trim((string) ($settings['eyebrow'] ?? ''));
    if ($eyebrow === '' || $eyebrow === 'THƯ VIỆN MẪU THIẾT KẾ & DỰ ÁN THỰC TẾ') {
      $eyebrow = 'THƯ VIỆN MẪU THIẾT KẾ & CÔNG TRÌNH THI CÔNG THỰC TẾ TẠI VINHOMES OCEAN PARK';
    }

    $title = trim((string) ($settings['title'] ?? ''));
    if ($title === '' || $title === 'Mẫu Thiết Kế Nội Thất Cao Cấp Theo Từng Phân Khu & Tòa Căn Hộ Ocean Park 1 • 2 • 3') {
      $title = 'Mẫu Thiết Kế & Thi Công Nội Thất Chung Cư Vinhomes Ocean Park 1 • 2 • 3 Theo Từng Phân Khu';
    }

    $description = trim((string) ($settings['description'] ?? ''));
    if ($description === '' || str_starts_with($description, 'Khám phá bộ sưu tập mặt bằng công năng và phối cảnh 3D Bespoke được KTS IC HOUSE nghiên cứu riêng')) {
      $description = 'Khám phá bộ sưu tập mặt bằng bố trí nội thất 2D, phối cảnh 3D Bespoke và công trình thực tế do đội ngũ KTS IC HOUSE (Văn phòng Số 01 Voi Phục – Nhà mẫu Sao Biển 23 – Nhà xưởng 2.000m² Như Quỳnh) thiết kế thi công trọn gói cho từng mã căn hộ tại Chung cư Ocean Park 1 (The London, The Beverly, The Paris), Ocean Park 2 (LUMIÈRE SpringBay, Masteri Trinity Square) và Dự án căn hộ Ocean Park 3.';
    }

    $cta_title = trim((string) ($settings['cta_banner_title'] ?? ''));
    if ($cta_title === '' || $cta_title === 'Bạn cần mặt bằng bố trí 2D & phối cảnh 3D đúng mã căn hộ của mình?') {
      $cta_title = 'Bạn muốn xem mặt bằng 2D, phối cảnh 3D đúng mã căn & tham quan trực tiếp Nhà mẫu tại Sao Biển 23?';
    }

    $cta_desc = trim((string) ($settings['cta_banner_description'] ?? ''));
    if ($cta_desc === '' || str_starts_with($cta_desc, 'IC HOUSE đã lưu trữ sẵn mặt bằng kỹ thuật toàn bộ các tòa')) {
      $cta_desc = 'Văn phòng KTS IC HOUSE tại Số 01 Voi Phục Gia Lâm đã lưu trữ sẵn mặt bằng kỹ thuật toàn bộ các tòa LD1, LD2, LD3 (The London), BE1–BE4 (The Beverly), The Paris, LUMIÈRE SpringBay, Masteri Trinity Square và Ocean Park 3. Đăng ký nhận bản vẽ miễn phí và đặt lịch xem Nhà mẫu ngay trong KĐT!';
    }

    $cta_btn = trim((string) ($settings['cta_banner_button_label'] ?? ''));
    if ($cta_btn === '' || $cta_btn === 'Nhận bản vẽ theo mã căn') {
      $cta_btn = 'Nhận bản vẽ theo mã căn & Xem nhà mẫu';
    }

    $props = [
      'eyebrow' => $eyebrow,
      'title' => $title,
      'description' => $description,
      'zoneTabs' => [
        ['id' => 'all', 'label' => 'Tất cả phân khu'],
        ['id' => 'the-london', 'label' => 'The London (LD1 • LD2 • LD3)'],
        ['id' => 'the-beverly', 'label' => 'The Beverly (BE1 • BE2 • BE3 • BE4)'],
        ['id' => 'the-paris', 'label' => 'The Paris Ocean Park 1'],
        ['id' => 'lumiere-springbay', 'label' => 'LUMIÈRE SpringBay OCP2'],
        ['id' => 'masteri-trinity-square', 'label' => 'Masteri Trinity Square OCP2'],
        ['id' => 'ocean-park-3', 'label' => 'Cao tầng Ocean Park 3'],
      ],
      'unitTypeTabs' => [
        ['id' => 'all', 'label' => 'Tất cả loại căn'],
        ['id' => 'studio', 'label' => 'Studio / 1PN'],
        ['id' => '1pn1', 'label' => '1PN+1'],
        ['id' => '2pn', 'label' => '2PN'],
        ['id' => '2pn1', 'label' => '2PN+1'],
        ['id' => '3pn', 'label' => '3PN / Căn góc'],
        ['id' => 'duplex', 'label' => 'Duplex / Penthouse'],
      ],
      'featuredProjects' => $featured_projects,
      'ctaBanner' => [
        'title' => $cta_title,
        'description' => $cta_desc,
        'buttonLabel' => $cta_btn,
        'buttonLink' => eai_rc_map_link(
          is_array($settings['cta_banner_button_link'] ?? null)
            ? $settings['cta_banner_button_link']
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

if (! function_exists('eai_ajax_ocean_park_project_samples_search_posts')) {
  function eai_ajax_ocean_park_project_samples_search_posts(): void
  {
    $post_type = sanitize_key((string) ($_REQUEST['post_type'] ?? ''));
    eai_featured_projects_verify_editor_ajax($post_type);

    if (! eai_featured_projects_is_public_post_type($post_type)) {
      wp_send_json(['results' => []]);
    }

    $search = isset($_REQUEST['q']) ? sanitize_text_field(wp_unslash($_REQUEST['q'])) : '';
    $ids = [];
    if (isset($_REQUEST['ids'])) {
      $raw_ids = wp_unslash($_REQUEST['ids']);
      $ids = is_array($raw_ids)
        ? array_map('intval', $raw_ids)
        : array_map('intval', explode(',', (string) $raw_ids));
      $ids = array_values(array_unique(array_filter($ids)));
    }

    if (! empty($ids)) {
      $posts = get_posts([
        'post_type' => $post_type,
        'post_status' => 'publish',
        'post__in' => array_slice($ids, 0, 24),
        'posts_per_page' => 24,
        'orderby' => 'post__in',
      ]);
    } else {
      $query_args = [
        'post_type' => $post_type,
        'post_status' => 'publish',
        'posts_per_page' => 20,
        'orderby' => 'title',
        'order' => 'ASC',
      ];
      if ($search !== '') {
        if (ctype_digit($search)) {
          $query_args['p'] = (int) $search;
          unset($query_args['orderby'], $query_args['order']);
        } else {
          $query_args['s'] = $search;
        }
      }
      $posts = get_posts($query_args);
    }

    $results = [];
    foreach ($posts as $post) {
      if (! $post instanceof \WP_Post) {
        continue;
      }
      $results[] = ['id' => (string) $post->ID, 'text' => (string) $post->post_title];
    }

    wp_send_json(['results' => $results]);
  }
}

add_action('wp_ajax_eai_ocean_park_project_samples_search_posts', 'eai_ajax_ocean_park_project_samples_search_posts');
