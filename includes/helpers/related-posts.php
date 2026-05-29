<?php
if (! defined('ABSPATH')) {
  exit;
}

if (! function_exists('eai_related_posts_excluded_category_slugs')) {
  /**
   * @return array<int, string>
   */
  function eai_related_posts_excluded_category_slugs(): array
  {
    $slugs = ['un-category', 'uncategorized'];

    /**
     * @param array<int, string> $slugs
     */
    return apply_filters('eai_related_posts_excluded_category_slugs', $slugs);
  }
}

if (! function_exists('eai_related_posts_is_excluded_term')) {
  function eai_related_posts_is_excluded_term(\WP_Term $term, string $taxonomy): bool
  {
    if ($taxonomy !== 'category') {
      return false;
    }

    return in_array($term->slug, eai_related_posts_excluded_category_slugs(), true);
  }
}

if (! function_exists('eai_related_posts_sort_terms')) {
  /**
   * Child terms (parent > 0) before parent terms.
   *
   * @param array<int, \WP_Term> $terms
   * @return array<int, \WP_Term>
   */
  function eai_related_posts_sort_terms(array $terms): array
  {
    $children = [];
    $parents = [];

    foreach ($terms as $term) {
      if (! ($term instanceof \WP_Term)) {
        continue;
      }

      if ((int) $term->parent > 0) {
        $children[] = $term;
      } else {
        $parents[] = $term;
      }
    }

    return array_merge($children, $parents);
  }
}

if (! function_exists('eai_related_posts_query_by_term')) {
  /**
   * @param array<int, int> $exclude_ids
   * @return array<int, int>
   */
  function eai_related_posts_query_by_term(
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

    $not_in = array_values(array_unique(array_merge([$current_post_id], $exclude_ids)));

    $query = new \WP_Query([
      'post_type' => $post_type,
      'post_status' => 'publish',
      'posts_per_page' => $limit,
      'orderby' => 'date',
      'order' => 'DESC',
      'post__not_in' => $not_in,
      'tax_query' => [
        [
          'taxonomy' => $taxonomy,
          'field' => 'term_id',
          'terms' => [$term_id],
        ],
      ],
      'fields' => 'ids',
      'no_found_rows' => true,
      'ignore_sticky_posts' => true,
    ]);

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

if (! function_exists('eai_related_posts_collect_from_term')) {
  /**
   * @param array<int, int> $found_ids
   * @return array<int, int>
   */
  function eai_related_posts_collect_from_term(
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

    $batch = eai_related_posts_query_by_term(
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
        $found_ids = eai_related_posts_collect_from_term(
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

if (! function_exists('eai_related_posts_resolve_taxonomy_slugs')) {
  /**
   * @param array<int, string> $taxonomy_slugs
   * @return array<int, string>
   */
  function eai_related_posts_resolve_taxonomy_slugs(int $post_id, array $taxonomy_slugs): array
  {
    $post_type = get_post_type($post_id);
    if (! is_string($post_type) || $post_type === '') {
      return [];
    }

    if (empty($taxonomy_slugs)) {
      return array_keys(eai_get_taxonomy_options_for_post_type($post_type));
    }

    $valid = array_keys(eai_get_taxonomy_options_for_post_type($post_type));
    $ordered = [];

    foreach ($taxonomy_slugs as $slug) {
      $slug = sanitize_key((string) $slug);
      if ($slug === '' || ! in_array($slug, $valid, true)) {
        continue;
      }
      if (! in_array($slug, $ordered, true)) {
        $ordered[] = $slug;
      }
    }

    return $ordered;
  }
}

if (! function_exists('eai_related_posts_resolve')) {
  /**
   * @param array<int, string> $taxonomy_slugs
   * @return array<int, int>
   */
  function eai_related_posts_resolve(int $post_id, int $limit, array $taxonomy_slugs = []): array
  {
    if ($post_id <= 0) {
      return [];
    }

    $post = get_post($post_id);
    if (! ($post instanceof \WP_Post) || $post->post_status !== 'publish') {
      return [];
    }

    $limit = min(3, max(1, $limit));
    $post_type = $post->post_type;
    $taxonomies = eai_related_posts_resolve_taxonomy_slugs($post_id, $taxonomy_slugs);
    $found_ids = [];

    foreach ($taxonomies as $taxonomy) {
      if (count($found_ids) >= $limit) {
        break;
      }

      $terms = wp_get_post_terms($post_id, $taxonomy);
      if (is_wp_error($terms) || empty($terms)) {
        continue;
      }

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

      if (empty($filtered)) {
        continue;
      }

      $sorted = eai_related_posts_sort_terms($filtered);

      foreach ($sorted as $term) {
        if (count($found_ids) >= $limit) {
          break 2;
        }

        $found_ids = eai_related_posts_collect_from_term(
          $term,
          $taxonomy,
          $post_type,
          $post_id,
          $found_ids,
          $limit
        );
      }
    }

    return array_slice($found_ids, 0, $limit);
  }
}

if (! function_exists('eai_rc_map_related_post_links')) {
  /**
   * @param array<int, int> $post_ids
   * @return array<int, array{label: string, link: array<string, mixed>}>
   */
  function eai_rc_map_related_post_links(array $post_ids): array
  {
    return eai_rc_map_footer_link_items($post_ids);
  }
}
