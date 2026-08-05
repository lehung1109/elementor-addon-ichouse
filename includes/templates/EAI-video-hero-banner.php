<?php
if (! defined('ABSPATH')) {
  exit;
}

$args = isset($args) && is_array($args) ? $args : [];
$html = $args['html'] ?? '';
$error = $args['error'] ?? null;

if (! empty($args['empty'])) {
  echo '<div class="eai-video-hero-banner-empty">' . esc_html__(
    'Chưa có URL video hoặc ảnh poster.',
    'eai'
  ) . '</div>';
  return;
}

if ($error instanceof WP_Error) {
  eai_rc_render_error_message($error);
  return;
}

echo $html;
