<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__) . '/includes/helpers/collaboration-intro.php';

final class CollaborationIntroHelperTest extends TestCase
{
  public function testMapsCompleteSettingsToReactProps(): void
  {
    $props = eai_collaboration_intro_get_rc_props([
      'class_name' => 'custom-collaboration',
      'background_image' => ['id' => 42],
      'background_image_resolution' => 'large',
      'subtitle' => 'Định hướng hợp tác',
      'title_html' => 'Nội dung <span>nổi bật</span>',
      'image' => ['id' => 42],
      'image_resolution' => 'medium',
      'bottom_title' => 'Lĩnh vực hợp tác',
      'items' => [[
        'image' => ['id' => 42],
        'image_resolution' => 'thumbnail',
        'image_alt' => 'Thiết kế',
        'title' => 'Thiết kế kiến trúc',
      ]],
      'note' => 'Vui lòng tham khảo tiêu chí.',
      'button_label' => 'Trở thành đối tác',
      'button_link' => [
        'url' => 'https://example.com/hop-tac',
        'is_external' => 'on',
        'nofollow' => 'on',
      ],
      'popup_target' => ' Tu Van ',
    ], 'abc-123');

    self::assertSame('custom-collaboration', $props['className']);
    self::assertSame('https://example.com/logo-large.png', $props['backgroundImage']['url']);
    self::assertSame('logo-large.png 320w', $props['backgroundImage']['srcSet']);
    self::assertSame(['width' => 320, 'height' => 128], $props['backgroundImage']['display_dimensions']);
    self::assertSame('https://example.com/logo-medium.png', $props['image']['url']);
    self::assertSame('Nội dung <span>nổi bật</span>', $props['titleHtml']);
    self::assertSame('Thiết kế', $props['items'][0]['image']['alt']);
    self::assertSame('Thiết kế kiến trúc', $props['items'][0]['title']);
    self::assertSame('https://example.com/hop-tac', $props['buttonLink']['url']);
    self::assertTrue($props['buttonLink']['is_external']);
    self::assertTrue($props['buttonLink']['nofollow']);
    self::assertSame('tu-van', $props['popupTarget']);
    self::assertSame('collaboration-intro-abc-123', $props['scrollReveal']['targetId']);
  }

  public function testFiltersInvalidItemsAndOmitsBlankClassName(): void
  {
    $props = eai_collaboration_intro_get_rc_props([
      'class_name' => '   ',
      'items' => [
        'invalid',
        ['image' => ['url' => ''], 'title' => 'Không có ảnh'],
        ['image' => ['url' => 'https://example.com/icon.png'], 'title' => '   '],
        ['image' => ['url' => 'https://example.com/icon.png'], 'title' => 'Hợp lệ'],
      ],
    ], '***');

    self::assertArrayNotHasKey('className', $props);
    self::assertCount(1, $props['items']);
    self::assertSame('Hợp lệ', $props['items'][0]['title']);
    self::assertSame('collaboration-intro-widget', $props['scrollReveal']['targetId']);
  }

  public function testDetectsOnlyCompletelyEmptyProps(): void
  {
    $empty = eai_collaboration_intro_get_rc_props([], 'empty');
    $with_note = eai_collaboration_intro_get_rc_props(['note' => 'Còn nội dung'], 'note');
    $with_background = eai_collaboration_intro_get_rc_props([
      'background_image' => ['url' => 'https://example.com/background.jpg'],
    ], 'background');

    self::assertTrue(eai_collaboration_intro_props_are_empty($empty));
    self::assertFalse(eai_collaboration_intro_props_are_empty($with_note));
    self::assertFalse(eai_collaboration_intro_props_are_empty($with_background));
  }

  public function testEditorSampleMirrorsCanonicalFixtureAndPreservesInstanceData(): void
  {
    $props = eai_collaboration_intro_get_editor_sample_props([
      'class_name' => 'editor-class',
    ], 'editor-1');

    self::assertSame('editor-class', $props['className']);
    self::assertSame('collaboration-intro-editor-1', $props['scrollReveal']['targetId']);
    self::assertSame('GIỚI THIỆU & ĐỊNH HƯỚNG HỢP TÁC', $props['subtitle']);
    self::assertStringContainsString('text-brand-gold', $props['titleHtml']);
    self::assertCount(5, $props['items']);
    self::assertSame('Thiết kế kiến trúc', $props['items'][0]['title']);
    self::assertSame(['width' => 1920, 'height' => 1080], $props['backgroundImage']['display_dimensions']);
    self::assertSame('TRỞ THÀNH ĐỐI TÁC ICHOUSE!', $props['buttonLabel']);
    self::assertSame('tu-van', $props['popupTarget']);
  }

  public function testOmitsPopupTargetWhenSettingIsBlank(): void
  {
    $props = eai_collaboration_intro_get_rc_props([
      'button_label' => 'Trở thành đối tác',
      'button_link' => ['url' => 'https://example.com/hop-tac'],
      'popup_target' => '   ',
    ], 'blank-popup');

    self::assertArrayNotHasKey('popupTarget', $props);
  }

  public function testPopupTargetAloneMakesPropsNonEmpty(): void
  {
    $props = eai_collaboration_intro_get_rc_props([
      'button_label' => 'Trở thành đối tác',
      'popup_target' => 'tu-van',
    ], 'popup-only');

    self::assertFalse(eai_collaboration_intro_props_are_empty($props));
    self::assertSame('tu-van', $props['popupTarget']);
  }

  public function testWidgetUsesServerComponentAndElementorEditorModeHelper(): void
  {
    $widget = file_get_contents(dirname(__DIR__) . '/includes/widgets/EAI-collaboration-intro.php');

    self::assertIsString($widget);
    self::assertStringContainsString("eai_rc_render_html('CollaborationIntro', \$props)", $widget);
    self::assertStringContainsString('eai_is_elementor_edit_mode()', $widget);
    self::assertStringNotContainsString("eai_rc_render_html('CollaborationIntroWrapper'", $widget);
  }
}
