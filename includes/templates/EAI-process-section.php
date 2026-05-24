<?php

if (! defined('ABSPATH')) {
  exit;
}

$args = isset($args) && is_array($args) ? $args : [];
$background_image = $args['background_image'] ?? [];
$background_image_resolution = $args['background_image_resolution'] ?? 'large';
$intro_content = $args['intro_content'] ?? '';
$steps = $args['steps'] ?? [];

$bg = eai_get_media_image_url($background_image, $background_image_resolution);
?>
<section class="relative overflow-hidden bg-cover bg-center bg-no-repeat text-white process-section p-8">
  <?php if (! empty($bg['url'])) : ?>
    <img
      src="<?php echo esc_url($bg['url']); ?>"
      alt="<?php echo esc_attr($background_image['alt'] ?? ''); ?>"
      <?php if (! empty($bg['width'])) : ?>
      width="<?php echo esc_attr((string) $bg['width']); ?>"
      <?php endif; ?>
      <?php if (! empty($bg['height'])) : ?>
      height="<?php echo esc_attr((string) $bg['height']); ?>"
      <?php endif; ?>
      class="absolute inset-0 z-0 object-cover max-w-none w-full h-full"
      loading="lazy"
      decoding="async" />
  <?php endif; ?>

  <div class="pointer-events-none absolute inset-0 z-1 bg-black/65" aria-hidden="true"></div>
  <div class="pointer-events-none absolute inset-0 z-1 bg-gradient-to-b from-black/50 via-black/40 to-black/60" aria-hidden="true"></div>

  <div class="relative z-10">
    <?php if ($intro_content !== '') : ?>
      <div class="process-section-intro mx-auto max-w-3xl text-center"><?php echo $intro_content; ?></div>
    <?php endif; ?>

    <?php if (! empty($steps)) : ?>
      <div class="relative mt-12">
        <div class="pointer-events-none absolute left-1/2 top-14 hidden w-screen -translate-x-1/2 md:block">
          <svg
            viewBox="0 0 1600 220"
            preserveAspectRatio="none"
            class="h-[160px] w-full"
            aria-hidden="true">
            <path
              d="M0,110 C140,165 250,55 400,95 C560,140 660,182 810,120 C950,65 1040,48 1200,98 C1360,145 1480,150 1600,110"
              fill="none"
              stroke="rgba(255,255,255,0.9)"
              stroke-width="3"
              stroke-dasharray="10 12"
              stroke-linecap="round"
              vector-effect="non-scaling-stroke"></path>
          </svg>
        </div>

        <div class="relative z-10 flex flex-wrap justify-center gap-x-10 gap-y-12">
          <?php foreach ($steps as $index => $step) :
            $step_id = $index + 1;
            $step_title = $step['title'] ?? '';
            $step_description = $step['description'] ?? '';
            $icon = $step['icon'] ?? 'user-round';
          ?>
            <div class="flex w-[240px] flex-col items-center text-center">
              <div class="relative mb-5 md:mb-6">
                <div class="relative h-24 w-24 rotate-45 rounded-[50%_0_50%_50%] border-[4px] border-white bg-white shadow-[0_10px_30px_rgba(0,0,0,0.28)] md:h-32 md:w-32 md:-translate-y-3">
                  <div class="absolute inset-[6px] rounded-[50%_0_50%_50%] border-[3px] border-emerald-400 md:inset-[8px] md:border-[4px]"></div>
                  <div class="absolute inset-0 flex -rotate-45 items-center justify-center text-black">
                    <?php
                    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG markup from trusted helper.
                    echo eai_get_process_section_icon_svg($icon);
                    ?>
                  </div>
                </div>
              </div>

              <div class="process-section-step-content w-full text-white">
                <?php if ($step_title !== '') : ?>
                  <h3 class="font-bold">
                    <?php echo esc_html($step_id . '. ' . $step_title); ?>
                  </h3>
                <?php endif; ?>
                <?php if ($step_description !== '') : ?>
                  <p class="mx-auto mt-3 max-w-[240px] text-sm leading-7 text-white/90 md:text-lg md:leading-8">
                    <?php echo esc_html($step_description); ?>
                  </p>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>
  </div>
</section>