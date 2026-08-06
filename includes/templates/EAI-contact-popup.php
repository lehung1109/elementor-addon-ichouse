<?php
if (! defined('ABSPATH')) {
  exit;
}

$args = isset($args) && is_array($args) ? $args : [];
$html = $args['html'] ?? '';
$error = $args['error'] ?? null;
$form_html = (string) ($args['form_html'] ?? '');
$form_source_id = (string) ($args['form_source_id'] ?? '');

if (! empty($args['empty'])) {
  echo '<div class="eai-contact-popup-empty">' . esc_html__(
    'Chưa nhập Popup key.',
    'eai'
  ) . '</div>';
  return;
}

if ($error instanceof WP_Error) {
  eai_rc_render_error_message($error);
  return;
}

echo $html;

if ($form_html !== '' && $form_source_id !== '') {
  echo '<div id="' . esc_attr($form_source_id) . '" hidden>' . $form_html . '</div>';
}
