<?php
if (! defined('ABSPATH')) {
  exit;
}

if (! function_exists('eai_contact_popup_get_cf7_options')) {
  /**
   * @return array<string, string>
   */
  function eai_contact_popup_get_cf7_options(): array
  {
    $options = [
      '' => esc_html__('— Chọn form —', 'eai'),
    ];

    if (! post_type_exists('wpcf7_contact_form')) {
      return $options;
    }

    $forms = get_posts([
      'post_type' => 'wpcf7_contact_form',
      'post_status' => 'publish',
      'posts_per_page' => -1,
      'orderby' => 'title',
      'order' => 'ASC',
    ]);

    foreach ($forms as $form) {
      if (! ($form instanceof WP_Post)) {
        continue;
      }
      $options[(string) $form->ID] = $form->post_title;
    }

    return $options;
  }
}

if (! function_exists('eai_contact_popup_render_form_html')) {
  function eai_contact_popup_render_form_html(int $form_id): string
  {
    if ($form_id <= 0) {
      return '';
    }

    if (! shortcode_exists('contact-form-7')) {
      return '';
    }

    return (string) do_shortcode(
      sprintf('[contact-form-7 id="%d"]', $form_id)
    );
  }
}

if (! function_exists('eai_contact_popup_get_rc_props')) {
  /**
   * @param array<string, mixed> $settings
   * @return array<string, mixed>
   */
  function eai_contact_popup_get_rc_props(array $settings): array
  {
    $class_name = trim((string) ($settings['class_name'] ?? ''));
    $props = [];

    if ($class_name !== '') {
      $props['className'] = $class_name;
    }

    return $props;
  }
}
