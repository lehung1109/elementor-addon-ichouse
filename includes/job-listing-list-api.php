<?php
if (! defined('ABSPATH')) {
  exit;
}

function eai_register_job_listing_list_rest_routes(): void
{
  register_rest_route('eai/v1', '/job-listing-list', [
    'methods' => \WP_REST_Server::CREATABLE,
    'callback' => 'eai_rest_job_listing_list',
    'permission_callback' => '__return_true',
  ]);
}
add_action('rest_api_init', 'eai_register_job_listing_list_rest_routes');

function eai_job_listing_list_config_from_request(\WP_REST_Request $request): array
{
  return eai_job_listing_list_config_from_settings([
    'post_type' => $request->get_param('post_type'),
    'page_size' => 3,
    'orderby' => $request->get_param('orderby'),
    'order' => $request->get_param('order'),
    'image_size' => $request->get_param('image_size'),
    'taxonomy' => $request->get_param('taxonomy'),
    'include_terms' => $request->get_param('include_terms'),
    'page_query_param' => 'jobs_page',
  ]);
}

function eai_rest_job_listing_list(\WP_REST_Request $request)
{
  $config = eai_job_listing_list_config_from_request($request);
  $post_type = get_post_type_object($config['post_type']);
  if (! $post_type || empty($post_type->public)) {
    return new \WP_Error(
      'eai_invalid_job_listing_config',
      __('Invalid public post type.', 'eai'),
      ['status' => 400]
    );
  }

  $taxonomy = (string) ($config['taxonomy'] ?? '');
  $taxonomy_object = $taxonomy !== '' && taxonomy_exists($taxonomy) ? get_taxonomy($taxonomy) : false;
  if ($taxonomy !== '' && ($taxonomy_object === false || empty($taxonomy_object->public) || ! is_object_in_taxonomy($config['post_type'], $taxonomy))) {
    return new \WP_Error(
      'eai_invalid_job_listing_taxonomy',
      __('Invalid taxonomy for the selected post type.', 'eai'),
      ['status' => 400]
    );
  }

  $body = $request->get_json_params();
  $body = is_array($body) ? $body : [];
  $page = max(1, (int) ($body['page'] ?? 1));
  $page_size = max(1, min(24, (int) ($body['pageSize'] ?? 3)));

  return new \WP_REST_Response(
    eai_job_listing_list_query($config, $page, $page_size),
    200
  );
}
