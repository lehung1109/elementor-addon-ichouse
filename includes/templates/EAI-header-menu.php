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

$items = eai_get_menu_tree_with_active($menu_id);

?>

<nav aria-label="Main navigation" class="relative">
  <ul class="flex min-h-[54px] items-stretch justify-center !bg-[#1f1f1f] !mb-0">
    <?php foreach ($items as $item) : ?>
      <li class="group relative">
        <a
          href="<?php echo $item['href']; ?>"
          class="flex h-[54px] items-center px-6 text-[17px] font-medium transition-colors duration-150 <?php echo $item['active'] ? '!bg-[#f47c20]' : '!bg-[#1f1f1f] hover:!bg-[#f47c20]'; ?> !text-white"><span><?php echo $item['label']; ?></span>

          <?php if (!empty($item['children'])) : ?>
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="24"
              height="24"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
              class="lucide lucide-chevron-down ml-1.5 h-3.5 w-3.5 opacity-80"
              aria-hidden="true">
              <path d="m6 9 6 6 6-6"></path>
            </svg>
          <?php endif; ?>
        </a>

        <?php if (!empty($item['children']) && count($item['children']) > 0) : ?>
          <div class="invisible absolute left-1/2 -translate-x-1/2 top-full z-50 opacity-0 transition-all duration-150 group-hover:visible group-hover:opacity-100">
            <div class="relative !bg-[#f6f6f6] shadow-[0_2px_10px_rgba(0,0,0,0.18)] border !border-[#d9d9d9]">
              <div class="absolute left-1/2 top-0 h-0 w-0 -translate-x-1/2 -translate-y-full border-l-[10px] border-r-[10px] border-b-[10px] border-l-transparent border-r-transparent !border-b-[#f6f6f6]">
              </div>

              <?php if (!empty($item['children']) && count($item['children']) > 0 && !empty($item['children'][0]['children']) && count($item['children'][0]['children']) > 0) : ?>
                <div class="grid auto-cols-fr grid-flow-col gap-5 px-7 py-6 w-max">
                  <?php foreach ($item['children'] as $child) : ?>
                    <div class="min-w-[270px]">
                      <a href="<?php echo $child['href']; ?>" class="block mb-3 text-[15px] font-bold uppercase !text-[#111] hover:!text-[#d82a28] hover:pl-2 hover:font-bold transition-all">
                        <?php echo $child['label']; ?>
                      </a>

                      <ul>
                        <?php foreach ($child['children'] as $child_child) : ?>
                          <li class="border-t border-[#e1e1e1] first:border-t">
                            <a href="<?php echo $child_child['href']; ?>" class="block py-3 text-[18px] leading-[1.35] !text-[#777] transition-colors duration-150 hover:!text-[#111]"><?php echo $child_child['label']; ?></a>
                          </li>
                        <?php endforeach; ?>
                      </ul>
                    </div>
                  <?php endforeach; ?>
                </div>
              <?php else: ?>
                <div class="min-w-[320px] px-6 py-4">
                  <ul>
                    <?php foreach ($item['children'] as $child) : ?>
                      <li class="border-t !border-[#e1e1e1] first:border-t-0">
                        <a href="<?php echo $child['href']; ?>" class="block py-3 text-[18px] leading-[1.35] !text-[#333] transition-all duration-150 hover:!text-[#d82a28] hover:pl-2 hover:font-bold"><?php echo $child['label']; ?></a>
                      </li>
                    <?php endforeach; ?>
                  </ul>
                </div>
              <?php endif; ?>
            </div>
          </div>
        <?php endif; ?>
      </li>
    <?php endforeach; ?>
  </ul>
</nav>