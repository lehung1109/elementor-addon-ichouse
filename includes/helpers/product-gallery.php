<?php
if (! defined('ABSPATH')) {
  exit;
}

if (! function_exists('eai_rc_galerie_4_row_attachment_id')) {
  /**
   * Resolve attachment ID from a Galerie 4 row after get_field format_value.
   */
  function eai_rc_galerie_4_row_attachment_id(array $row): int
  {
    $attachment = $row['attachment'] ?? null;

    if ($attachment instanceof WP_Post) {
      return (int) $attachment->ID;
    }

    if (is_numeric($attachment)) {
      return (int) $attachment;
    }

    if (is_array($attachment) && ! empty($attachment['ID'])) {
      return (int) $attachment['ID'];
    }

    return 0;
  }
}

if (! function_exists('eai_rc_map_product_gallery_items_from_acf_galerie_4')) {
  /**
   * Map ACF Galerie 4 rows to ProductGalleryModel.items (image: MediaModel).
   *
   * @param array<int, array<string, mixed>> $rows
   * @return array<int, array{image: array<string, mixed>}>
   */
  function eai_rc_map_product_gallery_items_from_acf_galerie_4(
    array $rows,
    string $size = 'full'
  ): array {
    $items = [];

    foreach ($rows as $row) {
      if (! is_array($row)) {
        continue;
      }

      $id = eai_rc_galerie_4_row_attachment_id($row);
      if ($id <= 0 || ! wp_attachment_is_image($id)) {
        continue;
      }

      $media = eai_rc_map_media_model(['id' => $id], [], null, $size);
      if (empty($media['url'])) {
        continue;
      }

      $items[] = ['image' => $media];
    }

    return $items;
  }
}

if (! function_exists('eai_product_gallery_get_editor_sample_props')) {
  /**
   * Static demo props for Elementor editor when ACF gallery is empty.
   *
   * @param array<string, mixed> $settings
   * @return array<string, mixed>
   */
  function eai_product_gallery_get_editor_sample_props(array $settings): array
  {
    $dimensions = ['width' => 2400, 'height' => 1800];
    $items = [];

    for ($i = 1; $i <= 8; $i++) {
      $items[] = [
        'image' => [
          'url' => 'https://placehold.co/2400x1800/png?text=image-' . $i,
          'alt' => 'Demo image ' . $i,
          'display_dimensions' => $dimensions,
          'srcSet' => '',
          'sizes' => '',
        ],
      ];
    }

    $props = [
      'items' => $items,
    ];

    $class_name = trim((string) ($settings['class_name'] ?? ''));
    if ($class_name !== '') {
      $props['className'] = $class_name;
    }

    return $props;
  }
}
