<?php
if (! defined('ABSPATH')) {
  exit;
}

if (! function_exists('eai_project_showcase_config_from_settings')) {
  /**
   * @param array<string, mixed> $settings
   * @return array<int, string>
   */
  function eai_project_showcase_post_types_from_settings(array $settings): array
  {
    $post_types = $settings['post_types'] ?? [];
    if (! is_array($post_types)) {
      $post_types = [];
    }

    $normalized = array_values(array_filter(array_map(
      static fn($value): string => sanitize_key((string) $value),
      $post_types
    )));

    // Backward compatibility: old widget stored a single post_type.
    if (empty($normalized)) {
      $legacy = sanitize_key((string) ($settings['post_type'] ?? ''));
      if ($legacy !== '') {
        $normalized = [$legacy];
      }
    }

    return array_values(array_unique($normalized));
  }

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
      $include_terms = $row['include_terms'] ?? [];
      if (! is_array($include_terms)) {
        $include_terms = [];
      }

      if ($key === '' || $taxonomy === '') {
        continue;
      }

      $prefix = $taxonomy . ':';
      $include_terms_slugs = [];
      foreach ($include_terms as $value) {
        $value = (string) $value;
        if ($value === '' || strpos($value, $prefix) !== 0) {
          continue;
        }
        $slug = sanitize_title(substr($value, strlen($prefix)));
        if ($slug !== '') {
          $include_terms_slugs[] = $slug;
        }
      }
      $include_terms_slugs = array_values(array_unique($include_terms_slugs));

      $taxonomies[] = [
        'key' => $key,
        'label' => $label !== '' ? $label : $key,
        'taxonomy' => $taxonomy,
        'include_terms_slugs' => $include_terms_slugs,
      ];
    }

    return [
      'post_types' => eai_project_showcase_post_types_from_settings($settings),
      'taxonomies' => $taxonomies,
      'posts_per_page' => (int) ($settings['posts_per_page'] ?? -1),
      'image_size' => sanitize_key((string) ($settings['image_size'] ?? 'large')),
    ];
  }
}

if (! function_exists('eai_project_showcase_filters_from_url')) {
  /**
   * @param array<string, mixed> $config
   * @return array<string, string>
   */
  function eai_project_showcase_filters_from_url(array $config): array
  {
    $out = [];

    if (empty($_GET) || ! is_array($_GET)) {
      return $out;
    }

    $taxonomies = is_array($config['taxonomies'] ?? null) ? $config['taxonomies'] : [];
    foreach ($taxonomies as $row) {
      if (! is_array($row)) {
        continue;
      }

      $key = sanitize_key((string) ($row['key'] ?? ''));
      if ($key === '' || ! isset($_GET[$key])) {
        continue;
      }

      $value = sanitize_title((string) wp_unslash($_GET[$key]));
      if ($value === '') {
        continue;
      }

      $out[$key] = $value;
    }

    return $out;
  }
}

if (! function_exists('eai_project_showcase_resolve_filters')) {
  /**
   * @param array<string, mixed> $settings
   * @param array<string, mixed> $config
   * @return array<string, string>
   */
  function eai_project_showcase_resolve_filters(array $settings, array $config): array
  {
    return eai_project_showcase_filters_from_url($config);
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
    $post_types = $config['post_types'] ?? [];
    if (! is_array($post_types)) {
      $post_types = [];
    }

    $query = [
      'image_size' => $config['image_size'] ?? 'large',
    ];

    foreach (array_values($post_types) as $idx => $post_type) {
      $post_type = sanitize_key((string) $post_type);
      if ($post_type === '') {
        continue;
      }
      $query["post_types[$idx]"] = $post_type;
    }

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
  function eai_get_taxonomy_terms_as_filter_options(string $taxonomy, array $slug_whitelist = []): array
  {
    if ($taxonomy === '') {
      return [];
    }

    $args = [
      'taxonomy' => $taxonomy,
      'hide_empty' => false,
    ];
    if (! empty($slug_whitelist)) {
      $args['slug'] = array_values(array_filter(array_map(
        static fn($slug): string => sanitize_title((string) $slug),
        $slug_whitelist
      )));
    }

    $terms = get_terms($args);

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
      $include_terms_slugs = is_array($row['include_terms_slugs'] ?? null) ? $row['include_terms_slugs'] : [];

      if ($key === '' || $taxonomy === '') {
        continue;
      }

      $out[$key] = eai_get_taxonomy_terms_as_filter_options($taxonomy, $include_terms_slugs);
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
      'url' => eai_rc_map_link(['url' => get_permalink($post)]),
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
      'post_type' => $config['post_types'],
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
    if (empty($config['post_types'])) {
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

