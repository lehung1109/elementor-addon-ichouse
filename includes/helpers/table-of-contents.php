<?php
if (! defined('ABSPATH')) {
  exit;
}

if (! function_exists('eai_toc_get_default_settings')) {
  /**
   * @return array{title: string, enabled_post_types: string[], min_headings: int}
   */
  function eai_toc_get_default_settings(): array
  {
    return [
      'title' => 'Mục lục',
      'enabled_post_types' => ['post', 'page'],
      'min_headings' => 2,
    ];
  }
}

if (! function_exists('eai_toc_get_post_type_options')) {
  /**
   * Public post types with admin UI (for settings checkboxes).
   *
   * @return array<string, string>
   */
  function eai_toc_get_post_type_options(): array
  {
    $options = [];

    foreach (get_post_types(['public' => true], 'objects') as $post_type) {
      if ($post_type->name === 'attachment' || empty($post_type->show_ui)) {
        continue;
      }

      $options[$post_type->name] = $post_type->labels->singular_name;
    }

    return $options;
  }
}

if (! function_exists('eai_toc_get_settings')) {
  /**
   * @return array{title: string, enabled_post_types: string[], min_headings: int}
   */
  function eai_toc_get_settings(): array
  {
    $defaults = eai_toc_get_default_settings();
    $stored = get_option('eai_toc_settings', []);

    if (! is_array($stored)) {
      $stored = [];
    }

    $allowed_types = array_keys(eai_toc_get_post_type_options());
    $enabled = $stored['enabled_post_types'] ?? $defaults['enabled_post_types'];
    if (! is_array($enabled)) {
      $enabled = $defaults['enabled_post_types'];
    }

    $enabled = array_values(
      array_intersect(
        array_map('sanitize_key', $enabled),
        $allowed_types
      )
    );

    if ($enabled === []) {
      $enabled = $defaults['enabled_post_types'];
    }

    $title = isset($stored['title']) && is_string($stored['title'])
      ? $stored['title']
      : $defaults['title'];
    $title = $title !== '' ? $title : $defaults['title'];

    $min_headings = isset($stored['min_headings']) ? (int) $stored['min_headings'] : $defaults['min_headings'];
    if ($min_headings < 1) {
      $min_headings = 1;
    }

    return [
      'title' => $title,
      'enabled_post_types' => $enabled,
      'min_headings' => $min_headings,
    ];
  }
}

if (! function_exists('eai_toc_is_enabled_for_post')) {
  function eai_toc_is_enabled_for_post(\WP_Post $post): bool
  {
    $settings = eai_toc_get_settings();

    return in_array($post->post_type, $settings['enabled_post_types'], true);
  }
}

if (! function_exists('eai_toc_unique_anchor')) {
  function eai_toc_unique_anchor(string $text, array &$used_ids): string
  {
    $base = sanitize_title($text);
    if ($base === '') {
      $base = 'section';
    }

    $candidate = $base;
    $suffix = 2;

    while (in_array($candidate, $used_ids, true)) {
      $candidate = $base . '-' . $suffix;
      $suffix++;
    }

    $used_ids[] = $candidate;

    return $candidate;
  }
}

if (! function_exists('eai_toc_add_heading_ids')) {
  function eai_toc_add_heading_ids(string $content): string
  {
    $used_ids = [];

    return (string) preg_replace_callback(
      '/(<h([1-6])\b([^>]*))>(.*?)<\/h\2>/is',
      static function (array $matches) use (&$used_ids): string {
        if (preg_match('/\bid\s*=/i', $matches[3])) {
          return $matches[0];
        }

        $label = wp_strip_all_tags($matches[4]);
        $id = eai_toc_unique_anchor($label, $used_ids);

        return $matches[1] . $matches[3] . ' id="' . esc_attr($id) . '">' . $matches[4] . '</h' . $matches[2] . '>';
      },
      $content
    );
  }
}

if (! function_exists('eai_toc_parse_headings')) {
  /**
   * @return array<int, array{level: int, targetId: string, label: string, markup: string}>
   */
  function eai_toc_parse_headings(string $content): array
  {
    $parsed = [];

    if (
      ! preg_match_all(
        '/<(h[2-6])\b([^>]*\bid="([^"]+)"[^>]*)>(.*?)<\/\1>/is',
        $content,
        $matches,
        PREG_SET_ORDER
      )
    ) {
      return [];
    }

    foreach ($matches as $match) {
      $tag = strtolower($match[1]);
      $level = (int) substr($tag, 1);
      $target_id = trim($match[3]);
      $label = trim(wp_strip_all_tags($match[4]));

      if ($target_id === '' || $label === '') {
        continue;
      }

      $parsed[] = [
        'level' => $level,
        'targetId' => $target_id,
        'label' => $label,
        'markup' => $match[0],
      ];
    }

    return $parsed;
  }
}

if (! function_exists('eai_toc_build_items')) {
  /**
   * @param array<int, array{level: int, targetId: string, label: string}> $parsed
   * @return array<int, array{label: string, targetId: string, items?: array}>
   */
  function eai_toc_build_items(array $parsed): array
  {
    $root = [];
    /** @var array<int, array{level: int, items: array}> $stack */
    $stack = [
      ['level' => 0, 'items' => &$root],
    ];

    foreach ($parsed as $entry) {
      $level = $entry['level'];

      while (count($stack) > 1 && $stack[count($stack) - 1]['level'] >= $level) {
        array_pop($stack);
      }

      $parent_items = &$stack[count($stack) - 1]['items'];
      $parent_items[] = [
        'label' => $entry['label'],
        'targetId' => $entry['targetId'],
      ];

      $last_index = array_key_last($parent_items);
      $child_items = [];
      $parent_items[$last_index]['items'] = &$child_items;

      $stack[] = [
        'level' => $level,
        'items' => &$child_items,
      ];
    }

    return eai_toc_prune_empty_item_children($root);
  }
}

if (! function_exists('eai_toc_prune_empty_item_children')) {
  /**
   * @param array<int, array<string, mixed>> $items
   * @return array<int, array{label: string, targetId: string, items?: array}>
   */
  function eai_toc_prune_empty_item_children(array $items): array
  {
    $out = [];

    foreach ($items as $item) {
      $node = [
        'label' => $item['label'],
        'targetId' => $item['targetId'],
      ];

      if (! empty($item['items']) && is_array($item['items'])) {
        $children = eai_toc_prune_empty_item_children($item['items']);
        if ($children !== []) {
          $node['items'] = $children;
        }
      }

      $out[] = $node;
    }

    return $out;
  }
}

if (! function_exists('eai_toc_get_rc_props')) {
  /**
   * @param array<int, array{level: int, targetId: string, label: string}> $parsed
   * @return array{title: string, items: array}
   */
  function eai_toc_get_rc_props(array $parsed, ?array $settings = null): array
  {
    $settings = $settings ?? eai_toc_get_settings();

    return [
      'title' => $settings['title'],
      'items' => eai_toc_build_items($parsed),
    ];
  }
}

if (! function_exists('eai_toc_insert_before_first_heading')) {
  function eai_toc_insert_before_first_heading(string $content, string $toc_html, array $parsed): string
  {
    if ($parsed === [] || $toc_html === '') {
      return $content;
    }

    $markup = $parsed[0]['markup'];
    $pos = strpos($content, $markup);

    if ($pos === false) {
      return $content;
    }

    return substr_replace($content, $toc_html . $markup, (int) $pos, strlen($markup));
  }
}

if (! function_exists('eai_toc_sanitize_settings')) {
  /**
   * @param mixed $input
   * @return array{title: string, enabled_post_types: string[], min_headings: int}
   */
  function eai_toc_sanitize_settings($input): array
  {
    $defaults = eai_toc_get_default_settings();
    $allowed_types = array_keys(eai_toc_get_post_type_options());

    if (! is_array($input)) {
      return $defaults;
    }

    $title = isset($input['title']) && is_string($input['title'])
      ? sanitize_text_field($input['title'])
      : $defaults['title'];

    if ($title === '') {
      $title = $defaults['title'];
    }

    $enabled = $input['enabled_post_types'] ?? [];
    if (! is_array($enabled)) {
      $enabled = [];
    }

    $enabled = array_values(
      array_intersect(
        array_map('sanitize_key', $enabled),
        $allowed_types
      )
    );

    if ($enabled === []) {
      $enabled = $defaults['enabled_post_types'];
    }

    $min_headings = isset($input['min_headings']) ? (int) $input['min_headings'] : $defaults['min_headings'];
    if ($min_headings < 1) {
      $min_headings = 1;
    }

    return [
      'title' => $title,
      'enabled_post_types' => $enabled,
      'min_headings' => $min_headings,
    ];
  }
}
