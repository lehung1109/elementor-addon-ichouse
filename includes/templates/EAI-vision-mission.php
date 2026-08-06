<?php
if (! defined('ABSPATH')) {
  exit;
}

$args = isset($args) && is_array($args) ? $args : [];
$html = $args['html'] ?? '';
$error = $args['error'] ?? null;

if ($error instanceof WP_Error) {
  eai_rc_render_error_message($error);
  return;
}

if ($html === '') {
  echo '<div class="eai-vision-mission-empty">' . esc_html__('Chưa có nội dung. Thêm cột Tầm nhìn / Sứ mệnh trong Elementor.', 'eai') . '</div>';
  return;
}

echo $html;
