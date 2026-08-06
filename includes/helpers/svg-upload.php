<?php
if (! defined('ABSPATH')) {
  exit;
}

/**
 * Allow SVG (and SVGZ) uploads for users who can upload media.
 *
 * WordPress blocks SVG by default; upload_mimes alone is not enough —
 * wp_check_filetype_and_ext often returns empty ext/type for SVG.
 *
 * Elementor also blocks SVG unless "Enable Unfiltered File Uploads" is on
 * (option elementor_unfiltered_files_upload). Without that, Media uploads
 * from Elementor return: "This file is not allowed for security reasons."
 */

if (! function_exists('eai_allow_svg_upload_mimes')) {
  /**
   * @param array<string, string> $mimes
   * @return array<string, string>
   */
  function eai_allow_svg_upload_mimes(array $mimes): array
  {
    if (! current_user_can('upload_files')) {
      return $mimes;
    }

    $mimes['svg'] = 'image/svg+xml';
    $mimes['svgz'] = 'image/svg+xml';

    return $mimes;
  }
}

if (! function_exists('eai_fix_svg_filetype_and_ext')) {
  /**
   * @param array{ext?: string|false, type?: string|false, proper_filename?: string|false}|array<string, mixed> $data
   * @param array<string, string>|null $mimes
   * @return array{ext?: string|false, type?: string|false, proper_filename?: string|false}|array<string, mixed>
   */
  function eai_fix_svg_filetype_and_ext($data, $file, string $filename, $mimes)
  {
    unset($file);

    if (! current_user_can('upload_files')) {
      return $data;
    }

    $filetype = wp_check_filetype($filename, $mimes);
    $ext = $filetype['ext'] ?? '';

    if ($ext !== 'svg' && $ext !== 'svgz') {
      return $data;
    }

    $data['ext'] = $ext;
    $data['type'] = 'image/svg+xml';

    return $data;
  }
}

if (! function_exists('eai_svg_sanitizer_can_run')) {
  function eai_svg_sanitizer_can_run(): bool
  {
    return class_exists('DOMDocument') && class_exists('SimpleXMLElement');
  }
}

if (! function_exists('eai_allow_elementor_unfiltered_upload')) {
  /**
   * Elementor requires unfiltered uploads for SVG; enable when user can upload
   * and PHP can run Elementor's SVG sanitizer.
   */
  function eai_allow_elementor_unfiltered_upload(bool $enabled): bool
  {
    if ($enabled) {
      return true;
    }

    if (! current_user_can('upload_files')) {
      return false;
    }

    return eai_svg_sanitizer_can_run();
  }
}

if (! function_exists('eai_ensure_elementor_unfiltered_upload_option')) {
  /**
   * Keep Elementor setting in sync so admin UI and option-based checks match.
   */
  function eai_ensure_elementor_unfiltered_upload_option(): void
  {
    if (! current_user_can('manage_options')) {
      return;
    }

    if (! eai_svg_sanitizer_can_run()) {
      return;
    }

    if ((string) get_option('elementor_unfiltered_files_upload') === '1') {
      return;
    }

    update_option('elementor_unfiltered_files_upload', '1');
  }
}

add_filter('upload_mimes', 'eai_allow_svg_upload_mimes');
add_filter('wp_check_filetype_and_ext', 'eai_fix_svg_filetype_and_ext', 10, 4);
add_filter('elementor/files/allow_unfiltered_upload', 'eai_allow_elementor_unfiltered_upload');
add_action('admin_init', 'eai_ensure_elementor_unfiltered_upload_option');
