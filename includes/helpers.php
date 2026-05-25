<?php
if (! defined('ABSPATH')) {
  exit;
}

if (! function_exists('eai_render_template')) {
  function eai_render_template(string $template, array $args = []): void
  {
    $theme_template = locate_template(
      [
        'elementor-addon-ichouse/' . $template,
      ],
      false,
      false
    );

    $plugin_template = \EAI_PATH . 'includes/' . ltrim($template, '/');
    $path = $theme_template ?: $plugin_template;

    if (! file_exists($path)) {
      return;
    }

    load_template($path, false, $args);
  }
}

if (! function_exists('eai_get_menu_tree_with_active')) {
  function eai_get_menu_tree_with_active(int $menu_id): array
  {
    $items = $menu_id ? wp_get_nav_menu_items($menu_id) : [];

    if (empty($items) || is_wp_error($items)) {
      return [];
    }

    $current_object_id = get_queried_object_id();
    $current_path = untrailingslashit(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');

    $normalized = array_map(function ($item) use ($current_object_id, $current_path) {
      $item_path = untrailingslashit(parse_url($item->url ?? '', PHP_URL_PATH) ?: '/');

      $is_current_by_object = !empty($item->object_id) && (int) $item->object_id === (int) $current_object_id;
      $is_current_by_path = $item_path && $item_path === $current_path;

      return [
        'id' => (int) $item->ID,
        'parent_id' => (int) $item->menu_item_parent,
        'label' => $item->title,
        'href' => $item->url,
        'current' => $is_current_by_object || $is_current_by_path,
      ];
    }, $items);

    return eai_build_menu_branch($normalized, 0);
  }
}

if (! function_exists('eai_build_menu_branch')) {
  function eai_build_menu_branch(array $items, int $parent_id = 0): array
  {
    $branch = [];

    foreach ($items as $item) {
      if ((int) $item['parent_id'] !== $parent_id) {
        continue;
      }

      $children = eai_build_menu_branch($items, (int) $item['id']);
      $has_active_child = !empty(array_filter($children, fn($child) => !empty($child['active'])));

      $item['children'] = $children;
      $item['active'] = !empty($item['current']) || $has_active_child;

      $branch[] = $item;
    }

    return $branch;
  }
}

if (! function_exists('eai_rc_map_header_menu_items')) {
  /**
   * Map WP menu tree to HeaderMenuItemModel props for api-rc.
   *
   * @param array<int, array<string, mixed>> $items
   * @return array<int, array<string, mixed>>
   */
  function eai_rc_map_header_menu_items(array $items): array
  {
    $mapped = [];

    foreach ($items as $item) {
      $entry = [
        'label' => (string) ($item['label'] ?? ''),
        'href' => (string) ($item['href'] ?? ''),
      ];

      if (! empty($item['active'])) {
        $entry['active'] = true;
      }

      if (! empty($item['children']) && is_array($item['children'])) {
        $entry['children'] = eai_rc_map_header_menu_items($item['children']);
      }

      $mapped[] = $entry;
    }

    return $mapped;
  }
}

if (! function_exists('eai_get_image_size_options')) {
  /**
   * Image size options for Elementor SELECT controls (matches Elementor media control labels).
   */
  function eai_get_image_size_options(): array
  {
    $wp_image_sizes = \Elementor\Group_Control_Image_Size::get_all_image_sizes();
    $options = [];

    foreach ($wp_image_sizes as $size_key => $size_attributes) {
      $label = ucwords(str_replace('_', ' ', $size_key));

      if (is_array($size_attributes)) {
        $label .= sprintf(' - %d x %d', $size_attributes['width'], $size_attributes['height']);
      }

      $options[$size_key] = $label;
    }

    $options[''] = esc_html_x('Full', 'Image Size Control', 'elementor');

    return $options;
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
