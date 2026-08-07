<?php

declare(strict_types=1);

define('ABSPATH', dirname(__DIR__, 5) . DIRECTORY_SEPARATOR);

function wp_get_attachment_image_src(int $attachment_id, string $size): array|false
{
  if ($attachment_id !== 42) {
    return false;
  }

  return ["https://example.com/logo-{$size}.png", 320, 128];
}

function get_post_meta(int $attachment_id, string $key, bool $single): string
{
  return $attachment_id === 42 && $key === '_wp_attachment_image_alt' && $single
    ? 'Attachment alt'
    : '';
}

function wp_get_attachment_image_srcset(int $attachment_id, string $size): string|false
{
  return $attachment_id === 42 ? "logo-{$size}.png 320w" : false;
}

function wp_get_attachment_image_sizes(int $attachment_id, string $size): string|false
{
  return $attachment_id === 42 && $size !== ''
    ? '(max-width: 320px) 100vw, 320px'
    : false;
}

function sanitize_html_class(string $classname, string $fallback = ''): string
{
  $sanitized = preg_replace('/[^A-Za-z0-9_-]/', '', $classname) ?? '';

  return $sanitized !== '' ? $sanitized : $fallback;
}

function wp_parse_url(string $url): array|false
{
  return parse_url($url);
}

require_once dirname(__DIR__) . '/includes/helpers/media.php';
require_once dirname(__DIR__) . '/includes/helpers/development-partners.php';
require_once dirname(__DIR__) . '/includes/helpers/service-offerings.php';
require_once dirname(__DIR__) . '/includes/helpers/youtube-video-list.php';
require_once dirname(__DIR__) . '/includes/helpers/fields-of-activity.php';
