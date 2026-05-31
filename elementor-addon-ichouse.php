<?php

/**
 * Plugin Name: Elementor Addon ICHouse
 * Plugin URI:       https://ichouse.vn/
 * Description: ICHouse widgets for Elementor.
 * Version:     1.0.0
 * Author:      ICHouse
 * Author URI:  https://ichouse.vn/
 * Text Domain: elementor-addon-ichouse
 * Requires Plugins: elementor
 */

if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}

define('EAI_FILE', __FILE__);
define('EAI_PATH', plugin_dir_path(__FILE__));
define('EAI_URL', plugin_dir_url(__FILE__));

function elementor_addon_ichouse()
{
  require_once EAI_PATH . 'includes/helpers/bootstrap.php';
  require_once EAI_PATH . 'includes/project-showcase-api.php';
  require_once EAI_PATH . 'includes/rc-render.php';
  require_once EAI_PATH . 'includes/admin/toc-settings.php';
  require_once EAI_PATH . 'includes/table-of-contents.php';
  require_once EAI_PATH . 'includes/plugin.php';

  // Run the plugin
  \EAI\Plugin::instance();
}

add_action('plugins_loaded', 'elementor_addon_ichouse');
