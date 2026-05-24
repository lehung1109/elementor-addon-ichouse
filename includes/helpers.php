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
