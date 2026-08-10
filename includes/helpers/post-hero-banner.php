<?php
if (! defined('ABSPATH')) {
  exit;
}

if (! function_exists('eai_post_hero_banner_normalize_acf_image')) {
  /** @return array<string, mixed> */
  function eai_post_hero_banner_normalize_acf_image(mixed $value): array
  {
    if (is_numeric($value) && (int) $value > 0) {
      return ['id' => (int) $value];
    }

    if (is_array($value)) {
      $id = (int) ($value['ID'] ?? $value['id'] ?? 0);
      if ($id > 0) {
        return ['id' => $id];
      }

      $url = trim((string) ($value['url'] ?? ''));
      if ($url !== '') {
        return [
          'url' => $url,
          'alt' => trim((string) ($value['alt'] ?? '')),
        ];
      }
    }

    if (is_string($value) && trim($value) !== '') {
      return ['url' => trim($value)];
    }

    return [];
  }
}

if (! function_exists('eai_post_hero_banner_get_rc_props')) {
  /** @param array<string, mixed> $settings @return array<string, mixed> */
  function eai_post_hero_banner_get_rc_props(int $post_id, array $settings): array
  {
    $field_key = trim((string) ($settings['acf_image_field'] ?? ''));
    $field = $post_id > 0 && $field_key !== '' && function_exists('get_field_object')
      ? get_field_object($field_key, $post_id, true, true)
      : false;
    $media = is_array($field) && ($field['type'] ?? '') === 'image'
      ? eai_post_hero_banner_normalize_acf_image($field['value'] ?? null)
      : [];
    $image_size = trim((string) ($settings['image_size'] ?? 'full')) ?: 'full';
    $post_type = $post_id > 0 ? get_post_type($post_id) : false;
    $post_type_object = is_string($post_type) && $post_type !== ''
      ? get_post_type_object($post_type)
      : null;
    $archive_url = is_string($post_type) && $post_type !== ''
      ? get_post_type_archive_link($post_type)
      : false;
    $archive_label = is_object($post_type_object)
      ? trim((string) ($post_type_object->labels->name ?? $post_type_object->labels->singular_name ?? ''))
      : '';
    $home_label = trim((string) ($settings['home_label'] ?? 'Trang chủ')) ?: 'Trang chủ';

    $props = [
      'backgroundImage' => eai_rc_map_media_model($media, [], null, $image_size),
      'breadcrumbItems' => [
        [
          'label' => $home_label,
          'link' => eai_rc_map_link(['url' => home_url('/')]),
        ],
        [
          'label' => $archive_label,
          'link' => eai_rc_map_link(['url' => is_string($archive_url) ? $archive_url : '']),
        ],
      ],
      'title' => $post_id > 0 ? trim(get_the_title($post_id)) : '',
      'titleHeading' => ($settings['title_heading'] ?? '') === 'h2' ? 'h2' : 'h1',
    ];

    $class_name = trim((string) ($settings['class_name'] ?? ''));
    if ($class_name !== '') {
      $props['className'] = $class_name;
    }

    return $props;
  }
}

if (! function_exists('eai_post_hero_banner_props_are_empty')) {
  /** @param array<string, mixed> $props */
  function eai_post_hero_banner_props_are_empty(array $props): bool
  {
    $background = is_array($props['backgroundImage'] ?? null) ? $props['backgroundImage'] : [];
    $items = is_array($props['breadcrumbItems'] ?? null) ? $props['breadcrumbItems'] : [];

    if (trim((string) ($background['url'] ?? '')) === '' || trim((string) ($props['title'] ?? '')) === '' || count($items) !== 2) {
      return true;
    }

    foreach ($items as $item) {
      if (! is_array($item) || trim((string) ($item['label'] ?? '')) === '' || trim((string) ($item['link']['url'] ?? '')) === '') {
        return true;
      }
    }

    return false;
  }
}

if (! function_exists('eai_post_hero_banner_get_editor_sample_props')) {
  /** @param array<string, mixed> $settings @return array<string, mixed> */
  function eai_post_hero_banner_get_editor_sample_props(array $settings): array
  {
    $props = [
      'backgroundImage' => [
        'url' => 'https://placehold.co/1600x900/jpg',
        'alt' => 'The Meridian',
        'display_dimensions' => ['width' => 1600, 'height' => 900],
      ],
      'breadcrumbItems' => [
        ['label' => 'Trang chủ', 'link' => eai_rc_map_link(['url' => '/'])],
        ['label' => 'Dự án tiêu biểu', 'link' => eai_rc_map_link(['url' => '/du-an-tieu-bieu/'])],
      ],
      'title' => 'The Meridian - Vẻ đẹp tân cổ điển vượt lên giá trị thẩm mỹ',
      'titleHeading' => ($settings['title_heading'] ?? '') === 'h2' ? 'h2' : 'h1',
    ];

    $class_name = trim((string) ($settings['class_name'] ?? ''));
    if ($class_name !== '') {
      $props['className'] = $class_name;
    }

    return $props;
  }
}
