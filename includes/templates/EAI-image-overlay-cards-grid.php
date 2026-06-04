<?php
if (! defined('ABSPATH')) {
  exit;
}

$args = isset($args) && is_array($args) ? $args : [];
$html = $args['html'] ?? '';
$error = $args['error'] ?? null;

if (! empty($args['empty'])) {
  echo '<div class="eai-image-overlay-cards-grid-empty">' . esc_html__(
    'Chưa có thẻ hợp lệ. Thêm ít nhất một mục có ảnh và tiêu đề trong Elementor.',
    'eai'
  ) . '</div>';
  return;
}

if ($error instanceof WP_Error) {
  eai_rc_render_error_message($error);
  return;
}

echo $html;
