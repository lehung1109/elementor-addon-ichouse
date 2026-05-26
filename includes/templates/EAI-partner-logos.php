<?php
if (! defined('ABSPATH')) {
  exit;
}

$args = isset($args) && is_array($args) ? $args : [];
$html = $args['html'] ?? '';
$error = $args['error'] ?? null;

if (! empty($args['empty'])) {
  echo '<div class="eai-partner-logos-empty">' . esc_html__('Chưa có logo đối tác. Thêm ít nhất một logo có ảnh trong Elementor.', 'eai') . '</div>';
  return;
}

if ($error instanceof WP_Error) {
  eai_rc_render_error_message($error);
  return;
}

echo $html;
