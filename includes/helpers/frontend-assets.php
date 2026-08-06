<?php
if (! defined('ABSPATH')) {
  exit;
}

if (! function_exists('eai_register_frontend_assets')) {
  function eai_register_frontend_assets(): void
  {
    static $registered = false;

    if ($registered) {
      return;
    }

    $registered = true;

    $manifest = eai_rc_get_version_manifest();
    if ($manifest === null) {
      return;
    }

    $version = eai_rc_get_bundle_version() ?? '1';
    $css_file = isset($manifest['cssFile']) && is_string($manifest['cssFile'])
      ? $manifest['cssFile']
      : 'react-loader.css';

    wp_register_style(
      'eai-frontend',
      WP_PLUGIN_URL . '/rc-files/' . $css_file,
      [],
      $version
    );

    wp_register_script_module(
      'eai-frontend',
      WP_PLUGIN_URL . '/rc-files/react-loader.js',
      [],
      $version
    );
  }
}

if (! function_exists('eai_enqueue_frontend_assets')) {
  function eai_enqueue_frontend_assets(): void
  {
    eai_register_frontend_assets();

    wp_enqueue_style('eai-frontend');
    wp_enqueue_script_module('eai-frontend');
  }
}

if (! function_exists('eai_filter_porto_generated_styles_src')) {
  /**
   * Cache-bust Porto uploads/porto_styles/*.css with api-rc bundle version.
   *
   * @param string $src    Stylesheet URL.
   * @param string $handle Style handle.
   */
  function eai_filter_porto_generated_styles_src(string $src, string $handle): string
  {
    if (strpos($src, '/porto_styles/') === false) {
      return $src;
    }

    $version = eai_rc_get_bundle_version();
    if ($version === null) {
      return $src;
    }

    $src = remove_query_arg('ver', $src);

    return add_query_arg('ver', $version, $src);
  }

  add_filter('style_loader_src', 'eai_filter_porto_generated_styles_src', 10, 2);
}

if (! function_exists('eai_enqueue_elementor_editor_assets')) {
  /**
   * Styles for Elementor preview iframe only (disable scroll slide-in).
   */
  function eai_enqueue_elementor_editor_assets(): void
  {
    $relative = 'assets/css/eai-elementor-editor.css';
    $path = EAI_PATH . $relative;

    if (! is_readable($path)) {
      return;
    }

    wp_enqueue_style(
      'eai-elementor-editor',
      EAI_URL . $relative,
      ['eai-frontend'],
      (string) filemtime($path)
    );
  }
}
