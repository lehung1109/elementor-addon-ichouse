<?php
if (! defined('ABSPATH')) {
  exit;
}

if (! function_exists('eai_toc_should_skip_filter')) {
  function eai_toc_should_skip_filter(): bool
  {
    if (doing_filter('get_the_excerpt')) {
      return true;
    }

    if (is_feed() || is_admin() || wp_doing_ajax()) {
      return true;
    }

    return false;
  }
}

if (! function_exists('eai_toc_filter_the_content')) {
  function eai_toc_filter_the_content(string $content): string
  {
    if ($content === '' || eai_toc_should_skip_filter()) {
      return $content;
    }

    if (! is_singular() || ! is_main_query()) {
      return $content;
    }

    $post = get_post();
    if (! ($post instanceof \WP_Post)) {
      return $content;
    }

    if (post_password_required($post) || ! eai_toc_is_enabled_for_post($post)) {
      return $content;
    }

    $settings = eai_toc_get_settings();
    $content = eai_toc_add_heading_ids($content);
    $parsed = eai_toc_parse_headings($content);

    var_dump('go into toc here');

    if (count($parsed) < $settings['min_headings']) {
      return $content;
    }

    $props = eai_toc_get_rc_props($parsed, $settings);
    if ($props['items'] === []) {
      return $content;
    }

    $result = eai_rc_render_html('TableOfContentsWrapper', $props);
    if (is_wp_error($result) || empty($result['html'])) {
      return $content;
    }

    $content = eai_toc_insert_before_first_heading($content, $result['html'], $parsed);
    eai_enqueue_frontend_assets();

    return $content;
  }
}

add_action('init', 'eai_register_frontend_assets');
add_filter('the_content', 'eai_toc_filter_the_content', 99);
