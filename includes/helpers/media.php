<?php
if (! defined('ABSPATH')) {
  exit;
}

if (! function_exists('eai_rc_map_link')) {
  /**
   * @param array<string, mixed> $link
   * @return array{url: string, is_external: bool, nofollow: bool}
   */
  function eai_rc_map_link(array $link): array
  {
    return [
      'url' => (string) ($link['url'] ?? ''),
      'is_external' => ! empty($link['is_external']),
      'nofollow' => ! empty($link['nofollow']),
    ];
  }
}

if (! function_exists('eai_get_media_image_url')) {
  /**
   * Resolve attachment URL and dimensions for a media control value and image size slug.
   *
   * @return array{url: string, width: int, height: int}
   */
  function eai_get_media_image_url(array $media, string $size = 'large'): array
  {
    $empty = [
      'url' => '',
      'width' => 0,
      'height' => 0,
    ];

    if (empty($media)) {
      return $empty;
    }

    if ($size === '') {
      $size = 'full';
    }

    if (! empty($media['id'])) {
      $src = wp_get_attachment_image_src((int) $media['id'], $size);

      if ($src) {
        return [
          'url' => $src[0],
          'width' => (int) $src[1],
          'height' => (int) $src[2],
        ];
      }
    }

    return [
      'url' => $media['url'] ?? '',
      'width' => 0,
      'height' => 0,
    ];
  }
}

if (! function_exists('eai_rc_map_media_model')) {
  /**
   * Map Elementor media + dimensions (+ optional link) to api-rc MediaModel.
   *
   * @param array<string, mixed> $media
   * @param array<string, mixed> $dimensions
   * @param array<string, mixed>|null $link
   * @return array<string, mixed>
   */
  function eai_rc_map_media_model(
    array $media,
    array $dimensions = [],
    ?array $link = null,
    string $size = 'full'
  ): array {
    $resolved = eai_get_media_image_url($media, $size);

    $width = (int) ($dimensions['width'] ?? 0);
    $height = (int) ($dimensions['height'] ?? 0);

    if ($width <= 0) {
      $width = (int) ($resolved['width'] ?? 0);
    }
    if ($height <= 0) {
      $height = (int) ($resolved['height'] ?? 0);
    }

    $alt = '';
    if (! empty($media['alt'])) {
      $alt = (string) $media['alt'];
    } elseif (! empty($media['id'])) {
      $alt = (string) get_post_meta((int) $media['id'], '_wp_attachment_image_alt', true);
    }

    $model = [
      'url' => (string) ($resolved['url'] ?: ($media['url'] ?? '')),
      'alt' => $alt,
      'display_dimensions' => [
        'width' => $width,
        'height' => $height,
      ],
    ];

    if ($link !== null && ! empty($link['url'])) {
      $model['link'] = eai_rc_map_link($link);
    }

    return $model;
  }
}

if (! function_exists('eai_rc_map_header_inner_info_list')) {
  /**
   * @param array<int, array<string, mixed>> $info_list
   * @return array<int, array<string, mixed>>
   */
  function eai_rc_map_header_inner_info_list(array $info_list): array
  {
    $mapped = [];

    foreach ($info_list as $item) {
      if (! is_array($item)) {
        continue;
      }

      $mapped[] = [
        'icon' => eai_rc_map_media_model(
          is_array($item['icon'] ?? null) ? $item['icon'] : [],
          is_array($item['icon_dimensions'] ?? null) ? $item['icon_dimensions'] : []
        ),
        'text' => (string) ($item['text'] ?? ''),
      ];
    }

    return $mapped;
  }
}

if (! function_exists('eai_rc_map_carousel_slides')) {
  /**
   * @param array<int, array<string, mixed>> $slides
   * @return array<int, array<string, mixed>>
   */
  function eai_rc_map_carousel_slides(array $slides): array
  {
    $mapped = [];

    foreach ($slides as $slide) {
      if (! is_array($slide)) {
        continue;
      }

      $image = is_array($slide['image'] ?? null) ? $slide['image'] : [];
      $resolution = (string) ($slide['image_resolution'] ?? 'large');
      $link = is_array($slide['link'] ?? null) && ! empty($slide['link']['url'])
        ? $slide['link']
        : null;

      $mapped[] = [
        'image' => eai_rc_map_media_model($image, [], $link, $resolution),
      ];
    }

    return $mapped;
  }
}

if (! function_exists('eai_rc_map_partner_logos')) {
  /**
   * Map Elementor repeater rows to PartnerLogosModel.logos (MediaModel[], no link).
   *
   * @param array<int, array<string, mixed>> $logos
   * @return array<int, array<string, mixed>>
   */
  function eai_rc_map_partner_logos(array $logos): array
  {
    $mapped = [];

    foreach ($logos as $row) {
      if (! is_array($row)) {
        continue;
      }

      $image = is_array($row['image'] ?? null) ? $row['image'] : [];
      $resolution = (string) ($row['image_resolution'] ?? 'medium');

      $media = eai_rc_map_media_model($image, [], null, $resolution);
      if (empty($media['url'])) {
        continue;
      }

      $alt_override = trim((string) ($row['alt'] ?? ''));
      if ($alt_override !== '') {
        $media['alt'] = $alt_override;
      }

      $mapped[] = $media;
    }

    return $mapped;
  }
}

