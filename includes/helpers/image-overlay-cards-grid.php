<?php
if (! defined('ABSPATH')) {
  exit;
}

if (! function_exists('eai_rc_map_image_overlay_cards_grid_items')) {
  /**
   * Map Elementor repeater rows to ImageOverlayCardsGridModel.items.
   *
   * @param array<int, array<string, mixed>> $rows
   * @return array<int, array<string, mixed>>
   */
  function eai_rc_map_image_overlay_cards_grid_items(array $rows): array
  {
    $mapped = [];

    foreach ($rows as $row) {
      if (! is_array($row)) {
        continue;
      }

      $title = trim((string) ($row['title'] ?? ''));
      if ($title === '') {
        continue;
      }

      $image = is_array($row['image'] ?? null) ? $row['image'] : [];
      $resolution = (string) ($row['image_resolution'] ?? 'large');

      $media = eai_rc_map_media_model($image, [], null, $resolution);
      if (empty($media['url'])) {
        continue;
      }

      $alt_override = trim((string) ($row['alt'] ?? ''));
      if ($alt_override !== '') {
        $media['alt'] = $alt_override;
      }

      $item = [
        'image' => $media,
        'title' => $title,
      ];

      $link = is_array($row['link'] ?? null) ? $row['link'] : [];
      if (trim((string) ($link['url'] ?? '')) !== '') {
        $item['link'] = eai_rc_map_link($link);
      }

      $mapped[] = $item;
    }

    return $mapped;
  }
}

if (! function_exists('eai_image_overlay_cards_grid_resolve_gap')) {
  function eai_image_overlay_cards_grid_resolve_gap(array $settings): int
  {
    $gap = (int) ($settings['gap'] ?? 24);
    if ($gap < 0) {
      $gap = 0;
    }
    if ($gap > 64) {
      $gap = 64;
    }

    return $gap;
  }
}

if (! function_exists('eai_image_overlay_cards_grid_get_rc_props')) {
  /**
   * @param array<string, mixed> $settings
   * @return array<string, mixed>
   */
  function eai_image_overlay_cards_grid_get_rc_props(array $settings): array
  {
    $items = is_array($settings['items'] ?? null) ? $settings['items'] : [];

    return [
      'className' => (string) ($settings['class_name'] ?? ''),
      'gap' => eai_image_overlay_cards_grid_resolve_gap($settings),
      'items' => eai_rc_map_image_overlay_cards_grid_items($items),
    ];
  }
}

if (! function_exists('eai_image_overlay_cards_grid_get_editor_sample_props')) {
  /**
   * Static sample for Elementor canvas (mirrors api-rc src/data/image-overlay-cards-grid.ts).
   *
   * @param array<string, mixed> $settings
   * @return array<string, mixed>
   */
  function eai_image_overlay_cards_grid_get_editor_sample_props(array $settings): array
  {
    $props = [
      'className' => (string) ($settings['class_name'] ?? ''),
      'gap' => eai_image_overlay_cards_grid_resolve_gap($settings),
      'items' => [
        [
          'image' => [
            'url' => 'https://placehold.co/400x400/png',
            'alt' => 'Thiết kế nội thất nhà phố',
            'display_dimensions' => ['width' => 400, 'height' => 400],
          ],
          'title' => 'Thiết kế nội thất nhà phố',
          'link' => ['url' => '#', 'is_external' => false, 'nofollow' => false],
        ],
        [
          'image' => [
            'url' => 'https://placehold.co/400x400/png?text=2',
            'alt' => 'Thiết kế nội thất chung cư',
            'display_dimensions' => ['width' => 400, 'height' => 400],
          ],
          'title' => 'Thiết kế nội thất chung cư',
          'link' => ['url' => '#', 'is_external' => false, 'nofollow' => false],
        ],
        [
          'image' => [
            'url' => 'https://placehold.co/400x400/png?text=3',
            'alt' => 'Thiết kế nội thất biệt thự',
            'display_dimensions' => ['width' => 400, 'height' => 400],
          ],
          'title' => 'Thiết kế nội thất biệt thự',
        ],
      ],
    ];

    return $props;
  }
}
