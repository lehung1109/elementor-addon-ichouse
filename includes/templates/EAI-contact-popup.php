<?php
if (! defined('ABSPATH')) {
  exit;
}

$args = isset($args) && is_array($args) ? $args : [];
$html = $args['html'] ?? '';
$error = $args['error'] ?? null;
$form_html = (string) ($args['form_html'] ?? '');

if ($error instanceof WP_Error) {
  eai_rc_render_error_message($error);
  return;
}

echo $html;

if ($form_html !== '') {
  echo '<div id="eai-contact-popup-cf7-source" hidden>' . $form_html . '</div>';
}
