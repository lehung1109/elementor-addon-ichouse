<?php
if (! defined('ABSPATH')) {
  exit;
}

if (! function_exists('eai_contact_popup_button_get_rc_props')) {
  /**
   * @param array<string, mixed> $settings
   * @return array<string, mixed>
   */
  function eai_contact_popup_button_get_rc_props(array $settings): array
  {
    $class_name = trim((string) ($settings['class_name'] ?? ''));
    $popup_target = eai_normalize_contact_popup_key((string) ($settings['popup_target'] ?? ''));
    $variant = trim((string) ($settings['button_variant'] ?? ''));

    $props = [
      'buttonLabel' => (string) ($settings['button_label'] ?? ''),
    ];

    if ($popup_target !== '') {
      $props['popupTarget'] = $popup_target;
    }

    if ($variant === 'navy') {
      $props['variant'] = $variant;
    }

    if ($class_name !== '') {
      $props['className'] = $class_name;
    }

    return $props;
  }
}

if (! function_exists('eai_contact_popup_button_props_are_empty')) {
  /**
   * @param array<string, mixed> $props
   */
  function eai_contact_popup_button_props_are_empty(array $props): bool
  {
    return trim((string) ($props['buttonLabel'] ?? '')) === ''
      || trim((string) ($props['popupTarget'] ?? '')) === '';
  }
}

if (! function_exists('eai_contact_popup_button_get_editor_sample_props')) {
  /**
   * @param array<string, mixed> $settings
   * @return array<string, mixed>
   */
  function eai_contact_popup_button_get_editor_sample_props(array $settings): array
  {
    $props = [
      'buttonLabel' => 'TRỞ THÀNH ĐỐI TÁC ICHOUSE!',
      'popupTarget' => 'tu-van',
    ];

    $class_name = trim((string) ($settings['class_name'] ?? ''));
    if ($class_name !== '') {
      $props['className'] = $class_name;
    }

    $variant = trim((string) ($settings['button_variant'] ?? ''));
    if ($variant === 'navy') {
      $props['variant'] = $variant;
    }

    return $props;
  }
}
