<?php
if (! defined('ABSPATH')) {
  exit;
}

if (! function_exists('eai_rc_footer_facebook_embed_html')) {
  /**
   * Facebook Page Plugin iframe HTML for FooterFanpages embed slot.
   */
  function eai_rc_footer_facebook_embed_html(string $page_url): string
  {
    $href = esc_url($page_url);
    if ($href === '') {
      return '';
    }

    $src = 'https://www.facebook.com/plugins/page.php?href=' . rawurlencode($page_url)
      . '&tabs=&width=340&height=130&small_header=false&adapt_container_width=true'
      . '&hide_cover=false&show_facepile=true';

    return '<iframe src="' . esc_attr($src) . '" width="340" height="130" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowfullscreen="true" allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share"></iframe>';
  }
}

if (! function_exists('eai_rc_map_footer_link_items')) {
  /**
   * @param array<int, int|string> $items
   * @return array<int, array{label: string, link: array<string, mixed>}>
   */
  function eai_rc_map_footer_link_items(array $items): array
  {
    $mapped = [];

    foreach ($items as $item) {
      $id = absint($item);
      if ($id <= 0) {
        continue;
      }

      $post = get_post($id);
      if (! ($post instanceof \WP_Post)) {
        continue;
      }

      if ($post->post_status !== 'publish') {
        continue;
      }

      if ($post->post_type !== 'page' && $post->post_type !== 'post') {
        continue;
      }

      $url = get_permalink($post);
      if (! is_string($url) || $url === '') {
        continue;
      }

      $mapped[] = [
        'label' => get_the_title($post),
        'link' => eai_rc_map_link([
          'url' => $url,
          'is_external' => false,
          'nofollow' => false,
        ]),
      ];
    }

    return $mapped;
  }
}

if (! function_exists('eai_rc_map_footer_menu_columns')) {
  /**
   * @param array<int, array{title: string, links: array<int, array<string, mixed>>}> $columns
   * @return array<int, array<string, mixed>>
   */
  function eai_rc_map_footer_menu_columns(array $columns): array
  {
    $mapped = [];

    foreach ($columns as $column) {
      if (! is_array($column)) {
        continue;
      }

      $title = trim((string) ($column['title'] ?? ''));
      $links = is_array($column['links'] ?? null) ? $column['links'] : [];
      $mapped_links = eai_rc_map_footer_link_items($links);

      if ($title === '' && empty($mapped_links)) {
        continue;
      }

      $mapped[] = [
        'title' => $title,
        'links' => $mapped_links,
      ];
    }

    return $mapped;
  }
}

if (! function_exists('eai_rc_map_footer_social_links')) {
  /**
   * @param array<int, array<string, mixed>> $links
   * @return array<int, array<string, mixed>>
   */
  function eai_rc_map_footer_social_links(array $links): array
  {
    $mapped = [];

    foreach ($links as $row) {
      if (! is_array($row)) {
        continue;
      }

      $url_control = is_array($row['link'] ?? null) ? $row['link'] : [];
      if (empty($url_control['url'])) {
        continue;
      }

      $icon = is_array($row['icon'] ?? null) ? $row['icon'] : [];
      $resolution = (string) ($row['icon_resolution'] ?? 'thumbnail');
      $icon_media = eai_rc_map_media_model(
        $icon,
        ['width' => 24, 'height' => 24],
        null,
        $resolution
      );

      if (empty($icon_media['url'])) {
        continue;
      }

      $alt_override = trim((string) ($row['icon_alt'] ?? ''));
      if ($alt_override !== '') {
        $icon_media['alt'] = $alt_override;
      }

      $mapped[] = [
        'icon' => $icon_media,
        'link' => eai_rc_map_link($url_control),
      ];
    }

    return $mapped;
  }
}

if (! function_exists('eai_rc_map_footer_contact_blocks')) {
  /**
   * @param array<int, array<string, mixed>> $blocks
   * @return array<int, array{title: string, contentHtml: string}>
   */
  function eai_rc_map_footer_contact_blocks(array $blocks): array
  {
    $mapped = [];

    foreach ($blocks as $block) {
      if (! is_array($block)) {
        continue;
      }

      $title = trim((string) ($block['title'] ?? ''));
      $content = (string) ($block['content'] ?? '');

      if ($title === '' && trim(wp_strip_all_tags($content)) === '') {
        continue;
      }

      $mapped[] = [
        'title' => $title,
        'contentHtml' => $content,
      ];
    }

    return $mapped;
  }
}

if (! function_exists('eai_rc_map_footer_fanpage_embeds')) {
  /**
   * @param array<int, array<string, mixed>> $embeds
   * @return array<int, array{embedHtml: string}>
   */
  function eai_rc_map_footer_fanpage_embeds(array $embeds): array
  {
    $mapped = [];

    foreach ($embeds as $row) {
      if (! is_array($row)) {
        continue;
      }

      $embed_html = trim((string) ($row['embed_html'] ?? ''));
      $facebook_control = is_array($row['facebook_page_url'] ?? null)
        ? $row['facebook_page_url']
        : [];
      $facebook_url = trim((string) ($facebook_control['url'] ?? ''));

      if ($embed_html === '' && $facebook_url !== '') {
        $embed_html = eai_rc_footer_facebook_embed_html($facebook_url);
      }

      if ($embed_html === '') {
        continue;
      }

      $mapped[] = [
        'embedHtml' => $embed_html,
      ];
    }

    return $mapped;
  }
}

if (! function_exists('eai_rc_map_footer_brand')) {
  /**
   * @param array<string, mixed> $settings
   * @return array<string, mixed>
   */
  function eai_rc_map_footer_brand(array $settings): array
  {
    $logo = is_array($settings['brand_logo'] ?? null) ? $settings['brand_logo'] : [];
    $logo_resolution = (string) ($settings['brand_logo_resolution'] ?? 'medium');
    $logo_link = is_array($settings['brand_logo_link'] ?? null) ? $settings['brand_logo_link'] : [];
    $badges = is_array($settings['brand_badges'] ?? null) ? $settings['brand_badges'] : [];

    return [
      'logo' => eai_rc_map_media_model(
        $logo,
        ['width' => 220, 'height' => 80],
        ! empty($logo_link['url']) ? $logo_link : null,
        $logo_resolution
      ),
      'descriptionHtml' => (string) ($settings['brand_description'] ?? ''),
      'badges' => eai_rc_map_partner_logos($badges),
      'hotlineLabel' => trim((string) ($settings['brand_hotline_label'] ?? '')),
      'hotlineText' => trim((string) ($settings['brand_hotline_text'] ?? '')),
      'hotline' => eai_rc_map_link(
        is_array($settings['brand_hotline_link'] ?? null) ? $settings['brand_hotline_link'] : []
      ),
    ];
  }
}

