<?php
if (! defined('ABSPATH')) {
  exit;
}

/**
 * Allow SVG (and SVGZ) uploads for users who can upload media.
 *
 * WordPress blocks SVG by default; upload_mimes alone is not enough —
 * wp_check_filetype_and_ext often returns empty ext/type for SVG.
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

add_filter('upload_mimes', 'eai_allow_svg_upload_mimes');
add_filter('wp_check_filetype_and_ext', 'eai_fix_svg_filetype_and_ext', 10, 4);
