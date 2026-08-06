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
  echo '<div class="eai-director-profile-empty">' . esc_html__('Chưa có nội dung. Thêm subtitle, mô tả hoặc items trong Elementor.', 'eai') . '</div>';
  return;
}

echo $html;
