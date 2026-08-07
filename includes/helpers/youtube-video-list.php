<?php
if (! defined('ABSPATH')) {
  exit;
}

if (! function_exists('eai_youtube_video_list_extract_video_id')) {
  function eai_youtube_video_list_extract_video_id(string $value): string
  {
    $value = trim($value);
    if (preg_match('/^[A-Za-z0-9_-]{11}$/', $value) === 1) {
      return $value;
    }

    $parts = wp_parse_url($value);
    if (! is_array($parts)) {
      return '';
    }

    $host = strtolower((string) ($parts['host'] ?? ''));
    $host = preg_replace('/^www\./', '', $host) ?? $host;
    $path = trim((string) ($parts['path'] ?? ''), '/');
    $candidate = '';

    if ($host === 'youtu.be') {
      $candidate = explode('/', $path)[0] ?? '';
    } elseif ($host === 'youtube.com' || $host === 'm.youtube.com') {
      if ($path === 'watch') {
        parse_str((string) ($parts['query'] ?? ''), $query);
        $candidate = (string) ($query['v'] ?? '');
      } elseif (preg_match('#^(?:shorts|embed)/([^/]+)#', $path, $matches) === 1) {
        $candidate = $matches[1];
      }
    }

    return preg_match('/^[A-Za-z0-9_-]{11}$/', $candidate) === 1 ? $candidate : '';
  }
}

if (! function_exists('eai_rc_map_youtube_video_list_items')) {
  /**
   * @param array<int, mixed> $rows
   * @return array<int, array<string, string>>
   */
  function eai_rc_map_youtube_video_list_items(array $rows): array
  {
    $mapped = [];

    foreach ($rows as $row) {
      if (! is_array($row)) {
        continue;
      }

      $video_id = eai_youtube_video_list_extract_video_id((string) ($row['youtube_video'] ?? ''));
      if ($video_id === '') {
        continue;
      }

      $item = ['youtubeVideoId' => $video_id];
      $title = trim((string) ($row['title'] ?? ''));
      if ($title !== '') {
        $item['title'] = $title;
      }

      $mapped[] = $item;
    }

    return $mapped;
  }
}

if (! function_exists('eai_youtube_video_list_get_target_id')) {
  function eai_youtube_video_list_get_target_id(string $widget_id): string
  {
    $safe_widget_id = sanitize_html_class($widget_id);

    return 'youtube-video-list-' . ($safe_widget_id !== '' ? $safe_widget_id : 'widget');
  }
}

if (! function_exists('eai_youtube_video_list_get_rc_props')) {
  /**
   * @param array<string, mixed> $settings
   * @return array<string, mixed>
   */
  function eai_youtube_video_list_get_rc_props(array $settings, string $widget_id): array
  {
    $rows = is_array($settings['items'] ?? null) ? $settings['items'] : [];
    $props = [
      'items' => eai_rc_map_youtube_video_list_items($rows),
      'scrollReveal' => [
        'targetId' => eai_youtube_video_list_get_target_id($widget_id),
      ],
    ];

    $class_name = trim((string) ($settings['class_name'] ?? ''));
    if ($class_name !== '') {
      $props['className'] = $class_name;
    }

    return $props;
  }
}

if (! function_exists('eai_youtube_video_list_get_editor_sample_props')) {
  /**
   * @param array<string, mixed> $settings
   * @return array<string, mixed>
   */
  function eai_youtube_video_list_get_editor_sample_props(array $settings, string $widget_id): array
  {
    $props = [
      'items' => [
        ['youtubeVideoId' => 'dQw4w9WgXcQ', 'title' => 'Báo giá chi phí thiết kế nhà'],
        ['youtubeVideoId' => 'dQw4w9WgXcQ', 'title' => 'Xây nhà 70m2 cần bao nhiêu'],
        ['youtubeVideoId' => 'dQw4w9WgXcQ', 'title' => 'Thiết kế thi công nhà phố'],
      ],
      'scrollReveal' => [
        'targetId' => eai_youtube_video_list_get_target_id($widget_id),
      ],
    ];

    $class_name = trim((string) ($settings['class_name'] ?? ''));
    if ($class_name !== '') {
      $props['className'] = $class_name;
    }

    return $props;
  }
}
