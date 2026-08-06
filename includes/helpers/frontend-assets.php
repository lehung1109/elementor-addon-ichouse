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
    $path = WP_PLUGIN_DIR . '/rc-files/version.json';

    if (! is_readable($path)) {
      return;
    }

    $json = file_get_contents($path);
    if ($json === false) {
      return;
    }

    $data = json_decode($json, true);
    if (! is_array($data)) {
      return;
    }

    $version = isset($data['version']) && is_string($data['version']) ? $data['version'] : '1';
    $css_file = isset($data['cssFile']) && is_string($data['cssFile']) ? $data['cssFile'] : 'react-loader.css';

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
