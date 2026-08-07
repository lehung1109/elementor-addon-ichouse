<?php
if (! defined('ABSPATH')) {
  exit;
}

if (! function_exists('eai_rc_map_director_profile_items')) {
  /**
   * @param array<int, array<string, mixed>> $rows
   * @return array<int, array{title: string, description: string}>
   */
  function eai_rc_map_director_profile_items(array $rows): array
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

if (! function_exists('eai_director_profile_get_target_id')) {
  function eai_director_profile_get_target_id(string $widget_id): string
  {
    $safe_widget_id = sanitize_html_class($widget_id);

    return 'director-profile-' . ($safe_widget_id !== '' ? $safe_widget_id : 'widget');
  }
}

if (! function_exists('eai_director_profile_get_rc_props')) {
  /**
   * @param array<string, mixed> $settings
   * @return array<string, mixed>
   */
  function eai_director_profile_get_rc_props(array $settings, string $widget_id = ''): array
  {
    $background_mobile_image = is_array($settings['background_mobile_image'] ?? null)
      ? $settings['background_mobile_image']
      : [];
    $background_desktop_image = is_array($settings['background_desktop_image'] ?? null)
      ? $settings['background_desktop_image']
      : [];
    $background_mobile_resolution = (string) ($settings['background_mobile_image_resolution'] ?? 'large');
    $background_desktop_resolution = (string) ($settings['background_desktop_image_resolution'] ?? 'large');
    $items = is_array($settings['items'] ?? null) ? $settings['items'] : [];
    $class_name = trim((string) ($settings['class_name'] ?? ''));
    $props = [
      'backgroundMobileImage' => eai_rc_map_media_model(
        $background_mobile_image,
        [],
        null,
        $background_mobile_resolution
      ),
      'backgroundDesktopImage' => eai_rc_map_media_model(
        $background_desktop_image,
        [],
        null,
        $background_desktop_resolution
      ),
      'subtitle' => trim((string) ($settings['subtitle'] ?? '')),
      'descriptionHtml' => trim((string) ($settings['description_html'] ?? '')),
      'items' => eai_rc_map_director_profile_items($items),
      'scrollReveal' => [
        'targetId' => eai_director_profile_get_target_id($widget_id),
      ],
    ];

    if ($class_name !== '') {
      $props['className'] = $class_name;
    }

    return $props;
  }
}

if (! function_exists('eai_director_profile_has_content')) {
  /** @param array<string, mixed> $props */
  function eai_director_profile_has_content(array $props): bool
  {
    return trim((string) ($props['backgroundMobileImage']['url'] ?? '')) !== ''
      || trim((string) ($props['backgroundDesktopImage']['url'] ?? '')) !== ''
      || trim((string) ($props['subtitle'] ?? '')) !== ''
      || trim((string) ($props['descriptionHtml'] ?? '')) !== ''
      || ! empty($props['items']);
  }
}

if (! function_exists('eai_director_profile_get_editor_sample_props')) {
  /**
   * @param array<string, mixed> $settings
   * @return array<string, mixed>
   */
  function eai_director_profile_get_editor_sample_props(array $settings, string $widget_id = ''): array
  {
    $props = [
      'backgroundMobileImage' => [
        'url' => 'https://placehold.co/768x1024/152243/ffffff?text=Director+BG+Mobile',
        'alt' => 'Nền hồ sơ giám đốc mobile',
        'display_dimensions' => ['width' => 768, 'height' => 1024],
      ],
      'backgroundDesktopImage' => [
        'url' => 'https://placehold.co/1920x1080/152243/ffffff?text=Director+BG+Desktop',
        'alt' => 'Nền hồ sơ giám đốc desktop',
        'display_dimensions' => ['width' => 1920, 'height' => 1080],
        'srcSet' => 'https://placehold.co/1280x800/152243/ffffff?text=Director+BG+Tablet 1280w, https://placehold.co/1920x1080/152243/ffffff?text=Director+BG+Desktop 1920w',
        'sizes' => '100vw',
      ],
      'subtitle' => 'GIÁM ĐỐC - TS. NGUYỄN ĐĂNG HẠNH',
      'descriptionHtml' => '<p>ICHOUSE ra đời với mong muốn thay đổi cách thức <span class="text-brand-gold">xây dựng</span> và quy chuẩn về một công trình chất lượng của người Việt. Với sứ mệnh kiến tạo những không gian sống bền vững, chúng tôi mang đến giải pháp thiết kế và thi công đồng bộ, chuẩn mực Châu Âu.</p>',
      'items' => eai_rc_map_director_profile_items([
        ['title' => 'Giảng viên', 'description' => 'ngành Kỹ thuật xây dựng tại trường ĐH Xây dựng Caen (Pháp) từ năm 2008 đến năm 2014'],
        ['title' => 'Hơn 15 năm', 'description' => 'kinh nghiệm quản lý và điều hành các dự án xây dựng dân dụng cao cấp tại Việt Nam'],
        ['title' => 'Tốt nghiệp Tiến sỹ', 'description' => 'ngành Kỹ thuật xây dựng tại Đại học Caen (Pháp), chuyên sâu kết cấu và vật liệu'],
        ['title' => 'Chủ tịch HĐQT', 'description' => 'công ty ICHOUSE — định hướng chiến lược phát triển và chuẩn hóa quy trình thi công'],
        ['title' => 'Tác giả', 'description' => 'nhiều công trình nghiên cứu và bài báo khoa học về kỹ thuật xây dựng bền vững'],
      ]),
      'scrollReveal' => ['targetId' => eai_director_profile_get_target_id($widget_id)],
    ];

    $class_name = trim((string) ($settings['class_name'] ?? ''));
    if ($class_name !== '') {
      $props['className'] = $class_name;
    }

    return $props;
  }
}
