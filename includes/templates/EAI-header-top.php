<?php
if (! defined('ABSPATH')) {
  exit;
}

$args = isset($args) && is_array($args) ? $args : [];
$text = $args['text'] ?? '';
$phone = $args['phone'] ?? '';
$search_placeholder = $args['search_placeholder'] ?? '';
$link_phone = $args['link_phone'] ?? [];
?>

<div>
  <div class="w-full bg-[#f36f21] text-xs">
    <div class="container">
      <div class="flex h-[52px] items-center justify-between gap-4">
        <p class="hidden text-sm font-medium text-white md:block !mbe-0">
          <?php echo $text; ?>
        </p>
<div class="flex flex-1 justify-center md:max-w-[420px] relative"><div class="w-full"><div class="relative"><input data-slot="input" class="w-full min-w-0 border-input px-2.5 py-1 text-base transition-colors outline-none file:inline-flex file:h-6 file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground focus-visible:border-ring focus-visible:ring-ring/50 disabled:pointer-events-none disabled:cursor-not-allowed disabled:bg-input/50 disabled:opacity-50 aria-invalid:border-destructive aria-invalid:ring-3 aria-invalid:ring-destructive/20 md:text-sm dark:bg-input/30 dark:disabled:bg-input/80 dark:aria-invalid:border-destructive/50 dark:aria-invalid:ring-destructive/40 h-9 rounded-full border-0 bg-white pr-10 !text-xs shadow-none placeholder:text-gray-400 focus-visible:ring-0 focus-visible:ring-offset-0" placeholder="Gõ tìm kiếm..." value=""/><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-500" aria-hidden="true"><path d="m21 21-4.34-4.34"></path><circle cx="11" cy="11" r="8"></circle></svg></div></div></div>
        <script data-rct="autocompleteSearch" type="application/json">
          {
            "placeholder": "<?php echo $search_placeholder; ?>",
            "api_url": "/wp-json/wp/v2/posts"
          }
        </script>
        <a
          href="<?php echo $link_phone && $link_phone['url'] ? esc_url($link_phone['url']) : ''; ?>"
          target="<?php echo $link_phone && $link_phone['is_external'] ? '_blank' : '_self'; ?>"
          rel="<?php echo $link_phone && $link_phone['nofollow'] ? 'nofollow' : ''; ?>"
          class="group/button inline-flex shrink-0 items-center justify-center rounded-lg border border-transparent bg-clip-padding text-sm font-medium whitespace-nowrap transition-all outline-none select-none focus-visible:border-ring focus-visible:ring-3 focus-visible:ring-ring/50 active:not-aria-[haspopup]:translate-y-px disabled:pointer-events-none disabled:opacity-50 aria-invalid:border-destructive aria-invalid:ring-3 aria-invalid:ring-destructive/20 dark:aria-invalid:border-destructive/50 dark:aria-invalid:ring-destructive/40 [&amp;_svg]:pointer-events-none [&amp;_svg]:shrink-0 [&amp;_svg:not([class*=&#x27;size-&#x27;])]:size-4 bg-primary text-primary-foreground [a]:hover:bg-primary/80 h-8 gap-1.5 px-2.5 has-data-[icon=inline-end]:pr-2 has-data-[icon=inline-start]:pl-2 h-10 rounded-full !bg-[#10b981] px-6 text-xs font-bold !text-white hover:!bg-[#039565]"
          data-slot="button"
          data-variant="default"
          data-size="default"><?php echo $phone; ?></a>
      </div>
    </div>
  </div>
</div>