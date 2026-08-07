<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class ServiceOfferingsHelperTest extends TestCase
{
  public function testMapsValidRowsAndSkipsRowsMissingRequiredContent(): void
  {
    $items = eai_rc_map_service_offerings_items([
      [
        'title' => 'Thiết kế',
        'description_html' => '<ul><li>Tư vấn</li></ul>',
        'image' => ['url' => 'https://example.com/design.png', 'alt' => 'Design'],
      ],
      ['title' => '   ', 'image' => ['url' => 'https://example.com/no-title.png']],
      ['title' => 'Không ảnh', 'image' => ['url' => '']],
      'invalid-row',
    ]);

    self::assertCount(1, $items);
    self::assertSame('Thiết kế', $items[0]['title']);
    self::assertSame('<ul><li>Tư vấn</li></ul>', $items[0]['descriptionHtml']);
    self::assertSame('https://example.com/design.png', $items[0]['image']['url']);
  }

  public function testUsesSelectedResolutionAndAltOverrideForAttachmentMedia(): void
  {
    $items = eai_rc_map_service_offerings_items([
      [
        'title' => 'Thi công',
        'description_html' => '',
        'image' => ['id' => 42],
        'image_resolution' => 'large',
        'alt' => 'Ảnh thi công',
      ],
    ]);

    self::assertSame('https://example.com/logo-large.png', $items[0]['image']['url']);
    self::assertSame('Ảnh thi công', $items[0]['image']['alt']);
    self::assertSame(['width' => 320, 'height' => 128], $items[0]['image']['display_dimensions']);
    self::assertSame('logo-large.png 320w', $items[0]['image']['srcSet']);
    self::assertSame('(max-width: 320px) 100vw, 320px', $items[0]['image']['sizes']);
  }

  public function testBuildsPropsWithOptionalClassNameAndUniqueTargetId(): void
  {
    $settings = [
      'class_name' => 'custom-offerings',
      'items' => [[
        'title' => 'Thiết kế',
        'description_html' => 'Mô tả',
        'image' => ['url' => 'https://example.com/design.png'],
      ]],
    ];

    $first = eai_service_offerings_get_rc_props($settings, 'abc-123');
    $second = eai_service_offerings_get_rc_props($settings, 'xyz 456');

    self::assertSame('custom-offerings', $first['className']);
    self::assertSame('service-offerings-abc-123', $first['scrollReveal']['targetId']);
    self::assertSame('service-offerings-xyz456', $second['scrollReveal']['targetId']);
    self::assertNotSame($first['scrollReveal']['targetId'], $second['scrollReveal']['targetId']);
  }

  public function testOmitsBlankClassNameAndUsesStableTargetFallback(): void
  {
    $props = eai_service_offerings_get_rc_props([
      'class_name' => '   ',
      'items' => [],
    ], '***');

    self::assertArrayNotHasKey('className', $props);
    self::assertSame([], $props['items']);
    self::assertSame('service-offerings-widget', $props['scrollReveal']['targetId']);
  }

  public function testEditorSampleMirrorsCanonicalFixtureAndPreservesClassName(): void
  {
    $props = eai_service_offerings_get_editor_sample_props([
      'class_name' => 'editor-class',
    ], 'editor-1');

    self::assertSame('editor-class', $props['className']);
    self::assertSame('service-offerings-editor-1', $props['scrollReveal']['targetId']);
    self::assertCount(2, $props['items']);
    self::assertSame('Thiết kế kiến trúc và nội thất công trình dân dụng', $props['items'][0]['title']);
    self::assertStringContainsString('<ul>', $props['items'][0]['descriptionHtml']);
    self::assertSame(
      ['width' => 450, 'height' => 480],
      $props['items'][0]['image']['display_dimensions']
    );
  }
}
