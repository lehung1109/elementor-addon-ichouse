<?php
if (! defined('ABSPATH')) {
  exit;
}

$args = isset($args) && is_array($args) ? $args : [];
$search_placeholder = $args['search_placeholder'] ?? '';
?>

<div class="flex flex-1 justify-center md:max-w-[420px] relative"><div class="w-full"><div class="relative"><input data-slot="input" class="w-full min-w-0 border-input px-2.5 py-1 text-base transition-colors outline-none file:inline-flex file:h-6 file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground focus-visible:border-ring focus-visible:ring-ring/50 disabled:pointer-events-none disabled:cursor-not-allowed disabled:bg-input/50 disabled:opacity-50 aria-invalid:border-destructive aria-invalid:ring-3 aria-invalid:ring-destructive/20 md:text-sm dark:bg-input/30 dark:disabled:bg-input/80 dark:aria-invalid:border-destructive/50 dark:aria-invalid:ring-destructive/40 h-9 rounded-full border-0 bg-white pr-10 !text-xs shadow-none placeholder:text-gray-400 focus-visible:ring-0 focus-visible:ring-offset-0" placeholder="Gõ tìm kiếm..." value=""/><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-500" aria-hidden="true"><path d="m21 21-4.34-4.34"></path><circle cx="11" cy="11" r="8"></circle></svg></div></div></div>
<script data-rct="autocompleteSearch" type="application/json">
  {
    "placeholder": "<?php echo $search_placeholder; ?>",
    "api_url": "/wp-json/wp/v2/posts"
  }
</script>

