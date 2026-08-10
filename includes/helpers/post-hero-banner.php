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

if (! function_exists('eai_post_hero_banner_map_breadcrumb_items')) {
  /** @return array<int, array{label: string, link: array<string, mixed>}> */
  function eai_post_hero_banner_map_breadcrumb_items(mixed $value): array
  {
    $breadcrumb_items = [];
    $rows = is_array($value) ? $value : [];

    foreach ($rows as $row) {
      if (! is_array($row)) {
        continue;
      }

      $label = trim((string) ($row['label'] ?? ''));
      $link = eai_rc_map_link(is_array($row['link'] ?? null) ? $row['link'] : []);
      if ($label === '' || trim($link['url']) === '') {
        continue;
      }

      $breadcrumb_items[] = ['label' => $label, 'link' => $link];
      if (count($breadcrumb_items) === 2) {
        break;
      }
    }

    return $breadcrumb_items;
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

    $background_image = eai_rc_map_media_model($media, [], null, $image_size);
    if (! empty($background_image['srcSet'])) {
      $background_image['sizes'] = '100vw';
    }

    $breadcrumb_items = eai_post_hero_banner_map_breadcrumb_items(
      $settings['breadcrumb_items'] ?? []
    );

    $props = [
      'backgroundImage' => $background_image,
      'breadcrumbItems' => $breadcrumb_items,
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

    return trim((string) ($background['url'] ?? '')) === '';
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
