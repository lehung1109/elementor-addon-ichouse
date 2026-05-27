<?php
if (! defined('ABSPATH')) {
  exit;
}

if (! function_exists('eai_get_widget_category_slug')) {
  function eai_get_widget_category_slug(): string
  {
    return 'eai-ichouse';
  }
}

if (! function_exists('eai_get_widget_categories')) {
  /**
   * Elementor panel category for all ICHouse api-rc widgets.
   *
   * @return array<int, string>
   */
  function eai_get_widget_categories(): array
  {
    return [eai_get_widget_category_slug()];
  }
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

if (! function_exists('eai_rc_map_link')) {
  /**
   * @param array<string, mixed> $link
   * @return array{url: string, is_external: bool, nofollow: bool}
   */
  function eai_rc_map_link(array $link): array
  {
    return [
      'url' => (string) ($link['url'] ?? ''),
      'is_external' => ! empty($link['is_external']),
      'nofollow' => ! empty($link['nofollow']),
    ];
  }
}

if (! function_exists('eai_rc_map_media_model')) {
  /**
   * Map Elementor media + dimensions (+ optional link) to api-rc MediaModel.
   *
   * @param array<string, mixed> $media
   * @param array<string, mixed> $dimensions
   * @param array<string, mixed>|null $link
   * @return array<string, mixed>
   */
  function eai_rc_map_media_model(
    array $media,
    array $dimensions = [],
    ?array $link = null,
    string $size = 'full'
  ): array {
    $resolved = eai_get_media_image_url($media, $size);

    $width = (int) ($dimensions['width'] ?? 0);
    $height = (int) ($dimensions['height'] ?? 0);

    if ($width <= 0) {
      $width = (int) ($resolved['width'] ?? 0);
    }
    if ($height <= 0) {
      $height = (int) ($resolved['height'] ?? 0);
    }

    $alt = '';
    if (! empty($media['alt'])) {
      $alt = (string) $media['alt'];
    } elseif (! empty($media['id'])) {
      $alt = (string) get_post_meta((int) $media['id'], '_wp_attachment_image_alt', true);
    }

    $model = [
      'url' => (string) ($resolved['url'] ?: ($media['url'] ?? '')),
      'alt' => $alt,
      'display_dimensions' => [
        'width' => $width,
        'height' => $height,
      ],
    ];

    if ($link !== null && ! empty($link['url'])) {
      $model['link'] = eai_rc_map_link($link);
    }

    return $model;
  }
}

if (! function_exists('eai_rc_map_header_inner_info_list')) {
  /**
   * @param array<int, array<string, mixed>> $info_list
   * @return array<int, array<string, mixed>>
   */
  function eai_rc_map_header_inner_info_list(array $info_list): array
  {
    $mapped = [];

    foreach ($info_list as $item) {
      if (! is_array($item)) {
        continue;
      }

      $mapped[] = [
        'icon' => eai_rc_map_media_model(
          is_array($item['icon'] ?? null) ? $item['icon'] : [],
          is_array($item['icon_dimensions'] ?? null) ? $item['icon_dimensions'] : []
        ),
        'text' => (string) ($item['text'] ?? ''),
      ];
    }

    return $mapped;
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

if (! function_exists('eai_rc_map_carousel_slides')) {
  /**
   * @param array<int, array<string, mixed>> $slides
   * @return array<int, array<string, mixed>>
   */
  function eai_rc_map_carousel_slides(array $slides): array
  {
    $mapped = [];

    foreach ($slides as $slide) {
      if (! is_array($slide)) {
        continue;
      }

      $image = is_array($slide['image'] ?? null) ? $slide['image'] : [];
      $resolution = (string) ($slide['image_resolution'] ?? 'large');
      $link = is_array($slide['link'] ?? null) && ! empty($slide['link']['url'])
        ? $slide['link']
        : null;

      $mapped[] = [
        'image' => eai_rc_map_media_model($image, [], $link, $resolution),
      ];
    }

    return $mapped;
  }
}

if (! function_exists('eai_rc_map_partner_logos')) {
  /**
   * Map Elementor repeater rows to PartnerLogosModel.logos (MediaModel[], no link).
   *
   * @param array<int, array<string, mixed>> $logos
   * @return array<int, array<string, mixed>>
   */
  function eai_rc_map_partner_logos(array $logos): array
  {
    $mapped = [];

    foreach ($logos as $row) {
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

      $mapped[] = $media;
    }

    return $mapped;
  }
}

if (! function_exists('eai_get_public_post_type_options')) {
  /**
   * @return array<string, string>
   */
  function eai_get_public_post_type_options(): array
  {
    $options = [];

    foreach (get_post_types(['public' => true], 'objects') as $post_type) {
      if ($post_type->name === 'attachment') {
        continue;
      }

      $options[$post_type->name] = $post_type->labels->singular_name;
    }

    return $options;
  }
}

if (! function_exists('eai_get_taxonomy_options_for_post_type')) {
  /**
   * @return array<string, string>
   */
  function eai_get_taxonomy_options_for_post_type(string $post_type): array
  {
    $options = [];

    foreach (get_object_taxonomies($post_type, 'objects') as $taxonomy) {
      if (empty($taxonomy->public) && empty($taxonomy->show_ui)) {
        continue;
      }

      $options[$taxonomy->name] = $taxonomy->labels->singular_name;
    }

    return $options;
  }
}

if (! function_exists('eai_get_public_taxonomy_options')) {
  /**
   * @return array<string, string>
   */
  function eai_get_public_taxonomy_options(): array
  {
    $options = [];

    foreach (get_taxonomies(['public' => true], 'objects') as $taxonomy) {
      $options[$taxonomy->name] = $taxonomy->labels->singular_name;
    }

    return $options;
  }
}

if (! function_exists('eai_get_post_short_description')) {
  function eai_get_post_short_description(\WP_Post $post, int $max_length = 120): string
  {
    if (has_excerpt($post)) {
      $text = get_the_excerpt($post);
    } else {
      $text = wp_strip_all_tags($post->post_content);
    }

    $text = trim(preg_replace('/\s+/u', ' ', $text));

    if ($max_length < 1) {
      return $text;
    }

    return wp_html_excerpt($text, $max_length, '…');
  }
}

if (! function_exists('eai_rc_map_feature_card_from_post')) {
  /**
   * @return array<string, mixed>|null
   */
  function eai_rc_map_feature_card_from_post(
    \WP_Post $post,
    string $image_size = 'large',
    int $excerpt_length = 120
  ): ?array {
    $thumbnail_id = (int) get_post_thumbnail_id($post);
    if ($thumbnail_id <= 0) {
      return null;
    }

    $media = eai_rc_map_media_model(['id' => $thumbnail_id], [], null, $image_size);
    if (empty($media['url'])) {
      return null;
    }

    return [
      'image' => $media,
      'title' => get_the_title($post),
      'description' => eai_get_post_short_description($post, $excerpt_length),
      'link' => eai_rc_map_link(['url' => get_permalink($post)]),
    ];
  }
}

if (! function_exists('eai_feature_cards_resolve_post_ids')) {
  /**
   * @param array<string, mixed> $settings
   * @return array<int, int>
   */
  function eai_feature_cards_resolve_post_ids(array $settings): array
  {
    $post_type = sanitize_key((string) ($settings['post_type'] ?? 'post'));
    if ($post_type === '') {
      $post_type = 'post';
    }

    $source = (string) ($settings['content_source'] ?? 'manual');

    if ($source === 'taxonomy') {
      $taxonomy = sanitize_key((string) ($settings['taxonomy'] ?? ''));

      if ($taxonomy === '' || ! is_object_in_taxonomy($post_type, $taxonomy)) {
        return [];
      }

      $posts_per_page = (int) ($settings['taxonomy_posts_per_page'] ?? 6);
      if ($posts_per_page < 1) {
        $posts_per_page = 6;
      }

      $query = new \WP_Query([
        'post_type' => $post_type,
        'post_status' => 'publish',
        'posts_per_page' => $posts_per_page,
        'orderby' => 'menu_order title',
        'order' => 'ASC',
        'fields' => 'ids',
        'no_found_rows' => true,
        'tax_query' => [
          [
            'taxonomy' => $taxonomy,
            'operator' => 'EXISTS',
          ],
        ],
      ]);

      return array_map('intval', $query->posts);
    }

    $selected = array_filter(array_map('intval', (array) ($settings['selected_posts'] ?? [])));
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

    return array_values(array_filter(
      $selected,
      static fn(int $id): bool => isset($valid_map[$id])
    ));
  }
}

if (! function_exists('eai_rc_map_feature_cards_from_posts')) {
  /**
   * @param array<int, int> $post_ids
   * @return array<int, array<string, mixed>>
   */
  function eai_rc_map_feature_cards_from_posts(
    array $post_ids,
    string $image_size = 'large',
    int $excerpt_length = 120
  ): array {
    $mapped = [];

    foreach ($post_ids as $post_id) {
      $post = get_post((int) $post_id);
      if (! $post instanceof \WP_Post || $post->post_status !== 'publish') {
        continue;
      }

      $card = eai_rc_map_feature_card_from_post($post, $image_size, $excerpt_length);
      if ($card !== null) {
        $mapped[] = $card;
      }
    }

    return $mapped;
  }
}

if (! function_exists('eai_rc_map_feature_cards_carousel_items')) {
  /**
   * Map Elementor widget settings to FeatureCardsCarouselModel.items for api-rc.
   *
   * @param array<string, mixed> $settings
   * @return array<int, array<string, mixed>>
   */
  function eai_rc_map_feature_cards_carousel_items(array $settings): array
  {
    $image_size = (string) ($settings['image_resolution'] ?? 'large');
    $excerpt_length = (int) ($settings['excerpt_length'] ?? 120);
    if ($excerpt_length < 1) {
      $excerpt_length = 120;
    }

    $post_ids = eai_feature_cards_resolve_post_ids($settings);

    return eai_rc_map_feature_cards_from_posts($post_ids, $image_size, $excerpt_length);
  }
}

if (! function_exists('eai_feature_cards_editor_can_query')) {
  function eai_feature_cards_editor_can_query(): bool
  {
    return current_user_can('edit_posts');
  }
}

if (! function_exists('eai_feature_cards_verify_editor_ajax')) {
  function eai_feature_cards_verify_editor_ajax(): void
  {
    if (! eai_feature_cards_editor_can_query()) {
      wp_send_json_error(['message' => 'Forbidden'], 403);
    }

    $nonce = isset($_REQUEST['nonce']) ? sanitize_text_field(wp_unslash($_REQUEST['nonce'])) : '';
    if (! wp_verify_nonce($nonce, 'eai_feature_cards_editor')) {
      wp_send_json_error(['message' => 'Invalid nonce'], 403);
    }
  }
}

if (! function_exists('eai_ajax_feature_cards_search_posts')) {
  function eai_ajax_feature_cards_search_posts(): void
  {
    eai_feature_cards_verify_editor_ajax();

    $post_type = sanitize_key((string) ($_REQUEST['post_type'] ?? ''));
    $post_types = $post_type !== ''
      ? [$post_type]
      : array_keys(eai_get_public_post_type_options());

    $search = isset($_REQUEST['q']) ? sanitize_text_field(wp_unslash($_REQUEST['q'])) : '';
    $ids = [];

    if (isset($_REQUEST['ids'])) {
      $raw_ids = wp_unslash($_REQUEST['ids']);
      if (is_array($raw_ids)) {
        $ids = array_map('intval', $raw_ids);
      } else {
        $ids = array_map('intval', explode(',', (string) $raw_ids));
      }
    }

    $ids = array_filter($ids);
    $results = [];

    if (! empty($ids)) {
      $posts = get_posts([
        'post_type' => $post_types,
        'post_status' => 'publish',
        'post__in' => $ids,
        'posts_per_page' => -1,
        'orderby' => 'post__in',
      ]);
    } else {
      $query_args = [
        'post_type' => $post_types,
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

    foreach ($posts as $post) {
      if (! $post instanceof \WP_Post) {
        continue;
      }

      $results[] = [
        'id' => (string) $post->ID,
        'text' => $post->post_title,
      ];
    }

    wp_send_json(['results' => $results]);
  }
}

add_action('wp_ajax_eai_feature_cards_search_posts', 'eai_ajax_feature_cards_search_posts');

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

if (! function_exists('eai_rc_footer_facebook_embed_html')) {
  /**
   * Facebook Page Plugin iframe HTML for FooterFanpages embed slot.
   */
  function eai_rc_footer_facebook_embed_html(string $page_url): string
  {
    $href = esc_url($page_url);
    if ($href === '') {
      return '';
    }

    $src = 'https://www.facebook.com/plugins/page.php?href=' . rawurlencode($page_url)
      . '&tabs=&width=340&height=130&small_header=false&adapt_container_width=true'
      . '&hide_cover=false&show_facepile=true';

    return '<iframe src="' . esc_attr($src) . '" width="340" height="130" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowfullscreen="true" allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share"></iframe>';
  }
}

if (! function_exists('eai_rc_map_footer_link_items')) {
  /**
   * @param array<int, array<string, mixed>> $items
   * @return array<int, array{label: string, link: array<string, mixed>}>
   */
  function eai_rc_map_footer_link_items(array $items): array
  {
    $mapped = [];

    foreach ($items as $item) {
      if (! is_array($item)) {
        continue;
      }

      $label = trim((string) ($item['label'] ?? ''));
      $link = is_array($item['link'] ?? null) ? $item['link'] : [];

      if ($label === '' || empty($link['url'])) {
        continue;
      }

      $mapped[] = [
        'label' => $label,
        'link' => eai_rc_map_link($link),
      ];
    }

    return $mapped;
  }
}

if (! function_exists('eai_rc_map_footer_menu_columns')) {
  /**
   * @param array<int, array{title: string, links: array<int, array<string, mixed>>}> $columns
   * @return array<int, array<string, mixed>>
   */
  function eai_rc_map_footer_menu_columns(array $columns): array
  {
    $mapped = [];

    foreach ($columns as $column) {
      if (! is_array($column)) {
        continue;
      }

      $title = trim((string) ($column['title'] ?? ''));
      $links = is_array($column['links'] ?? null) ? $column['links'] : [];
      $mapped_links = eai_rc_map_footer_link_items($links);

      if ($title === '' && empty($mapped_links)) {
        continue;
      }

      $mapped[] = [
        'title' => $title,
        'links' => $mapped_links,
      ];
    }

    return $mapped;
  }
}

if (! function_exists('eai_rc_map_footer_social_links')) {
  /**
   * @param array<int, array<string, mixed>> $links
   * @return array<int, array<string, mixed>>
   */
  function eai_rc_map_footer_social_links(array $links): array
  {
    $mapped = [];

    foreach ($links as $row) {
      if (! is_array($row)) {
        continue;
      }

      $url_control = is_array($row['link'] ?? null) ? $row['link'] : [];
      if (empty($url_control['url'])) {
        continue;
      }

      $icon = is_array($row['icon'] ?? null) ? $row['icon'] : [];
      $resolution = (string) ($row['icon_resolution'] ?? 'thumbnail');
      $icon_media = eai_rc_map_media_model(
        $icon,
        ['width' => 24, 'height' => 24],
        null,
        $resolution
      );

      if (empty($icon_media['url'])) {
        continue;
      }

      $alt_override = trim((string) ($row['icon_alt'] ?? ''));
      if ($alt_override !== '') {
        $icon_media['alt'] = $alt_override;
      }

      $mapped[] = [
        'icon' => $icon_media,
        'link' => eai_rc_map_link($url_control),
      ];
    }

    return $mapped;
  }
}

if (! function_exists('eai_rc_map_footer_contact_blocks')) {
  /**
   * @param array<int, array<string, mixed>> $blocks
   * @return array<int, array{title: string, contentHtml: string}>
   */
  function eai_rc_map_footer_contact_blocks(array $blocks): array
  {
    $mapped = [];

    foreach ($blocks as $block) {
      if (! is_array($block)) {
        continue;
      }

      $title = trim((string) ($block['title'] ?? ''));
      $content = (string) ($block['content'] ?? '');

      if ($title === '' && trim(wp_strip_all_tags($content)) === '') {
        continue;
      }

      $mapped[] = [
        'title' => $title,
        'contentHtml' => $content,
      ];
    }

    return $mapped;
  }
}

if (! function_exists('eai_rc_map_footer_fanpage_embeds')) {
  /**
   * @param array<int, array<string, mixed>> $embeds
   * @return array<int, array{embedHtml: string}>
   */
  function eai_rc_map_footer_fanpage_embeds(array $embeds): array
  {
    $mapped = [];

    foreach ($embeds as $row) {
      if (! is_array($row)) {
        continue;
      }

      $embed_html = trim((string) ($row['embed_html'] ?? ''));
      $facebook_control = is_array($row['facebook_page_url'] ?? null)
        ? $row['facebook_page_url']
        : [];
      $facebook_url = trim((string) ($facebook_control['url'] ?? ''));

      if ($embed_html === '' && $facebook_url !== '') {
        $embed_html = eai_rc_footer_facebook_embed_html($facebook_url);
      }

      if ($embed_html === '') {
        continue;
      }

      $mapped[] = [
        'embedHtml' => $embed_html,
      ];
    }

    return $mapped;
  }
}

if (! function_exists('eai_rc_map_footer_brand')) {
  /**
   * @param array<string, mixed> $settings
   * @return array<string, mixed>
   */
  function eai_rc_map_footer_brand(array $settings): array
  {
    $logo = is_array($settings['brand_logo'] ?? null) ? $settings['brand_logo'] : [];
    $logo_resolution = (string) ($settings['brand_logo_resolution'] ?? 'medium');
    $logo_link = is_array($settings['brand_logo_link'] ?? null) ? $settings['brand_logo_link'] : [];
    $badges = is_array($settings['brand_badges'] ?? null) ? $settings['brand_badges'] : [];

    return [
      'logo' => eai_rc_map_media_model(
        $logo,
        ['width' => 220, 'height' => 80],
        ! empty($logo_link['url']) ? $logo_link : null,
        $logo_resolution
      ),
      'descriptionHtml' => (string) ($settings['brand_description'] ?? ''),
      'badges' => eai_rc_map_partner_logos($badges),
      'hotlineLabel' => trim((string) ($settings['brand_hotline_label'] ?? '')),
      'hotlineText' => trim((string) ($settings['brand_hotline_text'] ?? '')),
      'hotline' => eai_rc_map_link(
        is_array($settings['brand_hotline_link'] ?? null) ? $settings['brand_hotline_link'] : []
      ),
    ];
  }
}

if (! function_exists('eai_project_showcase_config_from_settings')) {
  /**
   * @param array<string, mixed> $settings
   * @return array<string, mixed>
   */
  function eai_project_showcase_config_from_settings(array $settings): array
  {
    $taxonomies = [];
    $raw = is_array($settings['taxonomies'] ?? null) ? $settings['taxonomies'] : [];
    foreach ($raw as $row) {
      if (! is_array($row)) {
        continue;
      }

      $key = sanitize_key((string) ($row['key'] ?? ''));
      $label = sanitize_text_field((string) ($row['label'] ?? ''));
      $taxonomy = sanitize_key((string) ($row['taxonomy'] ?? ''));

      if ($key === '' || $taxonomy === '') {
        continue;
      }

      $taxonomies[] = [
        'key' => $key,
        'label' => $label !== '' ? $label : $key,
        'taxonomy' => $taxonomy,
      ];
    }

    return [
      'post_type' => sanitize_key((string) ($settings['post_type'] ?? '')),
      'taxonomies' => $taxonomies,
      'posts_per_page' => (int) ($settings['posts_per_page'] ?? -1),
      'image_size' => sanitize_key((string) ($settings['image_size'] ?? 'large')),
    ];
  }
}

if (! function_exists('eai_project_showcase_default_filters_from_settings')) {
  /**
   * @param array<string, mixed> $settings
   * @return array<string, string>
   */
  function eai_project_showcase_default_filters_from_settings(array $settings): array
  {
    $filters = [];

    $defaults = is_array($settings['default_filters'] ?? null) ? $settings['default_filters'] : [];
    foreach ($defaults as $row) {
      if (! is_array($row)) {
        continue;
      }

      $key = sanitize_key((string) ($row['key'] ?? ''));
      if ($key === '') {
        continue;
      }

      $term = sanitize_title((string) ($row['term'] ?? ''));
      if ($term === '') {
        continue;
      }

      $filters[$key] = $term;
    }

    return $filters;
  }
}

if (! function_exists('eai_project_showcase_normalize_filters')) {
  /**
   * @param array<string, mixed> $raw
   * @return array<string, string>
   */
  function eai_project_showcase_normalize_filters(array $raw): array
  {
    $filters = [];

    foreach ($raw as $key => $value) {
      $key = sanitize_key((string) $key);
      if ($key === '' || $value === '' || $value === null) {
        continue;
      }
      $filters[$key] = sanitize_title((string) $value);
    }

    return $filters;
  }
}

if (! function_exists('eai_project_showcase_filter_endpoint')) {
  /**
   * @param array<string, mixed> $config
   */
  function eai_project_showcase_filter_endpoint(array $config): string
  {
    $query = [
      'post_type' => $config['post_type'] ?? '',
      'image_size' => $config['image_size'] ?? 'large',
    ];

    $taxonomies = is_array($config['taxonomies'] ?? null) ? $config['taxonomies'] : [];
    foreach ($taxonomies as $idx => $row) {
      if (! is_array($row)) {
        continue;
      }
      $key = sanitize_key((string) ($row['key'] ?? ''));
      $taxonomy = sanitize_key((string) ($row['taxonomy'] ?? ''));
      if ($key === '' || $taxonomy === '') {
        continue;
      }
      // Encode dynamic taxonomy mapping into query string.
      $query["taxonomies[$idx][key]"] = $key;
      $query["taxonomies[$idx][taxonomy]"] = $taxonomy;
    }

    if (! empty($config['posts_per_page']) && (int) $config['posts_per_page'] > 0) {
      $query['posts_per_page'] = (int) $config['posts_per_page'];
    }

    return add_query_arg(
      array_filter($query, static fn($value) => $value !== '' && $value !== null),
      rest_url('eai/v1/projects/filter')
    );
  }
}

if (! function_exists('eai_get_taxonomy_terms_as_filter_options')) {
  /**
   * @return array<int, array{value: string, label: string}>
   */
  function eai_get_taxonomy_terms_as_filter_options(string $taxonomy): array
  {
    if ($taxonomy === '') {
      return [];
    }

    $terms = get_terms([
      'taxonomy' => $taxonomy,
      'hide_empty' => false,
    ]);

    if (is_wp_error($terms) || empty($terms)) {
      return [];
    }

    $options = [];

    foreach ($terms as $term) {
      $options[] = [
        'value' => $term->slug,
        'label' => $term->name,
      ];
    }

    return $options;
  }
}

if (! function_exists('eai_project_showcase_get_filter_options')) {
  /**
   * @param array<string, mixed> $config
   * @return array<string, array<int, array{value: string, label: string}>>
   */
  function eai_project_showcase_get_filter_options(array $config): array
  {
    $out = [];
    $taxonomies = is_array($config['taxonomies'] ?? null) ? $config['taxonomies'] : [];
    foreach ($taxonomies as $row) {
      if (! is_array($row)) {
        continue;
      }

      $key = sanitize_key((string) ($row['key'] ?? ''));
      $taxonomy = sanitize_key((string) ($row['taxonomy'] ?? ''));

      if ($key === '' || $taxonomy === '') {
        continue;
      }

      $out[$key] = eai_get_taxonomy_terms_as_filter_options($taxonomy);
    }

    return $out;
  }
}

if (! function_exists('eai_project_showcase_first_term_for_post')) {
  function eai_project_showcase_first_term_for_post(int $post_id, string $taxonomy): ?WP_Term
  {
    if ($taxonomy === '') {
      return null;
    }

    $terms = get_the_terms($post_id, $taxonomy);

    if (empty($terms) || is_wp_error($terms)) {
      return null;
    }

    return $terms[0];
  }
}

if (! function_exists('eai_project_showcase_bedrooms_from_term')) {
  function eai_project_showcase_bedrooms_from_term(?WP_Term $term): int
  {
    if (! $term instanceof WP_Term) {
      return 0;
    }

    if (is_numeric($term->slug)) {
      return (int) $term->slug;
    }

    if (preg_match('/(\d+)/', $term->slug, $matches)) {
      return (int) $matches[1];
    }

    return 0;
  }
}

if (! function_exists('eai_rc_map_project_showcase_item')) {
  /**
   * @param array<string, mixed> $config
   * @return array<string, mixed>
   */
  function eai_rc_map_project_showcase_item(WP_Post $post, array $config): array
  {
    $terms = [];
    $taxonomies = is_array($config['taxonomies'] ?? null) ? $config['taxonomies'] : [];
    foreach ($taxonomies as $row) {
      if (! is_array($row)) {
        continue;
      }

      $key = sanitize_key((string) ($row['key'] ?? ''));
      $taxonomy = sanitize_key((string) ($row['taxonomy'] ?? ''));
      if ($key === '' || $taxonomy === '') {
        continue;
      }

      $term = eai_project_showcase_first_term_for_post((int) $post->ID, $taxonomy);
      $terms[$key] = [
        'value' => $term instanceof WP_Term ? $term->slug : '',
        'label' => $term instanceof WP_Term ? $term->name : '',
      ];
    }

    $thumbnail_id = get_post_thumbnail_id($post);
    $image_size = (string) ($config['image_size'] ?? 'large');
    $image = $thumbnail_id
      ? eai_rc_map_media_model(['id' => $thumbnail_id], [], null, $image_size)
      : eai_rc_map_media_model(
        ['url' => 'https://placehold.co/600x400/png'],
        ['width' => 600, 'height' => 400],
        null,
        $image_size
      );

    return [
      'id' => (string) $post->ID,
      'title' => get_the_title($post),
      'image' => $image,
      'terms' => $terms,
    ];
  }
}

if (! function_exists('eai_project_showcase_build_query_args')) {
  /**
   * @param array<string, mixed> $config
   * @param array<string, string> $filters
   * @return array<string, mixed>
   */
  function eai_project_showcase_build_query_args(array $config, array $filters): array
  {
    $posts_per_page = (int) ($config['posts_per_page'] ?? -1);

    $args = [
      'post_type' => $config['post_type'],
      'post_status' => 'publish',
      'posts_per_page' => $posts_per_page > 0 ? $posts_per_page : -1,
      'orderby' => 'date',
      'order' => 'DESC',
      'ignore_sticky_posts' => true,
    ];

    $tax_query = [];

    $taxonomies = is_array($config['taxonomies'] ?? null) ? $config['taxonomies'] : [];
    foreach ($taxonomies as $row) {
      if (! is_array($row)) {
        continue;
      }

      $key = sanitize_key((string) ($row['key'] ?? ''));
      $taxonomy = sanitize_key((string) ($row['taxonomy'] ?? ''));

      if ($key === '' || $taxonomy === '' || empty($filters[$key])) {
        continue;
      }

      $tax_query[] = [
        'taxonomy' => $taxonomy,
        'field' => 'slug',
        'terms' => [$filters[$key]],
      ];
    }

    if (count($tax_query) > 1) {
      $tax_query['relation'] = 'AND';
    }

    if (! empty($tax_query)) {
      $args['tax_query'] = $tax_query;
    }

    return $args;
  }
}

if (! function_exists('eai_project_showcase_query_and_map')) {
  /**
   * @param array<string, mixed> $config
   * @param array<string, string> $filters
   * @return array<int, array<string, mixed>>
   */
  function eai_project_showcase_query_and_map(array $config, array $filters = []): array
  {
    if (empty($config['post_type'])) {
      return [];
    }

    $query = new WP_Query(eai_project_showcase_build_query_args($config, $filters));
    $items = [];

    if ($query->have_posts()) {
      foreach ($query->posts as $post) {
        if ($post instanceof WP_Post) {
          $items[] = eai_rc_map_project_showcase_item($post, $config);
        }
      }
    }

    wp_reset_postdata();

    return $items;
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
