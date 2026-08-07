<?php
if (! defined('ABSPATH')) {
  exit;
}

if (! function_exists('eai_rc_map_development_partners_items')) {
  /**
   * @param array<int, array<string, mixed>> $rows
   * @return array<int, array<string, mixed>>
   */
  function eai_rc_map_development_partners_items(array $rows): array
  {
    $mapped = [];

    foreach ($rows as $row) {
      if (! is_array($row)) {
        continue;
      }

      $image = is_array($row['image'] ?? null) ? $row['image'] : [];
      $resolution = (string) ($row['image_resolution'] ?? 'medium');
      $media = eai_rc_map_media_model($image, [], null, $resolution);

      if (empty($media['url'])) {
        continue;
      }

      $alt_override = trim((string) ($row['alt'] ?? ''));
      if ($alt_override !== '') {
        $media['alt'] = $alt_override;
      }

      $mapped[] = ['image' => $media];
    }

    return $mapped;
  }
}

if (! function_exists('eai_development_partners_get_target_id')) {
  function eai_development_partners_get_target_id(string $widget_id): string
  {
    $safe_widget_id = sanitize_html_class($widget_id);

    return 'development-partners-' . ($safe_widget_id !== '' ? $safe_widget_id : 'widget');
  }
}

if (! function_exists('eai_development_partners_get_rc_props')) {
  /**
   * @param array<string, mixed> $settings
   * @return array<string, mixed>
   */
  function eai_development_partners_get_rc_props(array $settings, string $widget_id): array
  {
    $items = is_array($settings['items'] ?? null) ? $settings['items'] : [];
    $class_name = trim((string) ($settings['class_name'] ?? ''));

    $props = [
      'title' => (string) ($settings['title'] ?? ''),
      'items' => eai_rc_map_development_partners_items($items),
      'scrollReveal' => [
        'targetId' => eai_development_partners_get_target_id($widget_id),
      ],
    ];

    if ($class_name !== '') {
      $props['className'] = $class_name;
    }

    return $props;
  }
}

if (! function_exists('eai_development_partners_get_editor_sample_props')) {
  /**
   * Static sample for Elementor canvas (mirrors api-rc src/data/development-partners.ts).
   *
   * @param array<string, mixed> $settings
   * @return array<string, mixed>
   */
  function eai_development_partners_get_editor_sample_props(array $settings, string $widget_id): array
  {
    $class_name = trim((string) ($settings['class_name'] ?? ''));
    $title = trim((string) ($settings['title'] ?? ''));
    $partners = [
      ['Viet+Ceramics', 'Viet Ceramics'],
      ['VIETCG', 'VIETCG'],
      ['DNA', 'DNA'],
      ['HUYNDOOR', 'HUYNDOOR'],
      ['MB+GLASS', 'MB GLASS'],
      ['HOA+PHAT', 'Hòa Phát'],
      ['Eurowindow', 'Eurowindow'],
      ['AN+CUONG', 'An Cường'],
      ['OBuilder', 'OBuilder'],
      ['D%26A+LIVING', 'D&A Living Collection'],
      ['LEGGO', 'LEGGO'],
    ];

    $items = array_map(
      static function (array $partner): array {
        return [
          'image' => [
            'url' => 'https://placehold.co/200x80/png?text=' . $partner[0],
            'alt' => $partner[1],
            'display_dimensions' => ['width' => 200, 'height' => 80],
          ],
        ];
      },
      $partners
    );

    $props = [
      'title' => $title !== '' ? $title : 'ĐỐI TÁC PHÁT TRIỂN',
      'items' => $items,
      'scrollReveal' => [
        'targetId' => eai_development_partners_get_target_id($widget_id),
      ],
    ];

    if ($class_name !== '') {
      $props['className'] = $class_name;
    }

    return $props;
  }
}
