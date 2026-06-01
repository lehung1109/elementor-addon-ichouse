<?php
if (! defined('ABSPATH')) {
  exit;
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

if (! function_exists('eai_feature_cards_resolve_selected_term_slugs')) {
  /**
   * @param array<string, mixed> $settings
   * @return array<int, string>
   */
  function eai_feature_cards_resolve_selected_term_slugs(array $settings): array
  {
    $taxonomy = sanitize_key((string) ($settings['taxonomy'] ?? ''));
    if ($taxonomy === '') {
      return [];
    }

    $raw = $settings['taxonomy_terms_' . $taxonomy] ?? [];
    if (! is_array($raw)) {
      $raw = [];
    }

    $slugs = [];
    foreach ($raw as $value) {
      $slug = sanitize_title((string) $value);
      if ($slug !== '') {
        $slugs[] = $slug;
      }
    }

    $slugs = array_values(array_unique($slugs));
    if (empty($slugs)) {
      return [];
    }

    $valid = get_terms([
      'taxonomy' => $taxonomy,
      'slug' => $slugs,
      'hide_empty' => false,
      'fields' => 'slugs',
    ]);

    if (is_wp_error($valid) || empty($valid)) {
      return [];
    }

    return array_values(array_intersect($slugs, array_map('strval', $valid)));
  }
}

if (! function_exists('eai_feature_cards_query_latest_by_taxonomy')) {
  /**
   * @param array<int, string> $term_slugs
   * @return array<int, int>
   */
  function eai_feature_cards_query_latest_by_taxonomy(
    string $taxonomy,
    string $post_type,
    int $limit,
    int $exclude_post_id = 0,
    array $term_slugs = [],
    int $offset = 0,
    bool $include_children = false
  ): array {
    if ($limit <= 0) {
      return [];
    }

    $offset = max(0, $offset);

    $tax_clause = [
      'taxonomy' => $taxonomy,
    ];

    $term_slugs = array_values(array_filter(array_map(
      static fn($slug): string => sanitize_title((string) $slug),
      $term_slugs
    )));

    if (! empty($term_slugs)) {
      $tax_clause['field'] = 'slug';
      $tax_clause['terms'] = $term_slugs;
      $tax_clause['operator'] = 'IN';
    } else {
      $tax_clause['operator'] = 'EXISTS';
    }

    if ($include_children) {
      $tax_clause['include_children'] = true;
    }

    $query_args = [
      'post_type' => $post_type,
      'post_status' => 'publish',
      'posts_per_page' => $limit,
      'orderby' => 'date',
      'order' => 'DESC',
      'fields' => 'ids',
      'no_found_rows' => true,
      'ignore_sticky_posts' => true,
      'tax_query' => [$tax_clause],
    ];

    if ($exclude_post_id > 0) {
      $query_args['post__not_in'] = [$exclude_post_id];
    }

    if ($offset > 0) {
      $query_args['offset'] = $offset;
    }

    $query = new \WP_Query($query_args);

    return array_map('intval', $query->posts);
  }
}

if (! function_exists('eai_feature_cards_query_latest_by_post_type')) {
  /**
   * @return array<int, int>
   */
  function eai_feature_cards_query_latest_by_post_type(
    string $post_type,
    int $limit,
    int $offset = 0
  ): array {
    if ($limit <= 0 || $post_type === '') {
      return [];
    }

    $offset = max(0, $offset);

    $query_args = [
      'post_type' => $post_type,
      'post_status' => 'publish',
      'posts_per_page' => $limit,
      'orderby' => 'date',
      'order' => 'DESC',
      'fields' => 'ids',
      'no_found_rows' => true,
      'ignore_sticky_posts' => true,
    ];

    if ($offset > 0) {
      $query_args['offset'] = $offset;
    }

    $query = new \WP_Query($query_args);

    return array_map('intval', $query->posts);
  }
}

if (! function_exists('eai_feature_cards_get_public_post_type_slugs')) {
  /**
   * @return array<int, string>
   */
  function eai_feature_cards_get_public_post_type_slugs(): array
  {
    return array_keys(eai_get_public_post_type_options());
  }
}

if (! function_exists('eai_feature_cards_filter_public_post_types')) {
  /**
   * @param array<int, string> $post_types
   * @return array<int, string>
   */
  function eai_feature_cards_filter_public_post_types(array $post_types): array
  {
    $public = array_fill_keys(eai_feature_cards_get_public_post_type_slugs(), true);
    $filtered = [];

    foreach ($post_types as $post_type) {
      $post_type = sanitize_key((string) $post_type);
      if ($post_type === '' || ! post_type_exists($post_type)) {
        continue;
      }
      if (! isset($public[$post_type])) {
        continue;
      }
      $filtered[] = $post_type;
    }

    return array_values(array_unique($filtered));
  }
}

if (! function_exists('eai_feature_cards_resolve_archive_context')) {
  /**
   * @return array{kind: string, taxonomy?: string, term_slug?: string, post_types?: array<int, string>, post_type?: string}|null
   */
  function eai_feature_cards_resolve_archive_context(): ?array
  {
    if (is_category() || is_tag() || is_tax()) {
      $term = get_queried_object();
      if (! ($term instanceof \WP_Term)) {
        return null;
      }

      $taxonomy_obj = get_taxonomy($term->taxonomy);
      if (! $taxonomy_obj || empty($taxonomy_obj->object_type)) {
        return null;
      }

      $post_types = eai_feature_cards_filter_public_post_types($taxonomy_obj->object_type);
      if ($post_types === []) {
        return null;
      }

      return [
        'kind' => 'taxonomy',
        'taxonomy' => $term->taxonomy,
        'term_slug' => $term->slug,
        'post_types' => $post_types,
      ];
    }

    if (! is_post_type_archive()) {
      return null;
    }

    $post_type = '';

    $queried = get_queried_object();
    if ($queried instanceof \WP_Post_Type) {
      $post_type = sanitize_key($queried->name);
    }

    if ($post_type === '') {
      $query_var = get_query_var('post_type');
      if (is_array($query_var)) {
        $query_var = reset($query_var);
      }
      $post_type = sanitize_key((string) $query_var);
    }

    $public = eai_feature_cards_filter_public_post_types([$post_type]);
    if ($public === []) {
      return null;
    }

    return [
      'kind' => 'post_type',
      'post_type' => $public[0],
    ];
  }
}

if (! function_exists('eai_feature_cards_resolve_archive_post_type')) {
  /**
   * @param array<string, mixed> $settings
   * @param array{kind: string, taxonomy?: string, term_slug?: string, post_types?: array<int, string>, post_type?: string} $context
   */
  function eai_feature_cards_resolve_archive_post_type(array $settings, array $context): string
  {
    $panel = sanitize_key((string) ($settings['post_type'] ?? ''));

    if (($context['kind'] ?? '') === 'post_type') {
      $archive_post_type = sanitize_key((string) ($context['post_type'] ?? ''));
      if ($archive_post_type === '') {
        return 'post';
      }
      if ($panel !== '' && $panel === $archive_post_type) {
        return $archive_post_type;
      }

      return $archive_post_type;
    }

    $allowed = $context['post_types'] ?? [];
    if (! is_array($allowed)) {
      $allowed = [];
    }

    if ($panel !== '' && in_array($panel, $allowed, true)) {
      return $panel;
    }

    return $allowed[0] ?? 'post';
  }
}

if (! function_exists('eai_feature_cards_resolve_related_taxonomy_slugs')) {
  /**
   * @param array<string, mixed> $settings
   * @return array<int, string>
   */
  function eai_feature_cards_resolve_related_taxonomy_slugs(array $settings): array
  {
    $raw = $settings['related_taxonomies'] ?? [];
    if (! is_array($raw)) {
      return [];
    }

    $slugs = [];
    foreach ($raw as $slug) {
      $slug = sanitize_key((string) $slug);
      if ($slug !== '') {
        $slugs[] = $slug;
      }
    }

    return $slugs;
  }
}

if (! function_exists('eai_feature_cards_stop_fetch_when_incomplete')) {
  /**
   * @param array<string, mixed> $settings
   */
  function eai_feature_cards_stop_fetch_when_incomplete(array $settings): bool
  {
    return ($settings['stop_fetch_when_incomplete'] ?? '') === 'yes';
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

    if ($source === 'related') {
      $current_post_id = (int) get_queried_object_id();
      if ($current_post_id <= 0) {
        $current_post_id = (int) get_the_ID();
      }

      if ($current_post_id <= 0) {
        return [];
      }

      $limit = (int) ($settings['related_posts_max'] ?? 6);
      if ($limit < 1) {
        $limit = 6;
      }

      $posts_offset = max(0, (int) ($settings['posts_offset'] ?? 0));
      $fetch_limit = $limit + $posts_offset;

      $taxonomy_slugs = eai_feature_cards_resolve_related_taxonomy_slugs($settings);

      $ids = eai_related_posts_resolve(
        $current_post_id,
        $fetch_limit,
        $taxonomy_slugs,
        [
          'stop_when_incomplete' => eai_feature_cards_stop_fetch_when_incomplete($settings),
        ]
      );

      return array_slice($ids, $posts_offset, $limit);
    }

    if ($source === 'archive') {
      $ctx = eai_feature_cards_resolve_archive_context();
      if ($ctx === null) {
        return [];
      }

      $posts_per_page = (int) ($settings['taxonomy_posts_per_page'] ?? 6);
      if ($posts_per_page < 1) {
        $posts_per_page = 6;
      }

      $posts_offset = max(0, (int) ($settings['posts_offset'] ?? 0));

      if (($ctx['kind'] ?? '') === 'taxonomy') {
        $taxonomy = sanitize_key((string) ($ctx['taxonomy'] ?? ''));
        $term_slug = sanitize_title((string) ($ctx['term_slug'] ?? ''));

        if ($taxonomy === '' || $term_slug === '') {
          return [];
        }

        $resolved_post_type = eai_feature_cards_resolve_archive_post_type($settings, $ctx);

        if (! is_object_in_taxonomy($resolved_post_type, $taxonomy)) {
          return [];
        }

        return eai_feature_cards_query_latest_by_taxonomy(
          $taxonomy,
          $resolved_post_type,
          $posts_per_page,
          0,
          [$term_slug],
          $posts_offset,
          true
        );
      }

      $archive_post_type = sanitize_key((string) ($ctx['post_type'] ?? ''));
      if ($archive_post_type === '') {
        return [];
      }

      return eai_feature_cards_query_latest_by_post_type(
        $archive_post_type,
        $posts_per_page,
        $posts_offset
      );
    }

    if ($source === 'taxonomy') {
      $taxonomy = sanitize_key((string) ($settings['taxonomy'] ?? ''));

      if ($taxonomy === '' || ! is_object_in_taxonomy($post_type, $taxonomy)) {
        return [];
      }

      $posts_per_page = (int) ($settings['taxonomy_posts_per_page'] ?? 6);
      if ($posts_per_page < 1) {
        $posts_per_page = 6;
      }

      $current_post_id = (int) get_queried_object_id();
      if ($current_post_id <= 0) {
        $current_post_id = (int) get_the_ID();
      }

      $term_slugs = eai_feature_cards_resolve_selected_term_slugs($settings);

      $posts_offset = max(0, (int) ($settings['posts_offset'] ?? 0));

      return eai_feature_cards_query_latest_by_taxonomy(
        $taxonomy,
        $post_type,
        $posts_per_page,
        $current_post_id,
        $term_slugs,
        $posts_offset
      );
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

if (! function_exists('eai_feature_cards_grid_resolve_item_layout')) {
  /**
   * @param array<string, mixed> $settings
   */
  function eai_feature_cards_grid_resolve_item_layout(array $settings): string
  {
    $layout = (string) ($settings['item_layout'] ?? 'stack');

    return in_array($layout, ['stack', 'media-left'], true) ? $layout : 'stack';
  }
}

if (! function_exists('eai_feature_cards_grid_apply_item_layout')) {
  /**
   * @param array<int, array<string, mixed>> $items
   * @param array<string, mixed> $settings
   * @return array<int, array<string, mixed>>
   */
  function eai_feature_cards_grid_apply_item_layout(array $items, array $settings): array
  {
    if (eai_feature_cards_grid_resolve_item_layout($settings) !== 'media-left') {
      return $items;
    }

    foreach ($items as &$item) {
      $item['layout'] = 'media-left';
    }
    unset($item);

    return $items;
  }
}

if (! function_exists('eai_feature_cards_grid_clamp_columns')) {
  function eai_feature_cards_grid_clamp_columns(int $value, int $fallback): int
  {
    if ($value < 1) {
      return max(1, min(6, $fallback));
    }

    if ($value > 6) {
      return 6;
    }

    return $value;
  }
}

if (! function_exists('eai_rc_map_feature_cards_grid_items')) {
  /**
   * Map Elementor widget settings to FeatureCardsGridModel.items for api-rc.
   *
   * @param array<string, mixed> $settings
   * @return array<int, array<string, mixed>>
   */
  function eai_rc_map_feature_cards_grid_items(array $settings): array
  {
    $show_description = ($settings['show_description'] ?? 'yes') === 'yes';
    $image_size = (string) ($settings['image_resolution'] ?? 'large');
    $excerpt_length = (int) ($settings['excerpt_length'] ?? 120);
    if ($excerpt_length < 1) {
      $excerpt_length = 120;
    }

    $post_ids = eai_feature_cards_resolve_post_ids($settings);
    $items = eai_rc_map_feature_cards_from_posts($post_ids, $image_size, $excerpt_length);

    if (! $show_description) {
      foreach ($items as &$item) {
        unset($item['description']);
      }
      unset($item);
    }

    return eai_feature_cards_grid_apply_item_layout($items, $settings);
  }
}

if (! function_exists('eai_feature_cards_grid_get_rc_props')) {
  /**
   * @param array<string, mixed> $settings
   * @return array<string, mixed>
   */
  function eai_feature_cards_grid_get_rc_props(array $settings): array
  {
    $columns_tablet = (int) ($settings['columns_tablet'] ?? 2);
    $columns_desktop = (int) ($settings['columns_desktop'] ?? 3);
    $gap = (int) ($settings['gap'] ?? 16);
    if ($gap < 0) {
      $gap = 0;
    }

    return [
      'items' => eai_rc_map_feature_cards_grid_items($settings),
      'columnsTablet' => eai_feature_cards_grid_clamp_columns($columns_tablet, 2),
      'columnsDesktop' => eai_feature_cards_grid_clamp_columns($columns_desktop, 3),
      'gap' => $gap,
    ];
  }
}

if (! function_exists('eai_feature_cards_grid_get_editor_sample_props')) {
  /**
   * Static demo props for Elementor editor when no posts resolve to cards.
   *
   * @param array<string, mixed> $settings
   * @return array<string, mixed>
   */
  function eai_feature_cards_grid_get_editor_sample_props(array $settings): array
  {
    $show_description = ($settings['show_description'] ?? 'yes') === 'yes';
    $dimensions = ['width' => 400, 'height' => 280];

    $sample_items = [
      [
        'title' => 'Thiết kế nội thất',
        'description' => 'Tư vấn và thiết kế không gian sống hiện đại, tối ưu công năng và thẩm mỹ cho từng căn hộ.',
      ],
      [
        'title' => 'Thi công trọn gói',
        'description' => 'Đội ngũ thi công chuyên nghiệp, đảm bảo tiến độ và chất lượng theo bản vẽ thiết kế.',
      ],
      [
        'title' => 'Nội thất cao cấp',
      ],
      [
        'title' => 'Bảo trì & hậu mãi',
        'description' => 'Hỗ trợ bảo trì, nâng cấp không gian sau bàn giao với chính sách rõ ràng.',
      ],
      [
        'title' => 'Tư vấn phong thủy',
      ],
      [
        'title' => 'Thi công nhanh',
        'description' => 'Cam kết tiến độ thi công theo hợp đồng.',
      ],
    ];

    $items = [];
    foreach ($sample_items as $index => $sample) {
      $item = [
        'image' => [
          'url' => 'https://placehold.co/400x280/png',
          'alt' => 'Feature ' . ($index + 1),
          'display_dimensions' => $dimensions,
        ],
        'title' => $sample['title'],
        'link' => eai_rc_map_link(['url' => '#']),
      ];

      if ($show_description && ! empty($sample['description'])) {
        $item['description'] = $sample['description'];
      }

      $items[] = $item;
    }

    $items = eai_feature_cards_grid_apply_item_layout($items, $settings);

    $columns_tablet = (int) ($settings['columns_tablet'] ?? 2);
    $columns_desktop = (int) ($settings['columns_desktop'] ?? 3);
    $gap = (int) ($settings['gap'] ?? 16);
    if ($gap < 0) {
      $gap = 0;
    }

    return [
      'items' => $items,
      'columnsTablet' => eai_feature_cards_grid_clamp_columns($columns_tablet, 2),
      'columnsDesktop' => eai_feature_cards_grid_clamp_columns($columns_desktop, 3),
      'gap' => $gap,
    ];
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
        'posts_per_page' => -1,
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
