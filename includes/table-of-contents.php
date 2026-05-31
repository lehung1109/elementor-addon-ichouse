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

    $inject_html = '';

    $related_props = eai_related_posts_get_rc_props((int) $post->ID, [
      'title' => $settings['related_posts_title'],
      'posts_count' => $settings['related_posts_count'],
      'taxonomies' => $settings['related_posts_taxonomies'],
    ]);

    if (! empty($related_props['links'])) {
      $related_result = eai_rc_render_html('RelatedPostList', $related_props);
      if (! is_wp_error($related_result) && ! empty($related_result['html'])) {
        $inject_html .= $related_result['html'];
      }
    }

    if (count($parsed) >= $settings['min_headings']) {
      $props = eai_toc_get_rc_props($parsed, $settings);
      if ($props['items'] !== []) {
        $result = eai_rc_render_html('TableOfContentsWrapper', $props);
        if (! is_wp_error($result) && ! empty($result['html'])) {
          $inject_html .= $result['html'];
        }
      }
    }

    if ($inject_html === '') {
      return $content;
    }

    if ($parsed !== []) {
      $content = eai_toc_insert_before_first_heading($content, $inject_html, $parsed);
    } else {
      $content = $inject_html . $content;
    }

    eai_enqueue_frontend_assets();

    return $content;
  }
}

add_action('init', 'eai_register_frontend_assets');
add_filter('the_content', 'eai_toc_filter_the_content', 99999999999);
