<?php
if (! defined('ABSPATH')) {
  exit;
}

function eai_register_news_list_rest_routes(): void
{
  register_rest_route('eai/v1', '/news-list', [
    'methods' => \WP_REST_Server::CREATABLE,
    'callback' => 'eai_rest_news_list',
    'permission_callback' => '__return_true',
  ]);
}
add_action('rest_api_init', 'eai_register_news_list_rest_routes');

function eai_rest_news_list(\WP_REST_Request $request)
{
  $config = eai_news_list_config_from_request($request);
  if (! post_type_exists($config['post_type'])) {
    return new \WP_Error('eai_invalid_news_list_config', __('Invalid post type.', 'eai'), ['status' => 400]);
  }

  $body = $request->get_json_params();
  $body = is_array($body) ? $body : [];
  $page = max(1, (int) ($body['page'] ?? 1));
  $page_size = max(1, min(24, (int) ($body['pageSize'] ?? $config['page_size'])));

  return new \WP_REST_Response(
    eai_news_list_query($config, $page, $page_size),
    200
  );
}
