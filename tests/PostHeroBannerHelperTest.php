<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class PostHeroBannerHelperTest extends TestCase
{
  public function testMapsPostContextAcfImageAndManualBreadcrumbsToProps(): void
  {
    $props = eai_post_hero_banner_get_rc_props(7, [
      'acf_image_field' => 'field_hero_image',
      'image_size' => 'medium',
      'breadcrumb_items' => [
        [
          'label' => ' Trang chủ ',
          'link' => [
            'url' => 'https://example.com/',
            'is_external' => true,
            'nofollow' => true,
          ],
        ],
        [
          'label' => ' Dự án ',
          'link' => ['url' => 'https://example.com/project/'],
        ],
      ],
      'title_heading' => 'h2',
      'class_name' => ' custom-hero ',
    ]);

    self::assertSame('https://example.com/logo-medium.png', $props['backgroundImage']['url']);
    self::assertSame('Biệt thự mẫu', $props['title']);
    self::assertSame('h2', $props['titleHeading']);
    self::assertSame('custom-hero', $props['className']);
    self::assertSame('Trang chủ', $props['breadcrumbItems'][0]['label']);
    self::assertSame([
      'url' => 'https://example.com/',
      'is_external' => true,
      'nofollow' => true,
    ], $props['breadcrumbItems'][0]['link']);
    self::assertSame('Dự án', $props['breadcrumbItems'][1]['label']);
    self::assertSame('https://example.com/project/', $props['breadcrumbItems'][1]['link']['url']);
  }

  public function testNormalizesSupportedAcfImageFormats(): void
  {
    self::assertSame(['id' => 42], eai_post_hero_banner_normalize_acf_image(42));
    self::assertSame(['id' => 42], eai_post_hero_banner_normalize_acf_image(['ID' => 42]));
    self::assertSame(['url' => 'https://example.com/hero.jpg'], eai_post_hero_banner_normalize_acf_image(' https://example.com/hero.jpg '));
  }

  public function testFiltersInvalidManualBreadcrumbsAndLimitsToTwoItems(): void
  {
    $props = eai_post_hero_banner_get_rc_props(7, [
      'acf_image_field' => 'field_hero_image',
      'breadcrumb_items' => [
        ['label' => '', 'link' => ['url' => 'https://example.com/ignored-label/']],
        ['label' => 'Thiếu URL', 'link' => ['url' => '']],
        ['label' => ' Mục một ', 'link' => ['url' => 'https://example.com/one/']],
        ['label' => 'Mục hai', 'link' => ['url' => 'https://example.com/two/']],
        ['label' => 'Mục ba', 'link' => ['url' => 'https://example.com/three/']],
        'invalid-row',
      ],
    ]);

    self::assertSame(['Mục một', 'Mục hai'], array_column($props['breadcrumbItems'], 'label'));
  }

  public function testAllowsEmptyManualBreadcrumbWhenBackgroundExists(): void
  {
    $props = eai_post_hero_banner_get_rc_props(7, [
      'acf_image_field' => 'field_hero_image',
      'breadcrumb_items' => [],
    ]);

    self::assertFalse(eai_post_hero_banner_props_are_empty($props));
    self::assertSame([], $props['breadcrumbItems']);
  }

  public function testTreatsMissingBackgroundAsEmpty(): void
  {
    self::assertTrue(eai_post_hero_banner_props_are_empty(eai_post_hero_banner_get_rc_props(8, [
      'acf_image_field' => 'field_hero_image',
    ])));
  }

  public function testMapsResponsiveBackgroundImageMetadata(): void
  {
    $props = eai_post_hero_banner_get_rc_props(7, [
      'acf_image_field' => 'field_hero_image',
      'image_size' => 'large',
    ]);

    self::assertSame('logo-large.png 320w', $props['backgroundImage']['srcSet']);
    self::assertSame('100vw', $props['backgroundImage']['sizes']);
  }

  public function testBuildsCompleteEditorSample(): void
  {
    $props = eai_post_hero_banner_get_editor_sample_props(['class_name' => ' sample ']);

    self::assertFalse(eai_post_hero_banner_props_are_empty($props));
    self::assertSame('sample', $props['className']);
    self::assertCount(2, $props['breadcrumbItems']);
  }

  public function testWidgetRegistrationAndRenderConventions(): void
  {
    $plugin_dir = dirname(__DIR__);
    $widget = file_get_contents($plugin_dir . '/includes/widgets/EAI-post-hero-banner.php');
    $template = file_get_contents($plugin_dir . '/includes/templates/EAI-post-hero-banner.php');
    $registration = file_get_contents($plugin_dir . '/includes/plugin.php');

    self::assertIsString($widget);
    self::assertStringContainsString("eai_rc_render_html('PostHeroBanner'", $widget);
    self::assertStringContainsString('eai_is_elementor_edit_mode()', $widget);
    self::assertStringContainsString('eai_post_hero_banner_props_are_empty', $widget);
    self::assertStringContainsString('get_queried_object_id()', $widget);
    self::assertIsString($template);
    self::assertStringContainsString('echo $html;', $template);
    self::assertStringNotContainsString('wp_kses_post', $template);
    self::assertIsString($registration);
    self::assertStringContainsString("widgets/EAI-post-hero-banner.php", $registration);
    self::assertStringContainsString('new \\EAI_Post_Hero_Banner_Widget()', $registration);
  }
}
