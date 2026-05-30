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
