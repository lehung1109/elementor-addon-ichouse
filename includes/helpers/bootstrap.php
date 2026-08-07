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
require_once __DIR__ . '/svg-upload.php';
require_once __DIR__ . '/elementor-controls.php';
require_once __DIR__ . '/process-icons.php';
require_once __DIR__ . '/footer.php';
require_once __DIR__ . '/construction-header.php';
require_once __DIR__ . '/feature-cards.php';
require_once __DIR__ . '/project-showcase.php';
require_once __DIR__ . '/related-posts.php';
require_once __DIR__ . '/product-gallery.php';
require_once __DIR__ . '/page-title-bar.php';
require_once __DIR__ . '/project-meta-bar.php';
require_once __DIR__ . '/inline-list.php';
require_once __DIR__ . '/entry-post-date.php';
require_once __DIR__ . '/breadcrumb.php';
require_once __DIR__ . '/number-icon-grid.php';
require_once __DIR__ . '/image-overlay-cards-grid.php';
require_once __DIR__ . '/customer-testimonials.php';
require_once __DIR__ . '/about-intro.php';
require_once __DIR__ . '/director-intro.php';
require_once __DIR__ . '/director-profile.php';
require_once __DIR__ . '/vision-mission.php';
require_once __DIR__ . '/key-personnel.php';
require_once __DIR__ . '/development-partners.php';
require_once __DIR__ . '/outstanding-advantages.php';
require_once __DIR__ . '/service-offerings.php';
require_once __DIR__ . '/youtube-video-list.php';
require_once __DIR__ . '/featured-projects.php';
require_once __DIR__ . '/news-events.php';
require_once __DIR__ . '/fields-of-activity.php';
require_once __DIR__ . '/construction-highlights.php';
require_once __DIR__ . '/construction-footer.php';
require_once __DIR__ . '/contact-popup.php';
require_once __DIR__ . '/contact-cta.php';
require_once __DIR__ . '/floating-contact.php';
require_once __DIR__ . '/frontend-assets.php';
require_once __DIR__ . '/table-of-contents.php';
