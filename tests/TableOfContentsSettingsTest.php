<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class TableOfContentsSettingsTest extends TestCase
{
  protected function setUp(): void
  {
    $GLOBALS['eai_test_options'] = [];
  }

  public function testRelatedPostsAreEnabledByDefault(): void
  {
    self::assertTrue(eai_toc_get_default_settings()['related_posts_enabled']);
    self::assertTrue(eai_toc_get_settings()['related_posts_enabled']);
  }

  public function testSanitizesRelatedPostsToggle(): void
  {
    $disabled = eai_toc_sanitize_settings([
      'title' => 'Mục lục',
      'enabled_post_types' => ['post'],
      'min_headings' => 2,
    ]);
    $enabled = eai_toc_sanitize_settings([
      'title' => 'Mục lục',
      'enabled_post_types' => ['post'],
      'min_headings' => 2,
      'related_posts_enabled' => '1',
    ]);

    self::assertFalse($disabled['related_posts_enabled']);
    self::assertTrue($enabled['related_posts_enabled']);
  }

  public function testFilterGuardsRelatedPostsQueryWithSetting(): void
  {
    $filter = file_get_contents(dirname(__DIR__) . '/includes/table-of-contents.php');

    self::assertIsString($filter);
    self::assertStringContainsString("if (\$settings['related_posts_enabled'])", $filter);
  }

  public function testSettingsPageIncludesRelatedPostsToggle(): void
  {
    $page = file_get_contents(dirname(__DIR__) . '/includes/admin/toc-settings.php');

    self::assertIsString($page);
    self::assertStringContainsString('eai_toc_related_posts_enabled', $page);
    self::assertStringContainsString('eai_toc_settings[related_posts_enabled]', $page);
  }
}
