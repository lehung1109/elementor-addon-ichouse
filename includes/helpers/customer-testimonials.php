<?php
if (! defined('ABSPATH')) {
  exit;
}

if (! function_exists('eai_parse_youtube_video_id')) {
  /**
   * Extract a YouTube video ID from a raw ID or common URL formats.
   */
  function eai_parse_youtube_video_id(string $input): string
  {
    $input = trim($input);
    if ($input === '') {
      return '';
    }

    if (preg_match('/^[a-zA-Z0-9_-]{11}$/', $input)) {
      return $input;
    }

    if (preg_match(
      '/(?:youtube\.com\/(?:watch\?(?:[^&\s]+&)*v=|embed\/|shorts\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/',
      $input,
      $matches
    )) {
      return $matches[1];
    }

    return '';
  }
}

if (! function_exists('eai_rc_map_customer_testimonials_items')) {
  /**
   * Map Elementor repeater rows to CustomerTestimonialsModel.items.
   *
   * @param array<int, array<string, mixed>> $rows
   * @return array<int, array<string, mixed>>
   */
  function eai_rc_map_customer_testimonials_items(array $rows): array
  {
    $mapped = [];

    foreach ($rows as $row) {
      if (! is_array($row)) {
        continue;
      }

      $image = is_array($row['image'] ?? null) ? $row['image'] : [];
      $resolution = (string) ($row['image_resolution'] ?? 'large');

      $media = eai_rc_map_media_model($image, [], null, $resolution);
      if (trim((string) ($media['url'] ?? '')) === '') {
        continue;
      }

      $alt_override = trim((string) ($row['alt'] ?? ''));
      if ($alt_override !== '') {
        $media['alt'] = $alt_override;
      }

      $youtube_video_id = eai_parse_youtube_video_id(
        (string) ($row['youtube_video'] ?? '')
      );
      if ($youtube_video_id === '') {
        continue;
      }

      $mapped[] = [
        'image' => $media,
        'youtubeVideoId' => $youtube_video_id,
      ];
    }

    return $mapped;
  }
}

if (! function_exists('eai_customer_testimonials_get_rc_props')) {
  /**
   * @param array<string, mixed> $settings
   * @return array<string, mixed>
   */
  function eai_customer_testimonials_get_rc_props(array $settings): array
  {
    $items = is_array($settings['items'] ?? null) ? $settings['items'] : [];

    return [
      'className' => (string) ($settings['class_name'] ?? ''),
      'title' => (string) ($settings['title'] ?? ''),
      'description' => (string) ($settings['description'] ?? ''),
      'items' => eai_rc_map_customer_testimonials_items($items),
    ];
  }
}
