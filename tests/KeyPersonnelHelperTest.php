<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class KeyPersonnelHelperTest extends TestCase
{
  public function testMapsValidRowsAndSkipsRowsMissingRequiredContent(): void
  {
    $items = eai_rc_map_key_personnel_items([
      [
        'title' => '  KS. Lưu Hoàng Nga  ',
        'description_html' => '<ul><li>Kinh nghiệm</li></ul>',
        'image' => ['url' => 'https://example.com/nga.png', 'alt' => 'Nga'],
      ],
      ['title' => '   ', 'image' => ['url' => 'https://example.com/no-title.png']],
      ['title' => 'Không ảnh', 'image' => ['url' => '']],
      'invalid-row',
    ]);

    self::assertCount(1, $items);
    self::assertSame('KS. Lưu Hoàng Nga', $items[0]['title']);
    self::assertSame('<ul><li>Kinh nghiệm</li></ul>', $items[0]['descriptionHtml']);
    self::assertSame('https://example.com/nga.png', $items[0]['image']['url']);
  }

  public function testUsesSelectedResolutionForAttachmentMedia(): void
  {
    $items = eai_rc_map_key_personnel_items([[
      'title' => 'KS. Nguyễn Văn Hùng',
      'description_html' => '',
      'image' => ['id' => 42],
      'image_resolution' => 'large',
    ]]);

    self::assertSame('https://example.com/logo-large.png', $items[0]['image']['url']);
    self::assertSame('Attachment alt', $items[0]['image']['alt']);
    self::assertSame(['width' => 320, 'height' => 128], $items[0]['image']['display_dimensions']);
    self::assertSame('logo-large.png 320w', $items[0]['image']['srcSet']);
    self::assertSame('(max-width: 320px) 100vw, 320px', $items[0]['image']['sizes']);
  }

  public function testMapsLinkOnlyWhenUrlAndLabelAreBothPresent(): void
  {
    $items = eai_rc_map_key_personnel_items([
      [
        'title' => 'Có link',
        'description_html' => '',
        'image' => ['url' => 'https://example.com/linked.png'],
        'link' => ['url' => ' https://example.com/profile ', 'is_external' => true, 'nofollow' => true],
        'link_label' => ' Xem hồ sơ ',
      ],
      [
        'title' => 'Thiếu label',
        'description_html' => '',
        'image' => ['url' => 'https://example.com/no-label.png'],
        'link' => ['url' => 'https://example.com/profile'],
        'link_label' => ' ',
      ],
      [
        'title' => 'Thiếu URL',
        'description_html' => '',
        'image' => ['url' => 'https://example.com/no-url.png'],
        'link' => ['url' => ' '],
        'link_label' => 'Xem hồ sơ',
      ],
    ]);

    self::assertSame([
      'url' => 'https://example.com/profile',
      'is_external' => true,
      'nofollow' => true,
    ], $items[0]['link']);
    self::assertSame('Xem hồ sơ', $items[0]['linkLabel']);
    self::assertArrayNotHasKey('link', $items[1]);
    self::assertArrayNotHasKey('linkLabel', $items[1]);
    self::assertArrayNotHasKey('link', $items[2]);
    self::assertArrayNotHasKey('linkLabel', $items[2]);
  }

  public function testBuildsPropsWithOptionalClassNameAndTrimmedTitle(): void
  {
    $props = eai_key_personnel_get_rc_props([
      'class_name' => ' custom-personnel ',
      'title' => ' Đội ngũ ',
      'items' => [],
    ]);

    self::assertSame('custom-personnel', $props['className']);
    self::assertSame('Đội ngũ', $props['title']);
    self::assertSame([], $props['items']);

    $withoutClass = eai_key_personnel_get_rc_props([
      'class_name' => '   ',
      'title' => ' ',
      'items' => [],
    ]);

    self::assertArrayNotHasKey('className', $withoutClass);
    self::assertSame('', $withoutClass['title']);
  }

  public function testEditorSampleMirrorsCanonicalFixtureAndPreservesClassName(): void
  {
    $props = eai_key_personnel_get_editor_sample_props([
      'class_name' => ' editor-personnel ',
      'title' => '',
    ]);

    self::assertSame('editor-personnel', $props['className']);
    self::assertSame('ĐỘI NGŨ NHÂN SỰ CHỦ CHỐT', $props['title']);
    self::assertCount(5, $props['items']);
    self::assertSame('KS. Lưu Hoàng Nga', $props['items'][0]['title']);
    self::assertSame(['width' => 480, 'height' => 600], $props['items'][0]['image']['display_dimensions']);
    self::assertSame('Xem chi tiết', $props['items'][0]['linkLabel']);
    self::assertArrayNotHasKey('link', $props['items'][2]);
    self::assertArrayNotHasKey('linkLabel', $props['items'][2]);
  }

  public function testWidgetUsesWrapperForEditorSampleAndPopulatedRenderBranches(): void
  {
    $source = file_get_contents(dirname(__DIR__) . '/includes/widgets/EAI-key-personnel.php');

    self::assertIsString($source);
    self::assertSame(2, substr_count($source, "eai_rc_render_html('KeyPersonnelWrapper', \$props)"));
    self::assertStringContainsString('eai_is_elementor_edit_mode() && empty($props[\'items\'])', $source);
    self::assertStringContainsString("if (empty(\$props['items']))", $source);
    self::assertStringContainsString("'empty' => true", $source);
  }

  public function testTemplateEchoesRawSsrHtmlAndHandlesEmptyAndErrors(): void
  {
    $source = file_get_contents(dirname(__DIR__) . '/includes/templates/EAI-key-personnel.php');

    self::assertIsString($source);
    self::assertStringContainsString("if (! empty(\$args['empty']))", $source);
    self::assertStringContainsString('eai_rc_render_error_message($error)', $source);
    self::assertStringContainsString('echo $html;', $source);
    self::assertStringNotContainsString('wp_kses_post', $source);
  }
}
