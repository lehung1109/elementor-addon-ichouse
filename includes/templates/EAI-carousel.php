<?php

if (! defined('ABSPATH')) {
  exit;
}

$args = isset($args) && is_array($args) ? $args : [];
$slides = $args['slides'] ?? [];
?>
<div><div class="undefined group"><div class="swiper w-full" style="--swiper-pagination-bullet-width:40px;--swiper-pagination-bullet-height:6px;--swiper-pagination-bullet-border-radius:0;--swiper-pagination-bullet-inactive-color:#ed1b24;--swiper-pagination-bullet-inactive-opacity:0.4;--swiper-pagination-color:#ed1b24;--swiper-pagination-bullet-horizontal-gap:8px"><div class="swiper-wrapper"><?php
  foreach ($slides as $slide):
    $image = eai_get_media_image_url($slide['image'], $slide['image_resolution'] ?? 'large');
?><div class="swiper-slide"><img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($slide['image']['alt'] ?? ''); ?>" width="<?php echo esc_attr($image['width']); ?>" height="<?php echo esc_attr($image['height']); ?>" class="block h-auto w-full"/></div><?php endforeach; ?></div><div class="swiper-button-prev"></div><div class="swiper-button-next"></div><div class="swiper-pagination"></div><div class="pointer-events-none absolute inset-x-0 top-1/2 z-10 flex -translate-y-1/2 justify-between px-[3%] group-hover:opacity-100 opacity-0 transition-all duration-300 group-hover:px-[2%]"><button class="cursor-pointer pointer-events-auto !text-white hover:!bg-[#f47c20] hover:!border-[#f47c20] !grid !h-10 !w-10 !rounded-full !border-2 !border-white !border-solid place-items-center transition-all duration-300 !p-0" aria-label="Previous slide"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left h-7 w-7" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg></button><button class="cursor-pointer pointer-events-auto !text-white hover:!bg-[#f47c20] hover:!border-[#f47c20] !grid !h-10 !w-10 !rounded-full !border-2 !border-white !border-solid place-items-center transition-all duration-300 !p-0" aria-label="Next slide"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right h-7 w-7" aria-hidden="true"><path d="m9 18 6-6-6-6"></path></svg></button></div></div></div>
</div>

<!-- Build json for carousel react component -->
 <?php
  $json_data = [
    "slides" => [],
  ];

  foreach ($slides as $slide) {
    $image = eai_get_media_image_url($slide['image'], $slide['image_resolution'] ?? 'large');
    $json_data['slides'][] = [
      "image" => [
        "url" => $image['url'],
        "alt" => $slide['image']['alt'] ?? '',
        "display_dimensions" => ["width" => $image['width'], "height" => $image['height']]
      ]
    ];
  }
 ?>
<script data-rct="carousel" type="application/json">
  <?php echo json_encode($json_data); ?>
</script>