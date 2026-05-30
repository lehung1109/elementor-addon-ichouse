<?php
if (! defined('ABSPATH')) {
  exit;
}

if (! function_exists('eai_get_widget_category_slug')) {
  function eai_get_widget_category_slug(): string
  {
    return 'eai-ichouse';
  }
}

if (! function_exists('eai_get_widget_categories')) {
  /**
   * Elementor panel category for all ICHouse api-rc widgets.
   *
   * @return array<int, string>
   */
  function eai_get_widget_categories(): array
  {
    return [eai_get_widget_category_slug()];
  }
}

if (! function_exists('eai_render_template')) {
  function eai_render_template(string $template, array $args = []): void
  {
    $theme_template = locate_template(
      [
        'elementor-addon-ichouse/' . $template,
      ],
      false,
      false
    );

    $plugin_template = \EAI_PATH . 'includes/' . ltrim($template, '/');
    $path = $theme_template ?: $plugin_template;

    if (! file_exists($path)) {
      return;
    }

    load_template($path, false, $args);
  }
}

require_once __DIR__ . '/menu.php';
require_once __DIR__ . '/media.php';
require_once __DIR__ . '/elementor-controls.php';
require_once __DIR__ . '/process-icons.php';
require_once __DIR__ . '/footer.php';
require_once __DIR__ . '/feature-cards.php';
require_once __DIR__ . '/project-showcase.php';
require_once __DIR__ . '/related-posts.php';
require_once __DIR__ . '/product-gallery.php';
require_once __DIR__ . '/page-title-bar.php';
require_once __DIR__ . '/project-meta-bar.php';
