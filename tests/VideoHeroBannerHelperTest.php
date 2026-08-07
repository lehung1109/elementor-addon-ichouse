<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class VideoHeroBannerHelperTest extends TestCase
{
  public function testMapsCompleteSettingsToWrapperProps(): void
  {
    $props = eai_video_hero_banner_get_rc_props([
      'class_name' => ' custom-hero ',
      'title' => ' ICHouse Interior ',
      'description' => ' Không gian sống tinh tế ',
      'video' => ['url' => ' https://example.com/hero.mp4 '],
      'poster' => ['id' => 42],
      'poster_resolution' => 'medium',
      'mobile_aspect_ratio' => 'yes',
    ]);

    self::assertSame('https://example.com/hero.mp4', $props['url']);
    self::assertSame('https://example.com/logo-medium.png', $props['poster']['url']);
    self::assertSame(['width' => 320, 'height' => 128], $props['poster']['display_dimensions']);
    self::assertSame('logo-medium.png 320w', $props['poster']['srcSet']);
    self::assertSame('custom-hero', $props['className']);
    self::assertSame('ICHouse Interior', $props['title']);
    self::assertSame('Không gian sống tinh tế', $props['description']);
    self::assertTrue($props['mobileAspectRatio']);
  }

  public function testOmitsBlankOptionalStringsForPosterOnlyBanner(): void
  {
    $props = eai_video_hero_banner_get_rc_props([
      'class_name' => '   ',
      'title' => " \t ",
      'description' => " \n ",
      'video' => ['url' => '   '],
      'poster' => ['url' => 'https://example.com/poster.jpg'],
      'poster_resolution' => 'large',
    ]);

    self::assertArrayNotHasKey('url', $props);
    self::assertArrayNotHasKey('className', $props);
    self::assertArrayNotHasKey('title', $props);
    self::assertArrayNotHasKey('description', $props);
    self::assertSame('https://example.com/poster.jpg', $props['poster']['url']);
    self::assertFalse($props['mobileAspectRatio']);
  }
}
