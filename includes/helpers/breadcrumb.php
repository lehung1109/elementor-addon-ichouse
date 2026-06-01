<?php
if (! defined('ABSPATH')) {
  exit;
}

if (! function_exists('eai_breadcrumb_map_link_level')) {
  /**
   * @param array<string, mixed> $settings
   * @return array{label: string, link: array{url: string, is_external: bool, nofollow: bool}}|null
   */
  function eai_breadcrumb_map_link_level(array $settings, string $label_key, string $link_key): ?array
  {
    $label = trim((string) ($settings[$label_key] ?? ''));
    $link_control = is_array($settings[$link_key] ?? null) ? $settings[$link_key] : [];
    $link = eai_rc_map_link($link_control);

    if ($label === '' || trim($link['url']) === '') {
      return null;
    }

    return [
      'label' => $label,
      'link' => $link,
    ];
  }
}

if (! function_exists('eai_breadcrumb_get_rc_props')) {
  /**
   * @param array<string, mixed> $settings
   * @return array<string, mixed>
   */
  function eai_breadcrumb_get_rc_props(int $post_id, array $settings): array
  {
    $link_levels = [];

    foreach (
      [
        ['level_1_label', 'level_1_link'],
        ['level_2_label', 'level_2_link'],
      ] as [$label_key, $link_key]
    ) {
      $level = eai_breadcrumb_map_link_level($settings, $label_key, $link_key);
      if ($level !== null) {
        $link_levels[] = $level;
      }
    }

    $props = [
      'linkLevels' => $link_levels,
      'currentLabel' => $post_id > 0 ? get_the_title($post_id) : '',
    ];

    $class_name = trim((string) ($settings['class_name'] ?? ''));
    if ($class_name !== '') {
      $props['className'] = $class_name;
    }

    return $props;
  }
}

if (! function_exists('eai_breadcrumb_get_editor_sample_props')) {
  /**
   * Static demo props for Elementor editor when there is no post context or title.
   * Mirrors api-rc src/data/breadcrumb.ts.
   *
   * @param array<string, mixed> $settings
   * @return array<string, mixed>
   */
  function eai_breadcrumb_get_editor_sample_props(array $settings): array
  {
    $props = [
      'linkLevels' => [
        [
          'label' => 'Trang chủ',
          'link' => [
            'url' => '/',
            'is_external' => false,
            'nofollow' => false,
          ],
        ],
        [
          'label' => '(Đã xác minh)',
          'link' => [
            'url' => '/ban-dao-bep',
            'is_external' => false,
            'nofollow' => false,
          ],
          'verified' => true,
        ],
      ],
      'currentLabel' => 'TOP 30+Mẫu bàn đảo bếp rời đẹp, thông minh và hiện đại 2026',
    ];

    $class_name = trim((string) ($settings['class_name'] ?? ''));
    if ($class_name !== '') {
      $props['className'] = $class_name;
    }

    return $props;
  }
}
