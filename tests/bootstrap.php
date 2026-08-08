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

function sanitize_key(string $value): string
{
  return strtolower(preg_replace('/[^a-zA-Z0-9_-]/', '', $value) ?? '');
}

function sanitize_title(string $value): string
{
  $value = strtolower(trim($value));
  return trim(preg_replace('/[^a-z0-9]+/', '-', $value) ?? '', '-');
}

function sanitize_text_field(string $value): string
{
  return trim(strip_tags($value));
}

function get_post_thumbnail_id(object|int $post): int
{
  $id = is_object($post) ? (int) $post->ID : $post;
  return $id === 7 ? 42 : 0;
}

function get_the_title(object $post): string
{
  return (string) $post->post_title;
}

function get_the_excerpt(object $post): string
{
  return (string) $post->post_excerpt;
}

function get_permalink(object $post): string
{
  return 'https://example.com/project-' . $post->ID;
}

function rest_url(string $path): string
{
  return '/wp-json/' . ltrim($path, '/');
}

function add_query_arg(array $args, string $url): string
{
  return $url . '?' . http_build_query($args);
}

function get_post_type_object(string $post_type): object
{
  return (object) [
    'public' => in_array($post_type, ['post', 'job'], true),
    'labels' => (object) ['singular_name' => $post_type === 'job' ? 'Jobs' : ucfirst($post_type)],
  ];
}

function taxonomy_exists(string $taxonomy): bool
{
  return in_array($taxonomy, ['job_type', 'private_job_type'], true);
}

function get_taxonomy(string $taxonomy): object|false
{
  if (! taxonomy_exists($taxonomy)) {
    return false;
  }

  return (object) ['public' => $taxonomy === 'job_type'];
}

function is_object_in_taxonomy(string $post_type, string $taxonomy): bool
{
  return $post_type === 'job' && in_array($taxonomy, ['job_type', 'private_job_type'], true);
}

function get_terms(array $args): array
{
  $valid_slugs = ['full-time', 'remote'];
  $requested = array_values(array_intersect((array) ($args['slug'] ?? []), $valid_slugs));

  return array_map(static fn (string $slug): object => (object) ['slug' => $slug], $requested);
}

function get_the_terms(object|int $post, string $taxonomy): array|false
{
  $post_id = is_object($post) ? (int) $post->ID : $post;
  if ($post_id !== 7 || $taxonomy !== 'job_type') {
    return false;
  }

  return [(object) ['name' => 'Toàn thời gian'], (object) ['name' => 'Làm từ xa']];
}

function get_field_object(string $key, int|false $post_id = false, bool $format_value = true, bool $load_value = true): array|false
{
  $fields = [
    'field_location' => ['key' => 'field_location', 'type' => 'select', 'choices' => ['hanoi' => 'Hà Nội'], 'value' => 'hanoi'],
    'field_location_text' => ['key' => 'field_location_text', 'type' => 'text', 'value' => 'Đà Nẵng'],
    'field_location_textarea' => ['key' => 'field_location_textarea', 'type' => 'textarea', 'value' => 'Văn phòng Hồ Chí Minh'],
    'field_location_radio' => ['key' => 'field_location_radio', 'type' => 'radio', 'choices' => ['remote' => 'Làm việc từ xa'], 'value' => 'remote'],
    'field_location_empty' => ['key' => 'field_location_empty', 'type' => 'text', 'value' => ''],
    'field_expiration' => ['key' => 'field_expiration', 'type' => 'date_picker', 'value' => '20260807'],
    'field_expiration_today' => ['key' => 'field_expiration_today', 'type' => 'date_picker', 'value' => '20260808'],
    'field_expiration_future' => ['key' => 'field_expiration_future', 'type' => 'date_picker', 'value' => '20260809'],
    'field_expiration_invalid' => ['key' => 'field_expiration_invalid', 'type' => 'date_picker', 'value' => 'khong-phai-ngay'],
    'field_expiration_empty' => ['key' => 'field_expiration_empty', 'type' => 'date_picker', 'value' => ''],
    'field_wrong_type' => ['key' => 'field_wrong_type', 'type' => 'number', 'value' => '123'],
  ];

  return $post_id === 7 && isset($fields[$key]) ? $fields[$key] : false;
}

function wp_timezone(): DateTimeZone
{
  return new DateTimeZone('Asia/Ho_Chi_Minh');
}

function current_time(string $type): string
{
  return $type === 'Ymd' ? '20260808' : '2026-08-08 12:00:00';
}

function is_wp_error(mixed $value): bool
{
  return false;
}

function wp_unslash(string $value): string
{
  return $value;
}

require_once dirname(__DIR__) . '/includes/helpers/media.php';
require_once dirname(__DIR__) . '/includes/helpers/project-category-gallery.php';
$job_listing_helper = dirname(__DIR__) . '/includes/helpers/job-listing-list.php';
if (file_exists($job_listing_helper)) {
  require_once $job_listing_helper;
}
require_once dirname(__DIR__) . '/includes/helpers/development-partners.php';
require_once dirname(__DIR__) . '/includes/helpers/outstanding-advantages.php';
require_once dirname(__DIR__) . '/includes/helpers/collaboration-intro.php';
require_once dirname(__DIR__) . '/includes/helpers/service-offerings.php';
require_once dirname(__DIR__) . '/includes/helpers/director-profile.php';
require_once dirname(__DIR__) . '/includes/helpers/key-personnel.php';
require_once dirname(__DIR__) . '/includes/helpers/youtube-video-list.php';
require_once dirname(__DIR__) . '/includes/helpers/video-hero-banner.php';
require_once dirname(__DIR__) . '/includes/helpers/fields-of-activity.php';
