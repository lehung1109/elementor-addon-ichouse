<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

if (! defined('EAI_PATH')) {
  define('EAI_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);
}

if (! defined('EAI_URL')) {
  define('EAI_URL', 'https://example.com/wp-content/plugins/elementor-addon-ichouse/');
}

if (! function_exists('eai_rc_get_bundle_version')) {
  function eai_rc_get_bundle_version(): ?string
  {
    return $GLOBALS['eai_test_bundle_version'] ?? null;
  }
}

require_once dirname(__DIR__) . '/includes/helpers/frontend-assets.php';

final class FrontendAssetsHelperTest extends TestCase
{
  protected function setUp(): void
  {
    $GLOBALS['eai_test_bundle_version'] = 'bundle-version';
  }

  protected function tearDown(): void
  {
    unset($GLOBALS['eai_test_bundle_version']);
  }

  public function testCacheBustsDynamicElementorPostStyles(): void
  {
    $src = 'https://example.com/wp-content/uploads/elementor/css/post-5178.css?ver=old';

    self::assertSame(
      'https://example.com/wp-content/uploads/elementor/css/post-5178.css?ver=bundle-version',
      eai_filter_porto_generated_styles_src($src, 'elementor-post-5178')
    );
  }

  public function testCacheBustsDifferentNumericElementorPostIds(): void
  {
    $src = 'https://example.com/wp-content/uploads/elementor/css/post-42.css?ver=old';

    self::assertSame(
      'https://example.com/wp-content/uploads/elementor/css/post-42.css?ver=bundle-version',
      eai_filter_porto_generated_styles_src($src, 'elementor-post-42')
    );
  }

  public function testDoesNotCacheBustNonNumericElementorPostHandle(): void
  {
    $src = 'https://example.com/wp-content/uploads/elementor/css/post-global.css?ver=old';

    self::assertSame(
      $src,
      eai_filter_porto_generated_styles_src($src, 'elementor-post-global')
    );
  }

  public function testStillCacheBustsPortoGeneratedStyles(): void
  {
    $src = 'https://example.com/wp-content/uploads/porto_styles/theme.css?ver=old';

    self::assertSame(
      'https://example.com/wp-content/uploads/porto_styles/theme.css?ver=bundle-version',
      eai_filter_porto_generated_styles_src($src, 'porto-theme')
    );
  }

  public function testDoesNotChangeUnrelatedStyles(): void
  {
    $src = 'https://example.com/wp-content/themes/theme/style.css?ver=old';

    self::assertSame(
      $src,
      eai_filter_porto_generated_styles_src($src, 'theme-style')
    );
  }

  public function testDoesNotChangeGeneratedStylesWithoutBundleVersion(): void
  {
    $GLOBALS['eai_test_bundle_version'] = null;
    $src = 'https://example.com/wp-content/uploads/elementor/css/post-5178.css?ver=old';

    self::assertSame(
      $src,
      eai_filter_porto_generated_styles_src($src, 'elementor-post-5178')
    );
  }
}
