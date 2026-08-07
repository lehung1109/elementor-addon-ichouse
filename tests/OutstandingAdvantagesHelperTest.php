<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class OutstandingAdvantagesHelperTest extends TestCase
{
  public function testMapsMediaFieldsWithSelectedResolutionsAndAltOverrides(): void
  {
    $settings = [
      'image' => ['id' => 42],
      'image_resolution' => 'medium',
      'image_alt' => 'Top image',
      'items' => [[
        'background_mobile_image' => ['id' => 42],
        'background_mobile_image_resolution' => 'mobile',
        'background_mobile_alt' => 'Mobile background',
        'background_desktop_image' => ['id' => 42],
        'background_desktop_image_resolution' => 'large',
        'background_desktop_alt' => 'Desktop background',
        'subtitle' => 'Ưu điểm vượt trội',
        'title' => 'Đội ngũ chuyên gia',
        'description' => 'Mô tả',
      ]],
    ];
    $props = eai_outstanding_advantages_get_rc_props($settings, 'test-1');
    $items = $props['items'];

    self::assertCount(1, $items);
    self::assertSame('https://example.com/logo-medium.png', $props['image']['url']);
    self::assertSame('Top image', $props['image']['alt']);
    self::assertArrayNotHasKey('image', $items[0]);
    self::assertSame('https://example.com/logo-mobile.png', $items[0]['backgroundMobileImage']['url']);
    self::assertSame('Mobile background', $items[0]['backgroundMobileImage']['alt']);
    self::assertSame('https://example.com/logo-large.png', $items[0]['backgroundDesktopImage']['url']);
    self::assertSame('Desktop background', $items[0]['backgroundDesktopImage']['alt']);
    self::assertSame('Ưu điểm vượt trội', $items[0]['subtitle']);
    self::assertSame('Đội ngũ chuyên gia', $items[0]['title']);
    self::assertSame('Mô tả', $items[0]['description']);
  }

  public function testKeepsItemWithOnlyOneBackgroundAndAllowsOptionalFieldsToBeEmpty(): void
  {
    $items = eai_rc_map_outstanding_advantages_items([
      [
        'background_mobile_image' => ['url' => 'https://example.com/mobile.jpg'],
        'background_desktop_image' => ['url' => ''],
        'title' => 'Lợi thế',
      ],
    ]);

    self::assertCount(1, $items);
    self::assertSame('https://example.com/mobile.jpg', $items[0]['backgroundMobileImage']['url']);
    self::assertSame('', $items[0]['backgroundDesktopImage']['url']);
    self::assertArrayNotHasKey('image', $items[0]);
    self::assertSame('', $items[0]['subtitle']);
    self::assertSame('', $items[0]['description']);
  }

  public function testSkipsRowsWithoutTitleOrBackground(): void
  {
    $items = eai_rc_map_outstanding_advantages_items([
      [
        'background_mobile_image' => ['url' => 'https://example.com/mobile.jpg'],
        'title' => '   ',
      ],
      [
        'background_mobile_image' => ['url' => ''],
        'background_desktop_image' => ['url' => ''],
        'title' => 'Không có nền',
      ],
      'invalid-row',
    ]);

    self::assertSame([], $items);
  }

  public function testBuildsPropsWithOptionalClassNameAndUniqueTargetId(): void
  {
    $settings = [
      'class_name' => 'custom-advantages',
      'items' => [[
        'background_desktop_image' => ['url' => 'https://example.com/desktop.jpg'],
        'title' => 'Lợi thế',
      ]],
    ];

    $first = eai_outstanding_advantages_get_rc_props($settings, 'abc-123');
    $second = eai_outstanding_advantages_get_rc_props($settings, 'xyz 456');

    self::assertSame('custom-advantages', $first['className']);
    self::assertSame('outstanding-advantages-abc-123', $first['scrollReveal']['targetId']);
    self::assertSame('outstanding-advantages-xyz456', $second['scrollReveal']['targetId']);
    self::assertNotSame($first['scrollReveal']['targetId'], $second['scrollReveal']['targetId']);
  }

  public function testOmitsBlankClassNameAndUsesStableTargetFallback(): void
  {
    $props = eai_outstanding_advantages_get_rc_props([
      'class_name' => '   ',
      'items' => [],
    ], '***');

    self::assertArrayNotHasKey('className', $props);
    self::assertSame('outstanding-advantages-widget', $props['scrollReveal']['targetId']);
  }

  public function testEditorSampleMirrorsCanonicalFixtureAndPreservesClassName(): void
  {
    $props = eai_outstanding_advantages_get_editor_sample_props([
      'class_name' => 'editor-class',
    ], 'editor-1');

    self::assertSame('editor-class', $props['className']);
    self::assertSame('outstanding-advantages-editor-1', $props['scrollReveal']['targetId']);
    self::assertCount(3, $props['items']);
    self::assertSame('https://placehold.co/229x137/png?text=Top+Image', $props['image']['url']);
    self::assertArrayNotHasKey('image', $props['items'][0]);
    self::assertSame('Ưu điểm vượt trội', $props['items'][0]['subtitle']);
    self::assertSame('Chuyên gia giàu kinh nghiệm', $props['items'][0]['title']);
    self::assertSame(
      ['width' => 384, 'height' => 480],
      $props['items'][0]['backgroundMobileImage']['display_dimensions']
    );
    self::assertSame(
      ['width' => 229, 'height' => 137],
      $props['image']['display_dimensions']
    );
  }
}
