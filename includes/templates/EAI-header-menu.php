<?php
if (! defined('ABSPATH')) {
  exit;
}

$args = isset($args) && is_array($args) ? $args : [];
$menu_id = $args['menu_id'] ?? '';

if (empty($menu_id)) {
  echo '<div class="eai-menu-empty">No menu selected</div>';
  return;
}

$items = wp_get_nav_menu_items($menu_id);

echo '<pre>';
var_dump($items);
echo '</pre>';
