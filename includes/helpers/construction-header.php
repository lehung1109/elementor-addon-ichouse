<?php
if (! defined('ABSPATH')) {
  exit;
}

if (! function_exists('eai_rc_map_construction_header_background')) {
  /**
   * Map Elementor media controls to ConstructionHeaderPictureModel.
   *
   * @param array<string, mixed> $mobile
   * @param array<string, mixed> $desktop
   * @return array<string, mixed>
   */
  function eai_rc_map_construction_header_background(
    array $mobile,
    array $desktop = [],
    string $mobile_size = 'full',
    string $desktop_size = 'full',
    string $alt = ''
  ): array {
    if ($mobile_size === '') {
      $mobile_size = 'full';
    }
    if ($desktop_size === '') {
      $desktop_size = 'full';
    }

    $resolved = eai_get_media_image_url($mobile, $mobile_size);
    $url = (string) ($resolved['url'] ?: ($mobile['url'] ?? ''));

    $img_alt = trim($alt);
    if ($img_alt === '') {
      if (! empty($mobile['alt'])) {
        $img_alt = (string) $mobile['alt'];
      } elseif (! empty($mobile['id'])) {
        $img_alt = (string) get_post_meta((int) $mobile['id'], '_wp_attachment_image_alt', true);
      }
    }

    $background = [
      'img' => [
        'url' => $url,
        'alt' => $img_alt,
        'width' => (int) ($resolved['width'] ?? 0),
        'height' => (int) ($resolved['height'] ?? 0),
      ],
    ];

    $desktop_resolved = eai_get_media_image_url($desktop, $desktop_size);
    $desktop_url = (string) ($desktop_resolved['url'] ?: ($desktop['url'] ?? ''));

    if ($desktop_url !== '') {
      $background['sources'] = [
        [
          'media' => '(min-width: 768px)',
          'srcSet' => $desktop_url,
        ],
      ];
    }

    return $background;
  }
}

if (! function_exists('eai_construction_header_resolve_animation')) {
  /**
   * @param array<string, mixed> $settings
   * @return array{enableFadeIn: bool, enableSlideIn: bool}
   */
  function eai_construction_header_resolve_animation(
    array $settings,
    string $fade_key,
    string $slide_key
  ): array {
    return [
      'enableFadeIn' => ($settings[$fade_key] ?? 'yes') === 'yes',
      'enableSlideIn' => ($settings[$slide_key] ?? 'yes') === 'yes',
    ];
  }
}

if (! function_exists('eai_construction_header_resolve_always_show_background')) {
  /**
   * Default true; false only when the current singular page is in the disable list.
   *
   * @param array<string, mixed> $settings
   */
  function eai_construction_header_resolve_always_show_background(array $settings): bool
  {
    $disabled = array_values(array_filter(array_map(
      'intval',
      (array) ($settings['disable_always_show_background_pages'] ?? [])
    )));

    if ($disabled === []) {
      return true;
    }

    $page_id = (int) get_queried_object_id();
    if ($page_id <= 0) {
      $page_id = (int) get_the_ID();
    }

    if ($page_id <= 0 || get_post_type($page_id) !== 'page') {
      return true;
    }

    return ! in_array($page_id, $disabled, true);
  }
}

if (! function_exists('eai_construction_header_get_rc_props')) {
  /**
   * Map Elementor settings to ConstructionHeaderModel props.
   *
   * @param array<string, mixed> $settings
   * @return array<string, mixed>
   */
  function eai_construction_header_get_rc_props(array $settings): array
  {
    $menu_id = (int) ($settings['menu_id'] ?? 0);

    $logo = is_array($settings['logo'] ?? null) ? $settings['logo'] : [];
    $logo_dimensions = is_array($settings['logo_dimensions'] ?? null)
      ? $settings['logo_dimensions']
      : [];
    $logo_link = is_array($settings['logo_link'] ?? null) ? $settings['logo_link'] : [];
    $logo_link_props = ! empty($logo_link['url']) ? $logo_link : null;

    $social_links = is_array($settings['social_links'] ?? null)
      ? $settings['social_links']
      : [];

    $bg_mobile = is_array($settings['background_mobile'] ?? null)
      ? $settings['background_mobile']
      : [];
    $bg_desktop = is_array($settings['background_desktop'] ?? null)
      ? $settings['background_desktop']
      : [];
    $bg_mobile_size = (string) ($settings['background_mobile_resolution'] ?? 'full');
    $bg_desktop_size = (string) ($settings['background_desktop_resolution'] ?? 'full');
    $bg_alt = trim((string) ($settings['background_alt'] ?? ''));

    $sticky_after = (int) ($settings['sticky_after_px'] ?? 80);
    if ($sticky_after < 0) {
      $sticky_after = 0;
    }

    $props = [
      'headerTop' => [
        'hotlineText' => (string) ($settings['hotline_text'] ?? ''),
      ],
      'logo' => eai_rc_map_media_model($logo, $logo_dimensions, $logo_link_props),
      'menu' => [
        'items' => eai_rc_map_header_menu_items(
          eai_get_menu_tree_with_active($menu_id)
        ),
        'navAriaLabel' => (string) ($settings['nav_aria_label'] ?? 'Điều hướng chính'),
        'openSubmenuLabelPrefix' => (string) (
          $settings['open_submenu_label_prefix'] ?? 'Mở menu'
        ),
      ],
      'socialLinks' => eai_rc_map_footer_social_links($social_links),
      'background' => eai_rc_map_construction_header_background(
        $bg_mobile,
        $bg_desktop,
        $bg_mobile_size,
        $bg_desktop_size,
        $bg_alt
      ),
      'autocomplete_search' => [
        'placeholder' => (string) ($settings['search_placeholder'] ?? ''),
        'api_url' => rest_url('wp/v2/posts'),
      ],
      'scrollMonitor' => [
        'stickyAfterPx' => $sticky_after,
        'targetId' => 'construction-header',
      ],
      'openMenuLabel' => (string) ($settings['open_menu_label'] ?? 'Mở menu'),
      'closeMenuLabel' => (string) ($settings['close_menu_label'] ?? 'Đóng menu'),
      'openSearchLabel' => (string) ($settings['open_search_label'] ?? 'Mở tìm kiếm'),
      'closeSearchLabel' => (string) ($settings['close_search_label'] ?? 'Đóng tìm kiếm'),
      'searchMenuItemLabel' => (string) (
        $settings['search_menu_item_label'] ?? 'Tìm kiếm'
      ),
      'menuModalAnimation' => eai_construction_header_resolve_animation(
        $settings,
        'menu_enable_fade_in',
        'menu_enable_slide_in'
      ),
      'searchModalAnimation' => eai_construction_header_resolve_animation(
        $settings,
        'search_enable_fade_in',
        'search_enable_slide_in'
      ),
      'alwaysShowBackground' => eai_construction_header_resolve_always_show_background($settings),
    ];

    $class_name = trim((string) ($settings['class_name'] ?? ''));
    if ($class_name !== '') {
      $props['className'] = $class_name;
    }

    return $props;
  }
}
