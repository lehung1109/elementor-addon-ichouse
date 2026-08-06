<?php
if (! defined('ABSPATH')) {
  exit;
}

if (! function_exists('eai_normalize_contact_popup_key')) {
  /**
   * Trim, lowercase, keep only [a-z0-9-].
   */
  function eai_normalize_contact_popup_key(string $value): string
  {
    $key = strtolower(trim($value));
    $key = preg_replace('/[^a-z0-9-]+/', '-', $key) ?? '';
    $key = preg_replace('/-+/', '-', $key) ?? '';
    return trim($key, '-');
  }
}

if (! function_exists('eai_contact_popup_cf7_source_id')) {
  function eai_contact_popup_cf7_source_id(string $popup_key): string
  {
    $key = eai_normalize_contact_popup_key($popup_key);
    if ($key === '') {
      return '';
    }

    return 'eai-contact-popup-cf7-source-' . $key;
  }
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
    $popup_key = eai_normalize_contact_popup_key((string) ($settings['popup_key'] ?? ''));
    $props = [
      'popupKey' => $popup_key,
    ];

    if ($class_name !== '') {
      $props['className'] = $class_name;
    }

    return $props;
  }
}
