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

if (! function_exists('eai_ocean_park_project_samples_get_editor_sample_props')) {
  /**
   * @param array<string, mixed> $settings
   * @return array<string, mixed>
   */
  function eai_ocean_park_project_samples_get_editor_sample_props(array $settings): array
  {
    $sample_rows = [
      ['title' => 'Thiết kế nội thất Căn hộ The London Ocean Park 1 — Tòa LD1 • Tòa LD2 • Tòa LD3', 'zoneId' => 'the-london', 'zoneLabel' => 'The London Ocean Park 1', 'towerLabel' => 'Tòa LD1 • Tòa LD2 • Tòa LD3', 'unitTypeId' => '2pn', 'unitTypeLabel' => 'Căn hộ 2PN', 'area' => '62.8m²', 'apartmentCode' => 'LD1-08', 'style' => 'Modern British Luxury', 'budget' => '185 – 230 triệu', 'summary' => 'Giải pháp bố trí nội thất may đo cho Căn hộ The London tại Tòa LD1, Tòa LD2, Tòa LD3.', 'isRealProject' => true],
      ['title' => 'Hoàn thiện nội thất Căn hộ The Beverly Ocean Park 1 — Tòa BE1 • Tòa BE2', 'zoneId' => 'the-beverly', 'zoneLabel' => 'The Beverly Ocean Park 1', 'towerLabel' => 'Tòa BE1 • Tòa BE2', 'unitTypeId' => '2pn1', 'unitTypeLabel' => 'Căn hộ 2PN+1', 'area' => '74.5m²', 'apartmentCode' => 'BE1-12A', 'style' => 'American Bespoke Luxury', 'budget' => '210 – 260 triệu', 'summary' => 'Thiết kế thi công trọn gói Căn hộ The Beverly tại Tòa BE1 và Tòa BE2.', 'isRealProject' => true],
      ['title' => 'Nội thất Căn hộ góc 3PN The Beverly Ocean Park 1 — Tòa BE3 • Tòa BE4', 'zoneId' => 'the-beverly', 'zoneLabel' => 'The Beverly Ocean Park 1', 'towerLabel' => 'Tòa BE3 • Tòa BE4', 'unitTypeId' => '3pn', 'unitTypeLabel' => 'Căn góc 3PN VIP', 'area' => '91.2m²', 'apartmentCode' => 'BE3-05', 'style' => 'Neoclassic Contemporary', 'budget' => '260 – 340 triệu', 'summary' => 'Phương án hoàn thiện cao cấp cho căn góc 3PN tại Tòa BE3 và Tòa BE4.', 'isRealProject' => true],
      ['title' => 'Thiết kế nội thất The Paris Ocean Park 1 — Chung cư Ocean Park 1', 'zoneId' => 'the-paris', 'zoneLabel' => 'The Paris Ocean Park 1', 'towerLabel' => 'PR1 • PR2 • PR3 • PR5', 'unitTypeId' => '1pn1', 'unitTypeLabel' => 'Căn hộ 1PN+1', 'area' => '48.6m²', 'apartmentCode' => 'PR2-15', 'style' => 'Parisian Chic & Japandi', 'budget' => '135 – 175 triệu', 'summary' => 'Tinh thần Pháp thanh lịch kết hợp đường nét tối giản hiện đại cho căn hộ 1PN+1.', 'isRealProject' => false],
      ['title' => 'Thiết kế nội thất Căn hộ LUMIÈRE SpringBay Ocean Park 2 — Chuẩn Masterise', 'zoneId' => 'lumiere-springbay', 'zoneLabel' => 'LUMIÈRE SpringBay Ocean Park 2', 'towerLabel' => 'Vida • Feliz • Fira • Amor', 'unitTypeId' => '2pn', 'unitTypeLabel' => 'Căn hộ 2PN Luxury', 'area' => '68.4m²', 'apartmentCode' => 'LSB-09', 'style' => 'Organic Modern Luxury', 'budget' => '190 – 250 triệu', 'summary' => 'May đo nội thất rời & hệ tủ âm tường khớp nối hoàn hảo với tiêu chuẩn bàn giao kính Low-E.', 'isRealProject' => true],
      ['title' => 'Thiết kế & Thi công Dự án Masteri Trinity Square Ocean Park 2', 'zoneId' => 'masteri-trinity-square', 'zoneLabel' => 'Masteri Trinity Square Ocean Park 2', 'towerLabel' => 'Southern • Central • Northern', 'unitTypeId' => 'duplex', 'unitTypeLabel' => 'Duplex / Ghép căn', 'area' => '112.0m²', 'apartmentCode' => 'MTS-18', 'style' => 'Bespoke Penthouse Luxury', 'budget' => '320 – 450 triệu', 'summary' => 'Không gian thông tầng đẳng cấp tại Dự án Masteri Trinity Square.', 'isRealProject' => false],
      ['title' => 'Mẫu nội thất Căn hộ cao tầng Ocean Park 3 — Dự án căn hộ Ocean Park 3', 'zoneId' => 'ocean-park-3', 'zoneLabel' => 'Ocean Park 3', 'towerLabel' => 'Phân khu Cao tầng OCP3', 'unitTypeId' => 'studio', 'unitTypeLabel' => 'Studio / 1PN Resort', 'area' => '36.5m²', 'apartmentCode' => 'OCP3-06', 'style' => 'Modern Resort & Wabi Sabi', 'budget' => '120 – 150 triệu', 'summary' => 'Giải pháp nội thất nghỉ dưỡng thông minh cho Căn hộ cao tầng Ocean Park 3.', 'isRealProject' => false],
    ];

    $featured_projects = [];
    foreach ($sample_rows as $index => $row) {
      $featured_projects[] = array_merge($row, [
        'image' => [
          'url' => 'https://placehold.co/800x500/152243/f5a623/png?text=Ocean+Park+' . ($index + 1),
          'alt' => $row['title'],
          'display_dimensions' => ['width' => 800, 'height' => 500],
        ],
        'detailLink' => eai_rc_map_link(['url' => '#dang-ky-tu-van']),
      ]);
    }

    $props = [
      'eyebrow' => trim((string) ($settings['eyebrow'] ?? '')) ?: 'THƯ VIỆN MẪU THIẾT KẾ & DỰ ÁN THỰC TẾ',
      'title' => trim((string) ($settings['title'] ?? '')) ?: 'Mẫu Thiết Kế Nội Thất Cao Cấp Theo Từng Phân Khu & Tòa Căn Hộ Ocean Park 1 • 2 • 3',
      'description' => trim((string) ($settings['description'] ?? '')) ?: 'Khám phá bộ sưu tập mặt bằng công năng và phối cảnh 3D Bespoke được KTS IC HOUSE nghiên cứu riêng cho từng mã căn hộ tại Chung cư Ocean Park 1, Ocean Park 2 và Ocean Park 3.',
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
        'title' => trim((string) ($settings['cta_banner_title'] ?? '')) ?: 'Bạn cần mặt bằng bố trí 2D & phối cảnh 3D đúng mã căn hộ của mình?',
        'description' => trim((string) ($settings['cta_banner_description'] ?? '')) ?: 'IC HOUSE đã lưu trữ sẵn mặt bằng kỹ thuật toàn bộ các tòa LD1, LD2, LD3 (The London), BE1–BE4 (The Beverly), The Paris, LUMIÈRE SpringBay, Masteri Trinity Square và Ocean Park 3.',
        'buttonLabel' => trim((string) ($settings['cta_banner_button_label'] ?? '')) ?: 'Nhận bản vẽ theo mã căn',
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
