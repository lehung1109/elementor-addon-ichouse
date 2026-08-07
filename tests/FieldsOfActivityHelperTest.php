<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class FieldsOfActivityHelperTest extends TestCase
{
  public function testMapsValidItemsAndSkipsInvalidRows(): void
  {
    $props = eai_fields_of_activity_get_rc_props([
      'items' => [
        [
          'title' => ' Thiết kế ',
          'content_html' => '<ul><li>Tư vấn</li></ul>',
          'default_open' => 'yes',
        ],
        ['title' => '   '],
        'invalid-row',
      ],
    ], 'abc-123');

    self::assertCount(1, $props['items']);
    self::assertSame('Thiết kế', $props['items'][0]['title']);
    self::assertSame('<ul><li>Tư vấn</li></ul>', $props['items'][0]['contentHtml']);
    self::assertTrue($props['items'][0]['defaultOpen']);
  }

  public function testAppliesSharedIconToEveryMappedItem(): void
  {
    $props = eai_fields_of_activity_get_rc_props([
      'icon_image' => ['id' => 42],
      'icon_image_resolution' => 'thumbnail',
      'items' => [
        ['title' => 'Thiết kế'],
        ['title' => 'Thi công'],
      ],
    ], 'shared-icon');

    self::assertSame('https://example.com/logo-thumbnail.png', $props['items'][0]['iconImage']['url']);
    self::assertSame($props['items'][0]['iconImage'], $props['items'][1]['iconImage']);
  }

  public function testUsesFirstLegacyItemIconWhenSharedIconIsEmpty(): void
  {
    $props = eai_fields_of_activity_get_rc_props([
      'items' => [
        [
          'title' => 'Thiết kế',
          'icon_image' => ['url' => 'https://example.com/legacy.png', 'alt' => 'Legacy'],
          'icon_image_resolution' => 'thumbnail',
        ],
        ['title' => 'Thi công'],
      ],
    ], 'legacy-icon');

    self::assertSame('https://example.com/legacy.png', $props['items'][0]['iconImage']['url']);
    self::assertSame($props['items'][0]['iconImage'], $props['items'][1]['iconImage']);
  }

  public function testBuildsCompleteModelPropsAndUniqueTechnicalIds(): void
  {
    $settings = [
      'class_name' => ' custom-fields ',
      'title' => 'Lĩnh vực hoạt động',
      'items' => [['title' => 'Thiết kế']],
      'image_1' => ['url' => 'https://example.com/one.jpg'],
      'image_2' => ['id' => 42],
      'image_2_resolution' => 'large',
      'button_label' => 'Xem thêm',
      'button_link' => [
        'url' => '/linh-vuc',
        'is_external' => true,
        'nofollow' => true,
      ],
      'scroll_reveal_target_id' => 'legacy-manual-id',
    ];

    $first = eai_fields_of_activity_get_rc_props($settings, 'abc-123');
    $second = eai_fields_of_activity_get_rc_props($settings, 'xyz 456');

    self::assertSame('custom-fields', $first['className']);
    self::assertSame('Lĩnh vực hoạt động', $first['title']);
    self::assertCount(2, $first['images']);
    self::assertSame('https://example.com/logo-large.png', $first['images'][1]['url']);
    self::assertSame([
      'url' => '/linh-vuc',
      'is_external' => true,
      'nofollow' => true,
    ], $first['buttonLink']);
    self::assertSame('fields-of-activity-abc-123', $first['scrollReveal']['targetId']);
    self::assertSame('fields-of-activity-abc-123-item', $first['checkboxIdPrefix']);
    self::assertSame('fields-of-activity-xyz456', $second['scrollReveal']['targetId']);
    self::assertNotSame($first['scrollReveal']['targetId'], $second['scrollReveal']['targetId']);
  }

  public function testUsesStableFallbackIdsAndOmitsBlankClassName(): void
  {
    $props = eai_fields_of_activity_get_rc_props([
      'class_name' => '   ',
      'items' => [],
    ], '***');

    self::assertArrayNotHasKey('className', $props);
    self::assertSame('fields-of-activity-widget', $props['scrollReveal']['targetId']);
    self::assertSame('fields-of-activity-widget-item', $props['checkboxIdPrefix']);
  }
}
