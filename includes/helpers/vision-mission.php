<?php
if (! defined('ABSPATH')) {
  exit;
}

if (! function_exists('eai_rc_map_vision_mission_items')) {
  /**
   * @param array<int, array<string, mixed>> $rows
   * @return array<int, array{title: string, description: string}>
   */
  function eai_rc_map_vision_mission_items(array $rows): array
  {
    $mapped = [];

    foreach ($rows as $row) {
      if (! is_array($row)) {
        continue;
      }

      $title = trim((string) ($row['title'] ?? ''));
      $description = trim((string) ($row['description'] ?? ''));

      if ($title === '' && $description === '') {
        continue;
      }

      $mapped[] = [
        'title' => $title,
        'description' => $description,
      ];
    }

    return $mapped;
  }
}

if (! function_exists('eai_rc_map_vision_mission_columns')) {
  /**
   * @param array<int, array<string, mixed>> $rows
   * @return array<int, array{title: string, items: array<int, array{title: string, description: string}>}>
   */
  function eai_rc_map_vision_mission_columns(array $rows): array
  {
    $mapped = [];

    foreach ($rows as $row) {
      if (! is_array($row)) {
        continue;
      }

      $title = trim((string) ($row['title'] ?? ''));
      $items_raw = is_array($row['items'] ?? null) ? $row['items'] : [];
      $items = eai_rc_map_vision_mission_items($items_raw);

      if ($title === '' && empty($items)) {
        continue;
      }

      $mapped[] = [
        'title' => $title,
        'items' => $items,
      ];
    }

    return $mapped;
  }
}

if (! function_exists('eai_vision_mission_get_rc_props')) {
  /**
   * @param array<string, mixed> $settings
   * @return array<string, mixed>
   */
  function eai_vision_mission_get_rc_props(array $settings): array
  {
    $columns = is_array($settings['columns'] ?? null) ? $settings['columns'] : [];
    $class_name = trim((string) ($settings['class_name'] ?? ''));
    $target_id = trim((string) ($settings['scroll_reveal_target_id'] ?? 'vision-mission'));
    if ($target_id === '') {
      $target_id = 'vision-mission';
    }

    $props = [
      'columns' => eai_rc_map_vision_mission_columns($columns),
      'scrollReveal' => [
        'targetId' => $target_id,
      ],
    ];

    if ($class_name !== '') {
      $props['className'] = $class_name;
    }

    return $props;
  }
}
