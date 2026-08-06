<?php
if (! defined('ABSPATH')) {
  exit;
}

if (! function_exists('eai_news_events_query_latest_by_post_type')) {
  /**
   * @return array<int, int>
   */
  function eai_news_events_query_latest_by_post_type(string $post_type, int $limit = 5): array
  {
    if ($limit <= 0 || $post_type === '' || ! post_type_exists($post_type)) {
      return [];
    }

    $query = new \WP_Query([
      'post_type' => $post_type,
      'post_status' => 'publish',
      'posts_per_page' => $limit,
      'orderby' => 'modified',
      'order' => 'DESC',
      'fields' => 'ids',
      'no_found_rows' => true,
      'ignore_sticky_posts' => true,
      'meta_query' => [
        [
          'key' => '_thumbnail_id',
          'compare' => 'EXISTS',
        ],
      ],
    ]);

    return array_map('intval', $query->posts);
  }
}

if (! function_exists('eai_rc_map_news_events_item_from_post')) {
  /**
   * @return array<string, mixed>|null
   */
  function eai_rc_map_news_events_item_from_post(
    \WP_Post $post,
    string $image_size = 'large'
  ): ?array {
    $thumbnail_id = (int) get_post_thumbnail_id($post);
    if ($thumbnail_id <= 0) {
      return null;
    }

    $media = eai_rc_map_media_model(['id' => $thumbnail_id], [], null, $image_size);
    if (empty($media['url'])) {
      return null;
    }

    $permalink = get_permalink($post);
    if (! is_string($permalink) || trim($permalink) === '') {
      return null;
    }

    return [
      'image' => $media,
      'time' => (string) get_the_modified_date('', $post),
      'title' => get_the_title($post),
      'link' => eai_rc_map_link(['url' => $permalink]),
    ];
  }
}

if (! function_exists('eai_rc_map_news_events_items_from_posts')) {
  /**
   * @param array<int, int> $post_ids
   * @return array<int, array<string, mixed>>
   */
  function eai_rc_map_news_events_items_from_posts(
    array $post_ids,
    string $image_size = 'large'
  ): array {
    $mapped = [];

    foreach ($post_ids as $post_id) {
      $post = get_post((int) $post_id);
      if (! $post instanceof \WP_Post || $post->post_status !== 'publish') {
        continue;
      }

      $item = eai_rc_map_news_events_item_from_post($post, $image_size);
      if ($item !== null) {
        $mapped[] = $item;
      }
    }

    return $mapped;
  }
}

if (! function_exists('eai_news_events_get_rc_props')) {
  /**
   * @param array<string, mixed> $settings
   * @return array<string, mixed>
   */
  function eai_news_events_get_rc_props(array $settings): array
  {
    $post_type = sanitize_key((string) ($settings['post_type'] ?? 'post'));
    if ($post_type === '' || ! post_type_exists($post_type)) {
      $post_type = 'post';
    }

    $image_size = (string) ($settings['image_resolution'] ?? 'large');
    $button_link = is_array($settings['button_link'] ?? null) ? $settings['button_link'] : [];
    $class_name = trim((string) ($settings['class_name'] ?? ''));
    $target_id = trim((string) ($settings['scroll_reveal_target_id'] ?? 'news-events'));

    $post_ids = eai_news_events_query_latest_by_post_type($post_type, 5);

    $props = [
      'title' => (string) ($settings['title'] ?? ''),
      'items' => eai_rc_map_news_events_items_from_posts($post_ids, $image_size),
      'buttonLabel' => (string) ($settings['button_label'] ?? ''),
      'buttonLink' => eai_rc_map_link($button_link),
      'scrollReveal' => [
        'targetId' => $target_id !== '' ? $target_id : 'news-events',
      ],
    ];

    if ($class_name !== '') {
      $props['className'] = $class_name;
    }

    return $props;
  }
}

if (! function_exists('eai_news_events_get_editor_sample_props')) {
  /**
   * Static sample for Elementor canvas (mirrors api-rc src/data/news-events.ts).
   *
   * @param array<string, mixed> $settings
   * @return array<string, mixed>
   */
  function eai_news_events_get_editor_sample_props(array $settings): array
  {
    $button_link = is_array($settings['button_link'] ?? null) ? $settings['button_link'] : [];
    $class_name = trim((string) ($settings['class_name'] ?? ''));
    $target_id = trim((string) ($settings['scroll_reveal_target_id'] ?? 'news-events'));
    $title = trim((string) ($settings['title'] ?? ''));
    $button_label = trim((string) ($settings['button_label'] ?? ''));

    $props = [
      'title' => $title !== '' ? $title : 'TIN TỨC - SỰ KIỆN',
      'items' => [
        [
          'image' => [
            'url' => 'https://placehold.co/800x450/png?text=News+1',
            'alt' => 'Lễ khởi công',
            'display_dimensions' => ['width' => 800, 'height' => 450],
          ],
          'time' => '15/03/2026',
          'title' => 'Một buổi lễ khởi công, nhưng là cả hành trình chuẩn bị chỉn chu!',
          'link' => ['url' => '#', 'is_external' => false, 'nofollow' => false],
        ],
        [
          'image' => [
            'url' => 'https://placehold.co/800x450/png?text=News+2',
            'alt' => 'Nhà 2 mặt tiền',
            'display_dimensions' => ['width' => 800, 'height' => 450],
          ],
          'time' => '12/03/2026',
          'title' => 'Top 8 thiết kế nhà 2 mặt tiền đẹp và tối ưu công năng',
          'link' => ['url' => '#', 'is_external' => false, 'nofollow' => false],
        ],
        [
          'image' => [
            'url' => 'https://placehold.co/800x450/png?text=News+3',
            'alt' => 'Văn phòng 7 tầng',
            'display_dimensions' => ['width' => 800, 'height' => 450],
          ],
          'time' => '10/03/2026',
          'title' => 'Gợi ý 6 mẫu nhà văn phòng 7 tầng hiện đại',
          'link' => ['url' => '#', 'is_external' => false, 'nofollow' => false],
        ],
        [
          'image' => [
            'url' => 'https://placehold.co/800x450/png?text=News+4',
            'alt' => 'Showroom',
            'display_dimensions' => ['width' => 800, 'height' => 450],
          ],
          'time' => '08/03/2026',
          'title' => 'Không gian showroom tối giản với ánh sáng tự nhiên',
          'link' => ['url' => '#', 'is_external' => false, 'nofollow' => false],
        ],
        [
          'image' => [
            'url' => 'https://placehold.co/800x450/png?text=News+5',
            'alt' => 'Thi công nội thất',
            'display_dimensions' => ['width' => 800, 'height' => 450],
          ],
          'time' => '05/03/2026',
          'title' => 'Quy trình thi công nội thất chuẩn ICHouse',
          'link' => ['url' => '#', 'is_external' => false, 'nofollow' => false],
        ],
      ],
      'buttonLabel' => $button_label !== '' ? $button_label : 'TÌM HIỂU THÊM',
      'buttonLink' => eai_rc_map_link(
        trim((string) ($button_link['url'] ?? '')) !== ''
          ? $button_link
          : ['url' => '/tin-tuc', 'is_external' => false, 'nofollow' => false]
      ),
      'scrollReveal' => [
        'targetId' => $target_id !== '' ? $target_id : 'news-events',
      ],
    ];

    if ($class_name !== '') {
      $props['className'] = $class_name;
    }

    return $props;
  }
}
