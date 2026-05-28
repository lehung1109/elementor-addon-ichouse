<?php
if (! defined('ABSPATH')) {
  exit;
}

if (! function_exists('eai_get_process_section_icon_options')) {
  function eai_get_process_section_icon_options(): array
  {
    return [
      'user-round' => esc_html__('User (Tư vấn)', 'eai'),
      'pencil-ruler' => esc_html__('Pencil Ruler (Thiết kế)', 'eai'),
      'cog' => esc_html__('Cog (Thi công)', 'eai'),
      'banknote' => esc_html__('Banknote (Thanh toán)', 'eai'),
    ];
  }
}

if (! function_exists('eai_rc_map_process_section_steps')) {
  /**
   * Map Elementor repeater steps to ProcessSectionModel.steps for api-rc.
   *
   * @param array<int, array<string, mixed>> $steps
   * @return array<int, array<string, mixed>>
   */
  function eai_rc_map_process_section_steps(array $steps): array
  {
    $mapped = [];
    $index = 0;

    foreach ($steps as $step) {
      if (! is_array($step)) {
        continue;
      }

      $index++;
      $mapped[] = [
        'id' => $index,
        'title' => (string) ($step['title'] ?? ''),
        'description' => (string) ($step['description'] ?? ''),
        'icon' => (string) ($step['icon'] ?? 'user-round'),
      ];
    }

    return $mapped;
  }
}

if (! function_exists('eai_get_process_section_icon_svg')) {
  /**
   * Inline Lucide-style SVG for process section step icons.
   */
  function eai_get_process_section_icon_svg(string $icon, string $class = 'h-9 w-9 md:h-12 md:w-12'): string
  {
    $class_attr = esc_attr($class);
    $common = 'xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-' . esc_attr($icon) . ' ' . $class_attr . '" aria-hidden="true"';

    switch ($icon) {
      case 'pencil-ruler':
        return '<svg ' . $common . '><path d="M13 7 8.7 2.7a2.41 2.41 0 0 0-3.4 0L2.7 5.3a2.41 2.41 0 0 0 0 3.4L7 13"/><path d="m8 6 2-2"/><path d="m18 16 2-2"/><path d="m17 11 4.3 4.3c.94.94.94 2.46 0 3.4l-2.6 2.6c-.94.94-2.46.94-3.4 0L11 17"/><path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"/><path d="m15 5 4 4"/></svg>';
      case 'cog':
        return '<svg ' . $common . '><path d="M11 10.27 7 3.34"/><path d="m11 13.73-4 6.93"/><path d="M12 22v-2"/><path d="M12 2v2"/><path d="M14 12h8"/><path d="m17 20.66-1-1.73"/><path d="m17 3.34-1 1.73"/><path d="M2 12h2"/><path d="m20.66 17-1.73-1"/><path d="m20.66 7-1.73 1"/><path d="m3.34 17 1.73-1"/><path d="m3.34 7 1.73 1"/><circle cx="12" cy="12" r="2"/><circle cx="12" cy="12" r="8"/></svg>';
      case 'banknote':
        return '<svg ' . $common . '><rect width="20" height="12" x="2" y="6" rx="2"/><circle cx="12" cy="12" r="2"/><path d="M6 12h.01M18 12h.01"/></svg>';
      case 'user-round':
      default:
        return '<svg ' . $common . '><circle cx="12" cy="8" r="5"/><path d="M20 21a8 8 0 0 0-16 0"/></svg>';
    }
  }
}

