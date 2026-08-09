<?php

declare(strict_types=1);

define('ABSPATH', dirname(__DIR__, 5) . DIRECTORY_SEPARATOR);

final class WP_Term
{
  public function __construct(
    public string $name,
    public string $slug,
    public int $parent = 0
  ) {}
}

final class WP_Post
{
  public function __construct(
    public int $ID,
    public string $post_title,
    public string $post_excerpt = '',
    public string $post_type = 'project',
    public string $post_status = 'publish'
  ) {}
}

final class WP_REST_Server
{
  public const CREATABLE = 'POST';
}

final class WP_REST_Request
{
  public function __construct(private array $params = []) {}

  public function get_param(string $key): mixed
  {
    return $this->params[$key] ?? null;
  }

  public function get_json_params(): array
  {
    return [];
  }
}

function add_action(string $hook_name, callable|string $callback): void
{
}

function register_rest_route(string $namespace, string $route, array $args): void
{
}

function apply_filters(string $hook_name, mixed $value, mixed ...$args): mixed
{
  return $value;
}

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

function eai_test_posts(): array
{
  return [
    7 => new WP_Post(7, 'Biệt thự mẫu', 'Excerpt không được dùng'),
    8 => new WP_Post(8, 'Không ảnh', 'Excerpt cũ'),
    9 => new WP_Post(9, 'Bài nháp', '', 'project', 'draft'),
    10 => new WP_Post(10, 'Tin tức', '', 'post'),
    11 => new WP_Post(11, 'Dự án thứ hai'),
    12 => new WP_Post(12, 'Dự án thứ ba'),
    13 => new WP_Post(13, 'Dự án thứ tư'),
    14 => new WP_Post(14, 'Thiếu permalink'),
  ];
}

function get_post(int $post_id): ?WP_Post
{
  return eai_test_posts()[$post_id] ?? null;
}

function get_posts(array $args = []): array
{
  $posts = array_values(eai_test_posts());
  $post_types = array_map('strval', (array) ($args['post_type'] ?? ['post']));
  $post_status = (string) ($args['post_status'] ?? 'publish');
  $post_ids = array_values(array_filter(array_map('intval', (array) ($args['post__in'] ?? []))));

  $posts = array_values(array_filter(
    $posts,
    static fn(WP_Post $post): bool => in_array($post->post_type, $post_types, true)
      && ($post_status === '' || $post->post_status === $post_status)
      && (empty($post_ids) || in_array($post->ID, $post_ids, true))
  ));

  if (($args['orderby'] ?? '') === 'post__in') {
    $positions = array_flip($post_ids);
    usort($posts, static fn(WP_Post $a, WP_Post $b): int => ($positions[$a->ID] ?? PHP_INT_MAX) <=> ($positions[$b->ID] ?? PHP_INT_MAX));
  }

  if (($args['fields'] ?? '') === 'ids') {
    return array_map(static fn(WP_Post $post): int => $post->ID, $posts);
  }

  return $posts;
}

function get_post_thumbnail_id(object|int $post): int
{
  $id = is_object($post) ? (int) $post->ID : $post;
  return in_array($id, [7, 11, 12, 13, 14], true) ? 42 : 0;
}

function get_the_title(object|int $post): string
{
  if (is_int($post)) {
    $post = get_post($post);
  }

  return is_object($post) ? (string) ($post->post_title ?? '') : '';
}

function get_the_excerpt(object $post): string
{
  return (string) $post->post_excerpt;
}

function get_post_type(object|int $post): string|false
{
  if (is_object($post)) {
    return (string) ($post->post_type ?? 'project');
  }

  return get_post($post)?->post_type ?: false;
}

function get_post_status(object|int $post): string|false
{
  if (is_object($post)) {
    return (string) ($post->post_status ?? 'publish');
  }

  return get_post($post)?->post_status ?: false;
}

function get_the_modified_date(string $format = '', object|int|null $post = null): string
{
  return '22/07/2026';
}

function get_permalink(object|int $post): string
{
  $post_id = is_object($post) ? (int) $post->ID : $post;
  return $post_id === 14 ? '' : 'https://example.com/project-' . $post_id;
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
    'public' => in_array($post_type, ['post', 'job', 'project'], true),
    'labels' => (object) ['singular_name' => $post_type === 'job' ? 'Jobs' : ucfirst($post_type)],
    'cap' => (object) ['edit_posts' => $post_type === 'project' ? 'edit_projects' : 'edit_posts'],
  ];
}

function taxonomy_exists(string $taxonomy): bool
{
  return in_array($taxonomy, [
    'job_type', 'private_job_type', 'project-category', 'project-investor',
    'project-model', 'private-project-taxonomy', 'wrong-post-taxonomy', 'category',
  ], true);
}

function get_taxonomy(string $taxonomy): object|false
{
  if (! taxonomy_exists($taxonomy)) {
    return false;
  }

  return (object) ['public' => ! in_array($taxonomy, [
    'private_job_type', 'private-project-taxonomy', 'wrong-post-taxonomy',
  ], true)];
}

function is_object_in_taxonomy(string $post_type, string $taxonomy): bool
{
  if ($post_type === 'job') {
    return in_array($taxonomy, ['job_type', 'private_job_type'], true);
  }

  return $post_type === 'project' && in_array($taxonomy, [
    'project-category', 'project-investor', 'project-model',
    'private-project-taxonomy', 'category',
  ], true);
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
  if ($post_id !== 7) {
    return false;
  }

  return match ($taxonomy) {
    'job_type' => [(object) ['name' => 'Toàn thời gian'], (object) ['name' => 'Làm từ xa']],
    'project-category' => [new WP_Term('Villa', 'villa')],
    'project-investor' => [
      new WP_Term('Nhà đầu tư mẹ', 'parent-investor'),
      new WP_Term('ICHouse', 'ichouse', 10),
    ],
    'project-model' => [
      new WP_Term('Nhà ở', 'nha-o'),
      new WP_Term('Nghỉ dưỡng', 'nghi-duong', 20),
    ],
    'private-project-taxonomy' => [new WP_Term('Nội bộ', 'noi-bo')],
    'category' => [new WP_Term('Không phân loại', 'uncategorized')],
    default => false,
  };
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
require_once dirname(__DIR__) . '/includes/helpers/related-posts.php';
require_once dirname(__DIR__) . '/includes/helpers/project-category-gallery.php';
require_once dirname(__DIR__) . '/includes/helpers/featured-projects.php';
$job_listing_helper = dirname(__DIR__) . '/includes/helpers/job-listing-list.php';
if (file_exists($job_listing_helper)) {
  require_once $job_listing_helper;
}
$news_list_helper = dirname(__DIR__) . '/includes/helpers/news-list.php';
if (file_exists($news_list_helper)) {
  require_once $news_list_helper;
}
require_once dirname(__DIR__) . '/includes/helpers/development-partners.php';
require_once dirname(__DIR__) . '/includes/helpers/outstanding-advantages.php';
require_once dirname(__DIR__) . '/includes/helpers/collaboration-intro.php';
require_once dirname(__DIR__) . '/includes/helpers/contact-popup.php';
require_once dirname(__DIR__) . '/includes/helpers/service-offerings.php';
require_once dirname(__DIR__) . '/includes/helpers/director-profile.php';
require_once dirname(__DIR__) . '/includes/helpers/key-personnel.php';
require_once dirname(__DIR__) . '/includes/helpers/youtube-video-list.php';
require_once dirname(__DIR__) . '/includes/helpers/video-hero-banner.php';
require_once dirname(__DIR__) . '/includes/helpers/fields-of-activity.php';
