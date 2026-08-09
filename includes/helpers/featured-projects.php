<?php
if (! defined('ABSPATH')) {
  exit;
}

if (! function_exists('eai_featured_projects_is_public_post_type')) {
  function eai_featured_projects_is_public_post_type(string $post_type): bool
  {
    $post_type = sanitize_key($post_type);
    $post_type_object = $post_type !== '' ? get_post_type_object($post_type) : null;

    return is_object($post_type_object) && ! empty($post_type_object->public) && $post_type !== 'attachment';
  }
}

if (! function_exists('eai_featured_projects_resolve_post_ids')) {
  /**
   * @param array<string, mixed> $settings
   * @return array<int, int>
   */
  function eai_featured_projects_resolve_post_ids(array $settings): array
  {
    $post_type = sanitize_key((string) ($settings['post_type'] ?? 'post')) ?: 'post';
    if (! eai_featured_projects_is_public_post_type($post_type)) {
      return [];
    }

    $dynamic_key = 'selected_posts_' . $post_type;
    $selected_posts = $settings[$dynamic_key] ?? $settings['selected_posts'] ?? [];
    $selected = array_values(array_unique(array_filter(array_map(
      'intval',
      (array) $selected_posts
    ))));
    if (empty($selected)) {
      return [];
    }

    $valid = get_posts([
      'post_type' => $post_type,
      'post_status' => 'publish',
      'post__in' => $selected,
      'posts_per_page' => -1,
      'orderby' => 'post__in',
      'fields' => 'ids',
    ]);
    $valid_map = array_fill_keys(array_map('intval', $valid), true);
    $resolved = array_values(array_filter(
      $selected,
      static fn(int $post_id): bool => isset($valid_map[$post_id])
    ));

    return array_slice($resolved, 0, 3);
  }
}

if (! function_exists('eai_rc_map_featured_project_from_post')) {
  /**
   * @param array<string, mixed> $config
   * @return array<string, mixed>|null
   */
  function eai_rc_map_featured_project_from_post(int $post_id, array $config): ?array
  {
    $post = get_post($post_id);
    if (! $post instanceof \WP_Post || get_post_status($post) !== 'publish') {
      return null;
    }

    $thumbnail_id = (int) get_post_thumbnail_id($post);
    $permalink = trim((string) get_permalink($post));
    if ($thumbnail_id <= 0 || $permalink === '') {
      return null;
    }

    $image_size = array_key_exists('image_resolution', $config)
      ? sanitize_key((string) $config['image_resolution'])
      : 'large';
    if ($image_size === '') {
      $image_size = 'full';
    }
    $image = eai_rc_map_media_model(['id' => $thumbnail_id], [], null, $image_size);
    if (trim((string) ($image['url'] ?? '')) === '') {
      return null;
    }

    return [
      'image' => $image,
      'title' => (string) get_the_title($post),
      'description' => eai_project_category_gallery_description($post_id, $config),
      'link' => eai_rc_map_link(['url' => $permalink]),
    ];
  }
}

if (! function_exists('eai_rc_map_featured_projects_from_posts')) {
  /**
   * @param array<int, int> $post_ids
   * @param array<string, mixed> $config
   * @return array<int, array<string, mixed>>
   */
  function eai_rc_map_featured_projects_from_posts(array $post_ids, array $config): array
  {
    $items = [];
    foreach ($post_ids as $post_id) {
      $item = eai_rc_map_featured_project_from_post((int) $post_id, $config);
      if ($item !== null) {
        $items[] = $item;
      }
    }

    return $items;
  }
}

if (! function_exists('eai_rc_map_legacy_featured_projects_items')) {
  /**
   * Compatibility path for widgets saved before post selection replaced the repeater.
   *
   * @param array<int, mixed> $rows
   * @return array<int, array<string, mixed>>
   */
  function eai_rc_map_legacy_featured_projects_items(array $rows): array
  {
    $items = [];
    foreach ($rows as $row) {
      if (! is_array($row)) {
        continue;
      }

      $image = is_array($row['image'] ?? null) ? $row['image'] : [];
      $link = is_array($row['link'] ?? null) ? $row['link'] : [];
      $image_size = array_key_exists('image_resolution', $row)
        ? sanitize_key((string) $row['image_resolution'])
        : 'large';
      $media = eai_rc_map_media_model($image, [], null, $image_size === '' ? 'full' : $image_size);
      if (trim((string) ($media['url'] ?? '')) === '' || trim((string) ($link['url'] ?? '')) === '') {
        continue;
      }

      $items[] = [
        'image' => $media,
        'title' => (string) ($row['title'] ?? ''),
        'description' => (string) ($row['description'] ?? ''),
        'link' => eai_rc_map_link($link),
      ];
    }

    return array_slice($items, 0, 3);
  }
}

if (! function_exists('eai_featured_projects_target_id')) {
  function eai_featured_projects_target_id(string $widget_id): string
  {
    return 'featured-projects-' . sanitize_html_class($widget_id, 'widget');
  }
}

if (! function_exists('eai_featured_projects_get_rc_props')) {
  /**
   * @param array<string, mixed> $settings
   * @return array<string, mixed>
   */
  function eai_featured_projects_get_rc_props(array $settings, string $widget_id): array
  {
    $image_resolution = array_key_exists('image_resolution', $settings)
      ? sanitize_key((string) $settings['image_resolution'])
      : 'large';
    $config = [
      'image_resolution' => $image_resolution,
      'investor_taxonomy' => sanitize_key((string) ($settings['investor_taxonomy'] ?? '')),
      'model_taxonomy' => sanitize_key((string) ($settings['model_taxonomy'] ?? '')),
    ];
    $post_ids = eai_featured_projects_resolve_post_ids($settings);
    $items = eai_rc_map_featured_projects_from_posts($post_ids, $config);
    if (empty($post_ids) && empty($items) && is_array($settings['items'] ?? null)) {
      $items = eai_rc_map_legacy_featured_projects_items($settings['items']);
    }

    $button_link = is_array($settings['button_link'] ?? null) ? $settings['button_link'] : [];
    $props = [
      'subtitle' => trim((string) ($settings['subtitle'] ?? '')),
      'title' => trim((string) ($settings['title'] ?? '')),
      'items' => $items,
      'buttonLabel' => trim((string) ($settings['button_label'] ?? '')),
      'buttonLink' => eai_rc_map_link($button_link),
      'scrollReveal' => [
        'targetId' => eai_featured_projects_target_id($widget_id),
      ],
    ];

    $class_name = trim((string) ($settings['class_name'] ?? ''));
    if ($class_name !== '') {
      $props['className'] = $class_name;
    }

    return $props;
  }
}

if (! function_exists('eai_featured_projects_get_editor_sample_props')) {
  /**
   * @param array<string, mixed> $settings
   * @return array<string, mixed>
   */
  function eai_featured_projects_get_editor_sample_props(array $settings, string $widget_id): array
  {
    $titles = ['Biệt thự Nghĩa Đô', 'THT Tower', 'Nhà phố hiện đại'];
    $items = [];
    foreach ($titles as $index => $title) {
      $items[] = [
        'image' => [
          'url' => 'https://placehold.co/600x800/png?text=Project+' . ($index + 1),
          'alt' => $title,
          'display_dimensions' => ['width' => 600, 'height' => 800],
        ],
        'title' => $title,
        'description' => "Chủ đầu tư: ICHouse\nMô hình: Nhà ở",
        'link' => eai_rc_map_link(['url' => '#']),
      ];
    }

    $props = [
      'subtitle' => trim((string) ($settings['subtitle'] ?? '')) ?: 'DỰ ÁN',
      'title' => trim((string) ($settings['title'] ?? '')) ?: 'Dự án nổi bật tại ICHOUSE',
      'items' => $items,
      'buttonLabel' => trim((string) ($settings['button_label'] ?? '')) ?: 'XEM TẤT CẢ DỰ ÁN',
      'buttonLink' => eai_rc_map_link(
        is_array($settings['button_link'] ?? null) ? $settings['button_link'] : ['url' => '#']
      ),
      'scrollReveal' => [
        'targetId' => eai_featured_projects_target_id($widget_id),
      ],
    ];

    $class_name = trim((string) ($settings['class_name'] ?? ''));
    if ($class_name !== '') {
      $props['className'] = $class_name;
    }

    return $props;
  }
}

if (! function_exists('eai_featured_projects_editor_capability')) {
  function eai_featured_projects_editor_capability(string $post_type): string
  {
    $post_type_object = get_post_type_object(sanitize_key($post_type));
    $capability = is_object($post_type_object)
      ? (string) ($post_type_object->cap->edit_posts ?? '')
      : '';

    return $capability !== '' ? $capability : 'edit_posts';
  }
}

if (! function_exists('eai_featured_projects_verify_editor_ajax')) {
  function eai_featured_projects_verify_editor_ajax(string $post_type): void
  {
    if (! current_user_can(eai_featured_projects_editor_capability($post_type))) {
      wp_send_json_error(['message' => 'Forbidden'], 403);
    }

    $nonce = isset($_REQUEST['nonce']) ? sanitize_text_field(wp_unslash($_REQUEST['nonce'])) : '';
    if (! wp_verify_nonce($nonce, 'eai_featured_projects_editor')) {
      wp_send_json_error(['message' => 'Invalid nonce'], 403);
    }
  }
}

if (! function_exists('eai_ajax_featured_projects_search_posts')) {
  function eai_ajax_featured_projects_search_posts(): void
  {
    $post_type = sanitize_key((string) ($_REQUEST['post_type'] ?? ''));
    eai_featured_projects_verify_editor_ajax($post_type);

    if (! eai_featured_projects_is_public_post_type($post_type)) {
      wp_send_json(['results' => []]);
    }

    $search = isset($_REQUEST['q']) ? sanitize_text_field(wp_unslash($_REQUEST['q'])) : '';
    $ids = [];
    if (isset($_REQUEST['ids'])) {
      $raw_ids = wp_unslash($_REQUEST['ids']);
      $ids = is_array($raw_ids)
        ? array_map('intval', $raw_ids)
        : array_map('intval', explode(',', (string) $raw_ids));
      $ids = array_values(array_unique(array_filter($ids)));
    }

    if (! empty($ids)) {
      $posts = get_posts([
        'post_type' => $post_type,
        'post_status' => 'publish',
        'post__in' => array_slice($ids, 0, 3),
        'posts_per_page' => 3,
        'orderby' => 'post__in',
      ]);
    } else {
      $query_args = [
        'post_type' => $post_type,
        'post_status' => 'publish',
        'posts_per_page' => 20,
        'orderby' => 'title',
        'order' => 'ASC',
      ];
      if ($search !== '') {
        if (ctype_digit($search)) {
          $query_args['p'] = (int) $search;
          unset($query_args['orderby'], $query_args['order']);
        } else {
          $query_args['s'] = $search;
        }
      }
      $posts = get_posts($query_args);
    }

    $results = [];
    foreach ($posts as $post) {
      if (! $post instanceof \WP_Post) {
        continue;
      }
      $results[] = ['id' => (string) $post->ID, 'text' => (string) $post->post_title];
    }

    wp_send_json(['results' => $results]);
  }
}

add_action('wp_ajax_eai_featured_projects_search_posts', 'eai_ajax_featured_projects_search_posts');
