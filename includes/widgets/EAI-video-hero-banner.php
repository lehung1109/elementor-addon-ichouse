<?php

class EAI_Video_Hero_Banner_Widget extends \Elementor\Widget_Base
{

  public function get_name(): string
  {
    return 'eai_video_hero_banner_widget';
  }

  public function get_title(): string
  {
    return esc_html__('ICHouse — Video Hero Banner', 'eai');
  }

  public function get_icon(): string
  {
    return 'eicon-video-playlist';
  }

  public function get_categories(): array
  {
    return eai_get_widget_categories();
  }

  public function get_keywords(): array
  {
    return ['video', 'hero', 'banner', 'mp4', 'eai', 'ichouse'];
  }

  protected function register_controls()
  {
    $this->start_controls_section(
      'section_content',
      [
        'label' => esc_html__('Content', 'eai'),
        'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_control(
      'class_name',
      [
        'label' => esc_html__('Class name', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => '',
      ]
    );

    $this->add_control(
      'video',
      [
        'label' => esc_html__('Video', 'eai'),
        'type' => \Elementor\Controls_Manager::MEDIA,
        'media_types' => ['video'],
        'description' => esc_html__('Upload MP4 hoặc WebM từ Media Library.', 'eai'),
      ]
    );

    $this->add_control(
      'poster',
      [
        'label' => esc_html__('Poster image', 'eai'),
        'type' => \Elementor\Controls_Manager::MEDIA,
        'default' => [
          'url' => 'https://placehold.co/1920x1080/1a1a2e/fff?text=Video+Hero',
        ],
      ]
    );

    $this->add_control(
      'poster_resolution',
      [
        'label' => esc_html__('Poster resolution', 'eai'),
        'type' => \Elementor\Controls_Manager::SELECT,
        'default' => 'large',
        'options' => eai_get_image_size_options(),
      ]
    );

    $this->end_controls_section();
  }

  protected function get_rc_props(): array
  {
    $settings = $this->get_settings_for_display();
    $video = is_array($settings['video'] ?? null) ? $settings['video'] : [];
    $poster = is_array($settings['poster'] ?? null) ? $settings['poster'] : [];
    $resolution = (string) ($settings['poster_resolution'] ?? 'large');
    $class_name = trim((string) ($settings['class_name'] ?? ''));

    $props = [
      'url' => trim((string) ($video['url'] ?? '')),
      'poster' => eai_rc_map_media_model($poster, [], null, $resolution),
    ];

    if ($class_name !== '') {
      $props['className'] = $class_name;
    }

    return $props;
  }

  protected function render(): void
  {
    $props = $this->get_rc_props();
    $poster_url = (string) ($props['poster']['url'] ?? '');
    $url = trim((string) ($props['url'] ?? ''));

    if ($url === '' || $poster_url === '') {
      eai_render_template('templates/EAI-video-hero-banner.php', [
        'empty' => true,
      ]);
      return;
    }

    $result = eai_rc_render_html('VideoHeroBannerWrapper', $props);

    eai_render_template('templates/EAI-video-hero-banner.php', [
      'html' => is_wp_error($result) ? '' : $result['html'],
      'error' => is_wp_error($result) ? $result : null,
    ]);
  }
}
