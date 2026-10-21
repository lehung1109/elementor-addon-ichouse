<?php
if (! defined('ABSPATH')) {
  exit;
}

$args = isset($args) && is_array($args) ? $args : [];
$html = $args['html'] ?? '';
$error = $args['error'] ?? null;
$form_key = isset($args['form_key']) ? (string) $args['form_key'] : '';
$cf7_form_id = isset($args['cf7_form_id']) ? (int) $args['cf7_form_id'] : 0;

if (! empty($args['empty'])) {
  return;
}

if ($error instanceof WP_Error) {
  eai_rc_render_error_message($error);
  return;
}

if ($cf7_form_id > 0 && $form_key !== '' && shortcode_exists('contact-form-7')) {
  $source_id = 'eai-ocean-park-cf7-source-' . $form_key;
  ?>
  <div id="<?php echo esc_attr($source_id); ?>" hidden>
    <?php echo do_shortcode(sprintf('[contact-form-7 id="%d"]', $cf7_form_id)); ?>
  </div>
  <?php
}

echo $html;
