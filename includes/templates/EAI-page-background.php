<?php
if (! defined('ABSPATH')) {
  exit;
}

$args = isset($args) && is_array($args) ? $args : [];
$html = $args['html'] ?? '';
$error = $args['error'] ?? null;

if (! empty($args['empty'])) {
  echo '<div class="eai-page-background-empty">' . esc_html__(
    'Chưa chọn ảnh nền mobile hoặc desktop.',
    'eai'
  ) . '</div>';
  return;
}

if ($error instanceof WP_Error) {
  eai_rc_render_error_message($error);
  return;
}

echo $html;
