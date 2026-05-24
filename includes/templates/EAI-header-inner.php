<?php
if (! defined('ABSPATH')) {
  exit;
}

$args = isset($args) && is_array($args) ? $args : [];
$logo = $args['logo'] ?? '';
$info_list = $args['info_list'] ?? [];

var_dump($logo);
var_dump($info_list);
