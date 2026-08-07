<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class DirectorProfileHelperTest extends TestCase
{
  public function testMapsValidRowsAndSkipsInvalidOrEmptyRows(): void
  {
    $items = eai_rc_map_director_profile_items([
      ['title' => ' Giảng viên ', 'description' => ' Kỹ thuật xây dựng '],
      ['title' => ' Chỉ tiêu đề ', 'description' => ' '],
      ['title' => ' ', 'description' => ' Chỉ mô tả '],
      ['title' => ' ', 'description' => ' '],
      'invalid-row',
    ]);

    self::assertSame([
      ['title' => 'Giảng viên', 'description' => 'Kỹ thuật xây dựng'],
      ['title' => 'Chỉ tiêu đề', 'description' => ''],
      ['title' => '', 'description' => 'Chỉ mô tả'],
    ], $items);
  }

  public function testBuildsPropsWithMediaResolutionClassAndUniqueTargetId(): void
  {
    $props = eai_director_profile_get_rc_props([
      'class_name' => ' custom-profile ',
      'background_mobile_image' => ['id' => 42],
      'background_mobile_image_resolution' => 'medium',
      'background_desktop_image' => ['id' => 42],
      'background_desktop_image_resolution' => 'large',
      'subtitle' => ' Giám đốc ',
      'description_html' => ' <p>Hồ sơ</p> ',
      'items' => [],
    ], 'widget @ 42');

    self::assertSame('custom-profile', $props['className']);
    self::assertSame('Giám đốc', $props['subtitle']);
    self::assertSame('<p>Hồ sơ</p>', $props['descriptionHtml']);
    self::assertSame('https://example.com/logo-medium.png', $props['backgroundMobileImage']['url']);
    self::assertSame('https://example.com/logo-large.png', $props['backgroundDesktopImage']['url']);
    self::assertSame(['width' => 320, 'height' => 128], $props['backgroundDesktopImage']['display_dimensions']);
    self::assertSame('director-profile-widget42', $props['scrollReveal']['targetId']);
  }

  public function testUsesSafeFallbackTargetIdAndOmitsEmptyClassName(): void
  {
    $props = eai_director_profile_get_rc_props([
      'class_name' => ' ',
      'items' => [],
    ], '***');

    self::assertArrayNotHasKey('className', $props);
    self::assertSame('director-profile-widget', $props['scrollReveal']['targetId']);
  }

  public function testDetectsWhetherPropsContainRenderableContent(): void
  {
    self::assertFalse(eai_director_profile_has_content([
      'backgroundMobileImage' => ['url' => ' '],
      'backgroundDesktopImage' => ['url' => ''],
      'subtitle' => ' ',
      'descriptionHtml' => ' ',
      'items' => [],
    ]));

    self::assertTrue(eai_director_profile_has_content([
      'backgroundMobileImage' => ['url' => 'https://example.com/bg.jpg'],
      'items' => [],
    ]));
  }

  public function testEditorSampleMirrorsCanonicalFixtureAndPreservesInstanceData(): void
  {
    $props = eai_director_profile_get_editor_sample_props([
      'class_name' => ' editor-profile ',
    ], 'abc-123');

    self::assertSame('editor-profile', $props['className']);
    self::assertSame('director-profile-abc-123', $props['scrollReveal']['targetId']);
    self::assertSame('https://placehold.co/768x1024/152243/ffffff?text=Director+BG+Mobile', $props['backgroundMobileImage']['url']);
    self::assertSame(['width' => 768, 'height' => 1024], $props['backgroundMobileImage']['display_dimensions']);
    self::assertSame(['width' => 1920, 'height' => 1080], $props['backgroundDesktopImage']['display_dimensions']);
    self::assertSame('100vw', $props['backgroundDesktopImage']['sizes']);
    self::assertSame('GIÁM ĐỐC - TS. NGUYỄN ĐĂNG HẠNH', $props['subtitle']);
    self::assertCount(5, $props['items']);
    self::assertSame('Giảng viên', $props['items'][0]['title']);
  }

  public function testWidgetUsesUniqueIdEditorSampleEmptyAndPopulatedBranches(): void
  {
    $source = file_get_contents(dirname(__DIR__) . '/includes/widgets/EAI-director-profile.php');

    self::assertIsString($source);
    self::assertStringContainsString("eai_director_profile_get_rc_props(\$this->get_settings_for_display(), \$this->get_id())", $source);
    self::assertStringContainsString('eai_is_elementor_edit_mode() && ! eai_director_profile_has_content($props)', $source);
    self::assertStringContainsString('eai_director_profile_get_editor_sample_props($settings, $this->get_id())', $source);
    self::assertStringContainsString("if (! eai_director_profile_has_content(\$props))", $source);
    self::assertStringContainsString("'empty' => true", $source);
    self::assertSame(2, substr_count($source, "eai_rc_render_html('DirectorProfile', \$props)"));
    self::assertStringNotContainsString("'scroll_reveal_target_id'", $source);
  }

  public function testTemplateEchoesRawSsrHtmlAndHandlesEmptyAndErrors(): void
  {
    $source = file_get_contents(dirname(__DIR__) . '/includes/templates/EAI-director-profile.php');

    self::assertIsString($source);
    self::assertStringContainsString("if (! empty(\$args['empty']))", $source);
    self::assertStringContainsString('eai_rc_render_error_message($error)', $source);
    self::assertStringContainsString('echo $html;', $source);
    self::assertStringNotContainsString('wp_kses_post', $source);
    self::assertStringContainsString('ảnh nền hoặc nội dung', $source);
  }

  public function testEditorCssDisablesDirectorProfileAnimationWithoutAffectingFrontendBundle(): void
  {
    $source = file_get_contents(dirname(__DIR__) . '/assets/css/eai-elementor-editor.css');

    self::assertIsString($source);
    self::assertStringContainsString('div .director-profile-subtitle', $source);
    self::assertStringContainsString('div .director-profile-description', $source);
    self::assertStringContainsString('div .director-profile-item', $source);
    self::assertStringContainsString('opacity: 1 !important;', $source);
    self::assertStringContainsString('transform: none !important;', $source);
    self::assertStringContainsString('translate: none !important;', $source);
    self::assertStringContainsString('transition: none !important;', $source);
    self::assertStringContainsString('transition-delay: 0s !important;', $source);
  }
}
