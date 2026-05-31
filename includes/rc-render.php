<?php
if (! defined('ABSPATH')) {
  exit;
}

if (! defined('EAI_RC_API_RENDER_URL')) {
  define('EAI_RC_API_RENDER_URL', 'https://api-rc.ichouse.vn/api/render-rc');
}

if (! defined('EAI_RC_CACHE_TTL')) {
  define('EAI_RC_CACHE_TTL', MONTH_IN_SECONDS);
}

if (! function_exists('eai_rc_get_version_manifest')) {
  /**
   * @return array<string, mixed>|null
   */
  function eai_rc_get_version_manifest(): ?array
  {
    static $manifest = null;
    static $loaded = false;

    if ($loaded) {
      return $manifest;
    }

    $loaded = true;
    $path = WP_PLUGIN_DIR . '/rc-files/version.json';

    if (! is_readable($path)) {
      return null;
    }

    $json = file_get_contents($path);
    if ($json === false) {
      return null;
    }

    $decoded = json_decode($json, true);
    if (! is_array($decoded)) {
      return null;
    }

    $manifest = $decoded;

    return $manifest;
  }
}

if (! function_exists('eai_rc_get_component_version')) {
  function eai_rc_get_component_version(string $component): ?string
  {
    $manifest = eai_rc_get_version_manifest();
    if ($manifest === null) {
      return null;
    }

    $version = $manifest['components'][$component]['version'] ?? null;

    return is_string($version) && $version !== '' ? $version : null;
  }
}

if (! function_exists('eai_rc_cache_key')) {
  function eai_rc_cache_key(string $component, array $props): string
  {
    $version = eai_rc_get_component_version($component) ?? 'noversion';
    $props_hash = hash('sha256', wp_json_encode($props));

    return 'eai_rc_' . substr(hash('sha256', "{$component}|{$version}|{$props_hash}"), 0, 32);
  }
}

if (! function_exists('eai_rc_get_api_render_url')) {
  function eai_rc_get_api_render_url(): string
  {
    return (string) apply_filters('eai_rc_api_render_url', EAI_RC_API_RENDER_URL);
  }
}

if (! function_exists('eai_rc_render_html')) {
  /**
   * @return array{html: string, from_cache: bool}|WP_Error
   */
  function eai_rc_render_html(string $component, array $props)
  {
    $component_version = eai_rc_get_component_version($component);
    $use_cache = $component_version !== null;
    $cache_key = $use_cache ? eai_rc_cache_key($component, $props) : null;
    $cached = get_transient($cache_key);

    if (
      $use_cache && $cache_key !== null && is_array($cached)
      && isset($cached['html'])
      && is_string($cached['html'])
      && $cached['html'] !== ''
    ) {
      return [
        'html' => $cached['html'],
        'from_cache' => true,
      ];
    }

    $response = wp_remote_post(
      eai_rc_get_api_render_url(),
      [
        'timeout' => 15,
        'headers' => [
          'Content-Type' => 'application/json',
        ],
        'body' => wp_json_encode([
          'component' => $component,
          'props' => $props,
        ]),
      ]
    );

    if (is_wp_error($response)) {
      return new WP_Error(
        'eai_rc_request_failed',
        $response->get_error_message()
      );
    }

    $status = (int) wp_remote_retrieve_response_code($response);
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    if ($status !== 200) {
      $message = is_array($data) && isset($data['error']) && is_string($data['error'])
        ? $data['error']
        : sprintf('API returned status %d', $status);

      return new WP_Error('eai_rc_api_error', $message);
    }

    if (! is_array($data) || empty($data['html']) || ! is_string($data['html'])) {
      return new WP_Error('eai_rc_invalid_response', 'API response missing html');
    }

    $html = $data['html'];
    $hash = isset($data['hash']) && is_string($data['hash']) ? $data['hash'] : '';

    if ($use_cache && $cache_key !== null) {
      set_transient(
        $cache_key,
        [
          'html' => $html,
          'hash' => $hash,
        ],
        EAI_RC_CACHE_TTL
      );
    }

    return [
      'html' => $html,
      'from_cache' => false,
    ];
  }
}

if (! function_exists('eai_rc_render_error_message')) {
  function eai_rc_render_error_message(WP_Error $error): void
  {
    if (! (defined('WP_DEBUG') && WP_DEBUG) && ! is_user_logged_in()) {
      return;
    }

    echo '<!-- EAI RC render failed: ' . esc_html($error->get_error_message()) . ' -->';
  }
}
