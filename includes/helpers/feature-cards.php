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

if (! function_exists('eai_feature_cards_query_by_term')) {
  /**
   * @param array<int, int> $exclude_ids
   * @return array<int, int>
   */
  function eai_feature_cards_query_by_term(
    int $term_id,
    string $taxonomy,
    string $post_type,
    int $current_post_id,
    array $exclude_ids,
    int $limit
  ): array {
    if ($term_id <= 0 || $limit <= 0) {
      return [];
    }

    $not_in = array_values(array_unique(array_merge(
      $current_post_id > 0 ? [$current_post_id] : [],
      $exclude_ids
    )));

    $query_args = [
      'post_type' => $post_type,
      'post_status' => 'publish',
      'posts_per_page' => $limit,
      'orderby' => 'menu_order title',
      'order' => 'ASC',
      'fields' => 'ids',
      'no_found_rows' => true,
      'ignore_sticky_posts' => true,
      'tax_query' => [
        [
          'taxonomy' => $taxonomy,
          'field' => 'term_id',
          'terms' => [$term_id],
        ],
      ],
    ];

    if ($not_in !== []) {
      $query_args['post__not_in'] = $not_in;
    }

    $query = new \WP_Query($query_args);

    if (! $query->have_posts()) {
      return [];
    }

    $ids = [];

    foreach ($query->posts as $id) {
      $id = absint($id);
      if ($id > 0) {
        $ids[] = $id;
      }
    }

    return $ids;
  }
}

if (! function_exists('eai_feature_cards_collect_from_term')) {
  /**
   * @param array<int, int> $found_ids
   * @return array<int, int>
   */
  function eai_feature_cards_collect_from_term(
    \WP_Term $term,
    string $taxonomy,
    string $post_type,
    int $current_post_id,
    array $found_ids,
    int $limit
  ): array {
    $remaining = $limit - count($found_ids);
    if ($remaining <= 0) {
      return $found_ids;
    }

    $batch = eai_feature_cards_query_by_term(
      (int) $term->term_id,
      $taxonomy,
      $post_type,
      $current_post_id,
      $found_ids,
      $remaining
    );

    foreach ($batch as $id) {
      if (! in_array($id, $found_ids, true)) {
        $found_ids[] = $id;
      }

      if (count($found_ids) >= $limit) {
        return $found_ids;
      }
    }

    $taxonomy_obj = get_taxonomy($taxonomy);
    if (
      $taxonomy_obj
      && ! empty($taxonomy_obj->hierarchical)
      && (int) $term->parent > 0
      && count($found_ids) < $limit
    ) {
      $parent = get_term((int) $term->parent, $taxonomy);
      if ($parent instanceof \WP_Term && ! is_wp_error($parent)) {
        $found_ids = eai_feature_cards_collect_from_term(
          $parent,
          $taxonomy,
          $post_type,
          $current_post_id,
          $found_ids,
          $limit
        );
      }
    }

    return $found_ids;
  }
}

if (! function_exists('eai_feature_cards_query_taxonomy_fallback')) {
  /**
   * @param array<int, int> $exclude_ids
   * @return array<int, int>
   */
  function eai_feature_cards_query_taxonomy_fallback(
    string $taxonomy,
    string $post_type,
    array $exclude_ids,
    int $limit
  ): array {
    if ($limit <= 0) {
      return [];
    }

    $not_in = array_values(array_unique(array_filter(array_map('intval', $exclude_ids))));

    $query_args = [
      'post_type' => $post_type,
      'post_status' => 'publish',
      'posts_per_page' => $limit,
      'orderby' => 'menu_order title',
      'order' => 'ASC',
      'fields' => 'ids',
      'no_found_rows' => true,
      'ignore_sticky_posts' => true,
      'tax_query' => [
        [
          'taxonomy' => $taxonomy,
          'operator' => 'EXISTS',
        ],
      ],
    ];

    if ($not_in !== []) {
      $query_args['post__not_in'] = $not_in;
    }

    $query = new \WP_Query($query_args);

    return array_map('intval', $query->posts);
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

      $current_post_id = (int) get_queried_object_id();
      if ($current_post_id <= 0) {
        $current_post_id = (int) get_the_ID();
      }

      $found_ids = [];

      if ($current_post_id > 0) {
        $terms = wp_get_post_terms($current_post_id, $taxonomy);
        if (! is_wp_error($terms) && $terms !== []) {
          $filtered = [];
          foreach ($terms as $term) {
            if (! ($term instanceof \WP_Term)) {
              continue;
            }
            if (eai_related_posts_is_excluded_term($term, $taxonomy)) {
              continue;
            }
            $filtered[] = $term;
          }

          if ($filtered !== []) {
            $sorted = eai_related_posts_sort_terms($filtered);

            foreach ($sorted as $term) {
              if (count($found_ids) >= $posts_per_page) {
                break;
              }

              $found_ids = eai_feature_cards_collect_from_term(
                $term,
                $taxonomy,
                $post_type,
                $current_post_id,
                $found_ids,
                $posts_per_page
              );
            }
          }
        }
      }

      if (count($found_ids) < $posts_per_page) {
        $exclude = $found_ids;
        if ($current_post_id > 0) {
          $exclude[] = $current_post_id;
        }

        $batch = eai_feature_cards_query_taxonomy_fallback(
          $taxonomy,
          $post_type,
          $exclude,
          $posts_per_page - count($found_ids)
        );

        foreach ($batch as $id) {
          if (! in_array($id, $found_ids, true)) {
            $found_ids[] = $id;
          }

          if (count($found_ids) >= $posts_per_page) {
            break;
          }
        }
      }

      return array_slice($found_ids, 0, $posts_per_page);
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

