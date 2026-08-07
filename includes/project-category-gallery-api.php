<?php
if (! defined('ABSPATH')) {
  exit;
}

function eai_register_project_category_gallery_rest_routes(): void
{
  register_rest_route('eai/v1', '/project-category-gallery', [
    'methods' => \WP_REST_Server::CREATABLE,
    'callback' => 'eai_rest_project_category_gallery',
    'permission_callback' => '__return_true',
  ]);
}
add_action('rest_api_init', 'eai_register_project_category_gallery_rest_routes');

function eai_project_category_gallery_config_from_request(\WP_REST_Request $request): array
{
  return eai_project_category_gallery_config_from_settings([
    'post_type' => $request->get_param('post_type'),
    'taxonomy' => $request->get_param('taxonomy'),
    'include_terms' => $request->get_param('include_terms'),
    'page_size' => 6,
    'orderby' => $request->get_param('orderby'),
    'order' => $request->get_param('order'),
    'image_size' => $request->get_param('image_size'),
  ]);
}

function eai_rest_project_category_gallery(\WP_REST_Request $request)
{
  $config = eai_project_category_gallery_config_from_request($request);
  if (! post_type_exists($config['post_type']) || ! taxonomy_exists($config['taxonomy'])) {
    return new \WP_Error('eai_invalid_gallery_config', __('Invalid post type or taxonomy.', 'eai'), ['status' => 400]);
  }

  $body = $request->get_json_params();
  $body = is_array($body) ? $body : [];
  $category = sanitize_title((string) ($body['category'] ?? ''));
  if ($category !== '' && ! in_array($category, $config['include_terms'], true)) {
    $category = '';
  }
  $page = max(1, (int) ($body['page'] ?? 1));
  $page_size = max(1, min(24, (int) ($body['pageSize'] ?? 6)));

  return new \WP_REST_Response(
    eai_project_category_gallery_query($config, $category, $page, $page_size),
    200
  );
}
