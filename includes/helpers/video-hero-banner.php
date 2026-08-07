<?php

if (! defined('ABSPATH')) {
  exit;
}

if (! function_exists('eai_video_hero_banner_get_rc_props')) {
  function eai_video_hero_banner_get_rc_props(array $settings): array
  {
    $video = is_array($settings['video'] ?? null) ? $settings['video'] : [];
    $poster = is_array($settings['poster'] ?? null) ? $settings['poster'] : [];
    $resolution = (string) ($settings['poster_resolution'] ?? 'large');
    $url = trim((string) ($video['url'] ?? ''));
    $class_name = trim((string) ($settings['class_name'] ?? ''));
    $title = trim((string) ($settings['title'] ?? ''));
    $description = trim((string) ($settings['description'] ?? ''));

    $props = [
      'poster' => eai_rc_map_media_model($poster, [], null, $resolution),
      'mobileAspectRatio' => ($settings['mobile_aspect_ratio'] ?? '') === 'yes',
    ];

    if ($url !== '') {
      $props['url'] = $url;
    }

    if ($class_name !== '') {
      $props['className'] = $class_name;
    }

    if ($title !== '') {
      $props['title'] = $title;
    }

    if ($description !== '') {
      $props['description'] = $description;
    }

    return $props;
  }
}
