<?php
if (! defined('ABSPATH')) {
  exit;
}

$args = isset($args) && is_array($args) ? $args : [];
$logo = $args['logo'] ?? '';
$logo_dimensions = $args['logo_dimensions'] ?? '';
$logo_link = $args['logo_link'] ?? '';
$info_list = $args['info_list'] ?? [];
?>

<div class="flex items-center justify-between gap-16">
  <div class="leading-0 max-w-[200px]">
    <!-- if logo is not empty -->
    <?php if ($logo) : ?>
      <a href="<?php echo esc_url(!empty($logo_link['url']) ? $logo_link['url'] : ''); ?>" target="<?php echo esc_attr(!empty($logo_link['is_external']) ? '_blank' : '_self'); ?>" rel="<?php echo esc_attr(!empty($logo_link['nofollow']) ? 'nofollow' : ''); ?>">
        <img
          src="<?php echo esc_url(!empty($logo['url']) ? $logo['url'] : ''); ?>"
          alt="<?php echo esc_attr(!empty($logo['alt']) ? $logo['alt'] : ''); ?>"
          width="<?php echo esc_attr(!empty($logo_dimensions['width']) ? $logo_dimensions['width'] : ''); ?>"
          height="<?php echo esc_attr(!empty($logo_dimensions['height']) ? $logo_dimensions['height'] : ''); ?>"
          class="w-full h-auto" />
      </a>
    <?php endif; ?>
  </div>
  <div class="flex items-center justify-between gap-8">
    <!-- loop -->
    <?php foreach ($info_list as $item) : ?>
      <div class="flex items-center gap-4">
        <div class="leading-0">
          <!-- if icon is not empty -->
          <?php if ($item['icon']) : ?>
            <img
              src="<?php echo esc_url(!empty($item['icon']['url']) ? $item['icon']['url'] : ''); ?>"
              alt="<?php echo esc_attr(!empty($item['icon']['alt']) ? $item['icon']['alt'] : ''); ?>"
              width="<?php echo esc_attr(!empty($item['icon_dimensions']['width']) ? $item['icon_dimensions']['width'] : ''); ?>"
              height="<?php echo esc_attr(!empty($item['icon_dimensions']['height']) ? $item['icon_dimensions']['height'] : ''); ?>"
              class="w-[45px] h-auto" />
          <?php endif; ?>
        </div>
        <div class="text-sm"><?php echo $item['text']; ?></div>
      </div>
    <?php endforeach; ?>
  </div>
</div>