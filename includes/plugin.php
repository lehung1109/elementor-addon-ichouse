<?php

namespace EAI;

if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}

/**
 * Plugin class.
 *
 * The main class that initiates and runs the addon.
 *
 * @since 1.0.0
 */
final class Plugin
{

  /**
   * Addon Version
   *
   * @since 1.0.0
   * @var string The addon version.
   */
  const VERSION = '1.0.0';

  /**
   * Minimum Elementor Version
   *
   * @since 1.0.0
   * @var string Minimum Elementor version required to run the addon.
   */
  const MINIMUM_ELEMENTOR_VERSION = '3.20.0';

  /**
   * Minimum PHP Version
   *
   * @since 1.0.0
   * @var string Minimum PHP version required to run the addon.
   */
  const MINIMUM_PHP_VERSION = '7.4';

  /**
   * Instance
   *
   * @since 1.0.0
   * @access private
   * @static
   * @var \Elementor_Ihouse_Addon\Plugin The single instance of the class.
   */
  private static $_instance = null;

  /**
   * Instance
   *
   * Ensures only one instance of the class is loaded or can be loaded.
   *
   * @since 1.0.0
   * @access public
   * @static
   * @return \Elementor_Ihouse_Addon\Plugin An instance of the class.
   */
  public static function instance()
  {

    if (is_null(self::$_instance)) {
      self::$_instance = new self();
    }
    return self::$_instance;
  }

  /**
   * Constructor
   *
   * Perform some compatibility checks to make sure basic requirements are meet.
   * If all compatibility checks pass, initialize the functionality.
   *
   * @since 1.0.0
   * @access public
   */
  public function __construct()
  {

    if ($this->is_compatible()) {
      add_action('elementor/init', [$this, 'init']);
    }
  }

  /**
   * Compatibility Checks
   *
   * Checks whether the site meets the addon requirement.
   *
   * @since 1.0.0
   * @access public
   */
  public function is_compatible(): bool
  {

    // Check if Elementor installed and activated
    if (! did_action('elementor/loaded')) {
      add_action('admin_notices', [$this, 'admin_notice_missing_main_plugin']);
      return false;
    }

    // Check for required Elementor version
    if (! version_compare(ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=')) {
      add_action('admin_notices', [$this, 'admin_notice_minimum_elementor_version']);
      return false;
    }

    // Check for required PHP version
    if (version_compare(PHP_VERSION, self::MINIMUM_PHP_VERSION, '<')) {
      add_action('admin_notices', [$this, 'admin_notice_minimum_php_version']);
      return false;
    }

    return true;
  }

  /**
   * Admin notice
   *
   * Warning when the site doesn't have Elementor installed or activated.
   *
   * @since 1.0.0
   * @access public
   */
  public function admin_notice_missing_main_plugin(): void
  {

    if (isset($_GET['activate'])) unset($_GET['activate']);

    $message = sprintf(
      /* translators: 1: Plugin name 2: Elementor */
      esc_html__('"%1$s" requires "%2$s" to be installed and activated.', 'eai'),
      '<strong>' . esc_html__('Elementor ICHouse Addon', 'eai') . '</strong>',
      '<strong>' . esc_html__('Elementor', 'eai') . '</strong>'
    );

    printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
  }

  /**
   * Admin notice
   *
   * Warning when the site doesn't have a minimum required Elementor version.
   *
   * @since 1.0.0
   * @access public
   */
  public function admin_notice_minimum_elementor_version(): void
  {

    if (isset($_GET['activate'])) unset($_GET['activate']);

    $message = sprintf(
      /* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
      esc_html__('"%1$s" requires "%2$s" version %3$s or greater.', 'eai'),
      '<strong>' . esc_html__('Elementor ICHouse Addon', 'eai') . '</strong>',
      '<strong>' . esc_html__('Elementor', 'eai') . '</strong>',
      self::MINIMUM_ELEMENTOR_VERSION
    );

    printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
  }

  /**
   * Admin notice
   *
   * Warning when the site doesn't have a minimum required PHP version.
   *
   * @since 1.0.0
   * @access public
   */
  public function admin_notice_minimum_php_version(): void
  {

    if (isset($_GET['activate'])) unset($_GET['activate']);

    $message = sprintf(
      /* translators: 1: Plugin name 2: PHP 3: Required PHP version */
      esc_html__('"%1$s" requires "%2$s" version %3$s or greater.', 'eai'),
      '<strong>' . esc_html__('Elementor ICHouse Addon', 'eai') . '</strong>',
      '<strong>' . esc_html__('PHP', 'eai') . '</strong>',
      self::MINIMUM_PHP_VERSION
    );

    printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
  }

  /**
   * Initialize
   *
   * Load the addons functionality only after Elementor is initialized.
   *
   * Fired by `elementor/init` action hook.
   *
   * @since 1.0.0
   * @access public
   */
  public function init(): void
  {

    add_action('elementor/widgets/register', [$this, 'register_widgets']);
    add_action('wp_enqueue_scripts', [$this, 'register_frontend_assets']);
  }

  /**
   * Register Widgets
   *
   * Load widgets files and register new Elementor widgets.
   *
   * Fired by `elementor/widgets/register` action hook.
   *
   * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager.
   */
  public function register_widgets($widgets_manager): void
  {

    require_once __DIR__ . '/widgets/EAI-header-top.php';
    require_once __DIR__ . '/widgets/EAI-header-inner.php';

    $widgets_manager->register(new \EAI_Header_Top_Widget());
    $widgets_manager->register(new \EAI_Header_Inner_Widget());
  }

  public function register_frontend_assets()
  {
    $jsonString = file_get_contents(WP_PLUGIN_DIR  . '/rc-files/version.json');

    $data = json_decode($jsonString, true);

    $version = $data['version'];

    wp_register_script(
      'eai-frontend',
      WP_PLUGIN_URL  . '/rc-files/react-loader.js',
      [],
      $version,
      true
    );

    wp_enqueue_script('eai-frontend');
  }
}
