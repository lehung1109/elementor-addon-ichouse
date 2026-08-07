<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class DevelopmentPartnersHelperTest extends TestCase
{
  public function testMapsValidRowsAndSkipsRowsWithoutAnImageUrl(): void
  {
    $items = eai_rc_map_development_partners_items([
      ['image' => ['url' => 'https://example.com/a.png', 'alt' => 'A']],
      ['image' => ['url' => '']],
      'invalid-row',
    ]);

    self::assertCount(1, $items);
    self::assertSame('https://example.com/a.png', $items[0]['image']['url']);
    self::assertSame('A', $items[0]['image']['alt']);
  }

  public function testUsesSelectedResolutionAndAltOverrideForAttachmentMedia(): void
  {
    $items = eai_rc_map_development_partners_items([
      [
        'image' => ['id' => 42],
        'image_resolution' => 'medium',
        'alt' => 'Custom partner alt',
      ],
    ]);

    self::assertSame('https://example.com/logo-medium.png', $items[0]['image']['url']);
    self::assertSame('Custom partner alt', $items[0]['image']['alt']);
    self::assertSame(['width' => 320, 'height' => 128], $items[0]['image']['display_dimensions']);
    self::assertSame('logo-medium.png 320w', $items[0]['image']['srcSet']);
  }

  public function testBuildsApiPropsWithOptionalClassNameAndUniqueTargetId(): void
  {
    $settings = [
      'class_name' => 'custom-section',
      'title' => 'ĐỐI TÁC',
      'items' => [['image' => ['url' => 'https://example.com/logo.png']]],
    ];

    $first = eai_development_partners_get_rc_props($settings, 'abc-123');
    $second = eai_development_partners_get_rc_props($settings, 'xyz 456');

    self::assertSame('custom-section', $first['className']);
    self::assertSame('ĐỐI TÁC', $first['title']);
    self::assertSame('development-partners-abc-123', $first['scrollReveal']['targetId']);
    self::assertSame('development-partners-xyz456', $second['scrollReveal']['targetId']);
    self::assertNotSame($first['scrollReveal']['targetId'], $second['scrollReveal']['targetId']);
  }

  public function testOmitsBlankClassNameAndUsesStableTargetFallback(): void
  {
    $props = eai_development_partners_get_rc_props([
      'class_name' => '   ',
      'title' => '',
      'items' => [],
    ], '***');

    self::assertArrayNotHasKey('className', $props);
    self::assertSame('development-partners-widget', $props['scrollReveal']['targetId']);
  }

  public function testEditorSampleMirrorsCanonicalFixtureAndPreservesSettings(): void
  {
    $props = eai_development_partners_get_editor_sample_props([
      'class_name' => 'editor-class',
      'title' => 'Tiêu đề trong editor',
    ], 'editor-1');

    self::assertSame('Tiêu đề trong editor', $props['title']);
    self::assertSame('editor-class', $props['className']);
    self::assertSame('development-partners-editor-1', $props['scrollReveal']['targetId']);
    self::assertCount(11, $props['items']);
    self::assertSame(
      ['width' => 200, 'height' => 80],
      $props['items'][0]['image']['display_dimensions']
    );
  }
}
