<?php
if (! defined('ABSPATH')) {
  exit;
}

if (! function_exists('eai_collaboration_intro_get_target_id')) {
  function eai_collaboration_intro_get_target_id(string $widget_id): string
  {
    $safe_widget_id = sanitize_html_class($widget_id);
    return 'collaboration-intro-' . ($safe_widget_id !== '' ? $safe_widget_id : 'widget');
  }
}

if (! function_exists('eai_collaboration_intro_map_media')) {
  /** @param array<string, mixed> $settings */
  function eai_collaboration_intro_map_media(array $settings, string $field, string $default_size): array
  {
    $media = is_array($settings[$field] ?? null) ? $settings[$field] : [];
    $size = (string) ($settings[$field . '_resolution'] ?? $default_size);
    $model = eai_rc_map_media_model($media, [], null, $size);
    $alt = trim((string) ($settings[$field . '_alt'] ?? ''));

    if ($alt !== '') {
      $model['alt'] = $alt;
    }

    return $model;
  }
}

if (! function_exists('eai_collaboration_intro_map_items')) {
  /** @param array<int, mixed> $rows */
  function eai_collaboration_intro_map_items(array $rows): array
  {
    $items = [];

    foreach ($rows as $row) {
      if (! is_array($row)) {
        continue;
      }

      $title = trim((string) ($row['title'] ?? ''));
      $image = eai_collaboration_intro_map_media($row, 'image', 'thumbnail');

      if ($title === '' || trim((string) ($image['url'] ?? '')) === '') {
        continue;
      }

      $items[] = ['image' => $image, 'title' => $title];
    }

    return $items;
  }
}

if (! function_exists('eai_collaboration_intro_get_rc_props')) {
  /** @param array<string, mixed> $settings */
  function eai_collaboration_intro_get_rc_props(array $settings, string $widget_id): array
  {
    $background = eai_collaboration_intro_map_media($settings, 'background_image', 'large');
    $image = eai_collaboration_intro_map_media($settings, 'image', 'large');
    $button_link = is_array($settings['button_link'] ?? null) ? $settings['button_link'] : [];
    $rows = is_array($settings['items'] ?? null) ? $settings['items'] : [];
    $class_name = trim((string) ($settings['class_name'] ?? ''));
    $popup_target = eai_normalize_contact_popup_key((string) ($settings['popup_target'] ?? ''));

    $props = [
      'subtitle' => (string) ($settings['subtitle'] ?? ''),
      'titleHtml' => (string) ($settings['title_html'] ?? ''),
      'image' => $image,
      'bottomTitle' => (string) ($settings['bottom_title'] ?? ''),
      'items' => eai_collaboration_intro_map_items($rows),
      'note' => (string) ($settings['note'] ?? ''),
      'buttonLabel' => (string) ($settings['button_label'] ?? ''),
      'buttonLink' => eai_rc_map_link($button_link),
      'scrollReveal' => ['targetId' => eai_collaboration_intro_get_target_id($widget_id)],
    ];

    if ($popup_target !== '') {
      $props['popupTarget'] = $popup_target;
    }

    if (trim((string) ($background['url'] ?? '')) !== '') {
      $props['backgroundImage'] = $background;
    }
    if ($class_name !== '') {
      $props['className'] = $class_name;
    }

    return $props;
  }
}

if (! function_exists('eai_collaboration_intro_props_are_empty')) {
  /** @param array<string, mixed> $props */
  function eai_collaboration_intro_props_are_empty(array $props): bool
  {
    $background_url = is_array($props['backgroundImage'] ?? null)
      ? trim((string) ($props['backgroundImage']['url'] ?? ''))
      : '';
    $image_url = is_array($props['image'] ?? null)
      ? trim((string) ($props['image']['url'] ?? ''))
      : '';
    $button_link = is_array($props['buttonLink'] ?? null) ? $props['buttonLink'] : [];
    $popup_target = (string) ($props['popupTarget'] ?? '');
    $has_button = trim((string) ($props['buttonLabel'] ?? '')) !== ''
      && (trim((string) ($button_link['url'] ?? '')) !== '' || $popup_target !== '');

    return $background_url === ''
      && trim((string) ($props['subtitle'] ?? '')) === ''
      && trim((string) ($props['titleHtml'] ?? '')) === ''
      && $image_url === ''
      && trim((string) ($props['bottomTitle'] ?? '')) === ''
      && empty($props['items'])
      && trim((string) ($props['note'] ?? '')) === ''
      && ! $has_button;
  }
}

if (! function_exists('eai_collaboration_intro_get_editor_sample_props')) {
  /** @param array<string, mixed> $settings */
  function eai_collaboration_intro_get_editor_sample_props(array $settings, string $widget_id): array
  {
    $item = static function (string $text, string $title): array {
      return [
        'image' => [
          'url' => 'https://placehold.co/80x80/png?text=' . $text,
          'alt' => '',
          'display_dimensions' => ['width' => 80, 'height' => 80],
        ],
        'title' => $title,
      ];
    };

    $props = [
      'backgroundImage' => [
        'url' => 'https://placehold.co/1920x1080/png?text=Collaboration+Background',
        'alt' => '',
        'display_dimensions' => ['width' => 1920, 'height' => 1080],
      ],
      'subtitle' => 'GIỚI THIỆU & ĐỊNH HƯỚNG HỢP TÁC',
      'titleHtml' => 'ICHOUSE là <span class="text-brand-gold">Tổng thầu Thiết kế và Thi công</span> chuyên nghiệp, cung cấp dịch vụ thiết kế và thi công trọn gói cho các công trình trên toàn quốc.',
      'image' => [
        'url' => 'https://placehold.co/640x480/png?text=Team+Review',
        'alt' => 'Đội ngũ ICHOUSE khảo sát vật liệu thi công',
        'display_dimensions' => ['width' => 640, 'height' => 480],
      ],
      'bottomTitle' => 'Chúng tôi tìm kiếm những đối tác có năng lực, trách nhiệm và tinh thần hợp tác trong các lĩnh vực:',
      'items' => [
        $item('Design', 'Thiết kế kiến trúc'),
        $item('Build', 'Thi công chuyên môn sâu'),
        $item('Supply', 'Cung ứng vật tư, thiết bị'),
        $item('Tech', 'Cung cấp giải pháp công nghệ, kỹ thuật'),
        $item('Media', 'Truyền thông, marketing và sản xuất nội dung'),
      ],
      'note' => 'Trước khi gửi hồ sơ, vui lòng tham khảo các tiêu chí và quy trình hợp tác dưới đây để đảm bảo sự phù hợp, hiệu quả trong quá trình làm việc.',
      'buttonLabel' => 'TRỞ THÀNH ĐỐI TÁC ICHOUSE!',
      'popupTarget' => 'tu-van',
      'scrollReveal' => ['targetId' => eai_collaboration_intro_get_target_id($widget_id)],
    ];

    $class_name = trim((string) ($settings['class_name'] ?? ''));
    if ($class_name !== '') {
      $props['className'] = $class_name;
    }

    return $props;
  }
}
