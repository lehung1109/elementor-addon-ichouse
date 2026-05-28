<?php
if (! defined('ABSPATH')) {
  exit;
}

if (! function_exists('eai_get_public_post_type_options')) {
  /**
   * @return array<string, string>
   */
  function eai_get_public_post_type_options(): array
  {
    $options = [];

    foreach (get_post_types(['public' => true], 'objects') as $post_type) {
      if ($post_type->name === 'attachment') {
        continue;
      }

      $options[$post_type->name] = $post_type->labels->singular_name;
    }

    return $options;
  }
}

if (! function_exists('eai_get_taxonomy_options_for_post_type')) {
  /**
   * @return array<string, string>
   */
  function eai_get_taxonomy_options_for_post_type(string $post_type): array
  {
    $options = [];

    foreach (get_object_taxonomies($post_type, 'objects') as $taxonomy) {
      if (empty($taxonomy->public) && empty($taxonomy->show_ui)) {
        continue;
      }

      $options[$taxonomy->name] = $taxonomy->labels->singular_name;
    }

    return $options;
  }
}

if (! function_exists('eai_get_public_taxonomy_options')) {
  /**
   * @return array<string, string>
   */
  function eai_get_public_taxonomy_options(): array
  {
    $options = [];

    foreach (get_taxonomies(['public' => true], 'objects') as $taxonomy) {
      $options[$taxonomy->name] = $taxonomy->labels->singular_name;
    }

    return $options;
  }
}

if (! function_exists('eai_get_taxonomy_terms_as_options')) {
  /**
   * @return array<string, string>
   */
  function eai_get_taxonomy_terms_as_options(string $taxonomy): array
  {
    $terms = get_terms(
      [
        'taxonomy' => $taxonomy,
        'hide_empty' => false,
      ]
    );

    if (is_wp_error($terms) || empty($terms)) {
      return [];
    }

    $options = [];

    foreach ($terms as $term) {
      $options[$term->slug] = $term->name;
    }

    return $options;
  }
}

if (! function_exists('eai_get_image_size_options')) {
  /**
   * Image size options for Elementor SELECT controls (matches Elementor media control labels).
   */
  function eai_get_image_size_options(): array
  {
    $wp_image_sizes = \Elementor\Group_Control_Image_Size::get_all_image_sizes();
    $options = [];

    foreach ($wp_image_sizes as $size_key => $size_attributes) {
      $label = ucwords(str_replace('_', ' ', $size_key));

      if (is_array($size_attributes)) {
        $label .= sprintf(' - %d x %d', $size_attributes['width'], $size_attributes['height']);
      }

      $options[$size_key] = $label;
    }

    $options[''] = esc_html_x('Full', 'Image Size Control', 'elementor');

    return $options;
  }
}
