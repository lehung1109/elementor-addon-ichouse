<?php

if (! defined('ABSPATH')) {
  exit;
}

if (! function_exists('eai_register_project_showcase_rest_routes')) {
  function eai_register_project_showcase_rest_routes(): void
  {
    register_rest_route(
      'eai/v1',
      '/projects/filter',
      [
        'methods' => \WP_REST_Server::CREATABLE,
        'callback' => 'eai_rest_project_showcase_filter',
        'permission_callback' => '__return_true',
      ]
    );
  }
}

add_action('rest_api_init', 'eai_register_project_showcase_rest_routes');

if (! function_exists('eai_rest_project_showcase_filter')) {
  /**
   * @return \WP_REST_Response|\WP_Error
   */
  function eai_rest_project_showcase_filter(\WP_REST_Request $request)
  {
    $config = eai_project_showcase_config_from_request($request);

    if (empty($config['post_types'])) {
      return new \WP_Error(
        'eai_missing_post_types',
        __('Missing post_types query parameter.', 'eai'),
        ['status' => 400]
      );
    }

    $body = $request->get_json_params();
    if (! is_array($body)) {
      $body = [];
    }

    $filters = eai_project_showcase_normalize_filters($body);
    $items = eai_project_showcase_query_and_map($config, $filters);

    return new \WP_REST_Response(['items' => $items], 200);
  }
}

if (! function_exists('eai_project_showcase_config_from_request')) {
  /**
   * @return array<string, mixed>
   */
  function eai_project_showcase_config_from_request(\WP_REST_Request $request): array
  {
    $post_types = $request->get_param('post_types');
    if (! is_array($post_types)) {
      $post_types = [];
    }
    $post_types = array_values(array_filter(array_map(
      static fn($value): string => sanitize_key((string) $value),
      $post_types
    )));

    // Backward compatibility for older filterEndpoint URLs.
    if (empty($post_types)) {
      $legacy = sanitize_key((string) $request->get_param('post_type'));
      if ($legacy !== '') {
        $post_types = [$legacy];
      }
    }

    $taxonomies = $request->get_param('taxonomies');
    if (! is_array($taxonomies)) {
      $taxonomies = [];
    }

    $normalized_taxonomies = [];
    foreach ($taxonomies as $row) {
      if (! is_array($row)) {
        continue;
      }
      $taxonomy = sanitize_key((string) ($row['taxonomy'] ?? ''));
      if ($taxonomy === '') {
        continue;
      }
      $normalized_taxonomies[] = [
        'key' => $taxonomy,
        'taxonomy' => $taxonomy,
      ];
    }

    return [
      'post_types' => $post_types,
      'taxonomies' => $normalized_taxonomies,
      'posts_per_page' => (int) $request->get_param('posts_per_page'),
      'image_size' => sanitize_key((string) ($request->get_param('image_size') ?: 'large')),
    ];
  }
}
