<?php
if (! defined('ABSPATH')) {
  exit;
}

if (! function_exists('eai_rc_map_number_icon_grid_items')) {
  /**
   * Map Elementor repeater rows to NumberIconGridModel.items.
   *
   * @param array<int, array<string, mixed>> $rows
   * @return array<int, array<string, mixed>>
   */
  function eai_rc_map_number_icon_grid_items(array $rows): array
  {
    $mapped = [];

    foreach ($rows as $row) {
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

      $number = (int) ($row['number'] ?? 0);
      if ($number < 1) {
        $number = 1;
      }

      $mapped[] = [
        'number' => $number,
        'image' => $media,
        'title' => (string) ($row['title'] ?? ''),
      ];
    }

    return $mapped;
  }
}
