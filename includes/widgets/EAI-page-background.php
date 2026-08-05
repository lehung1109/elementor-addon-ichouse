<?php

class EAI_Page_Background_Widget extends \Elementor\Widget_Base
{

  public function get_name(): string
  {
    return 'eai_page_background_widget';
  }

  public function get_title(): string
  {
    return esc_html__('ICHouse — Page Background', 'eai');
  }

  public function get_icon(): string
  {
    return 'eicon-image';
  }

  public function get_categories(): array
  {
    return eai_get_widget_categories();
  }

  public function get_keywords(): array
  {
    return ['background', 'page', 'wallpaper', 'eai', 'ichouse'];
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
      'mobile_image',
      [
        'label' => esc_html__('Mobile image', 'eai'),
        'type' => \Elementor\Controls_Manager::MEDIA,
        'default' => [
          'url' => '/images/concrete-bg-official-updated-mobile.jpg',
        ],
      ]
    );

    $this->add_control(
      'mobile_image_resolution',
      [
        'label' => esc_html__('Mobile image resolution', 'eai'),
        'type' => \Elementor\Controls_Manager::SELECT,
        'default' => 'large',
        'options' => eai_get_image_size_options(),
      ]
    );

    $this->add_control(
      'desktop_image',
      [
        'label' => esc_html__('Desktop image', 'eai'),
        'type' => \Elementor\Controls_Manager::MEDIA,
        'default' => [
          'url' => 'https://placehold.co/1920x1080/152243/ffffff?text=Page+BG+Desktop',
        ],
      ]
    );

    $this->add_control(
      'desktop_image_resolution',
      [
        'label' => esc_html__('Desktop image resolution', 'eai'),
        'type' => \Elementor\Controls_Manager::SELECT,
        'default' => 'full',
        'options' => eai_get_image_size_options(),
      ]
    );

    $this->end_controls_section();
  }

  protected function get_rc_props(): array
  {
    $settings = $this->get_settings_for_display();
    $mobile_image = is_array($settings['mobile_image'] ?? null) ? $settings['mobile_image'] : [];
    $desktop_image = is_array($settings['desktop_image'] ?? null) ? $settings['desktop_image'] : [];
    $mobile_resolution = (string) ($settings['mobile_image_resolution'] ?? 'large');
    $desktop_resolution = (string) ($settings['desktop_image_resolution'] ?? 'full');
    $class_name = trim((string) ($settings['class_name'] ?? ''));

    $props = [
      'mobileImage' => eai_rc_map_media_model($mobile_image, [], null, $mobile_resolution),
      'desktopImage' => eai_rc_map_media_model($desktop_image, [], null, $desktop_resolution),
    ];

    if ($class_name !== '') {
      $props['className'] = $class_name;
    }

    return $props;
  }

  protected function render(): void
  {
    $props = $this->get_rc_props();
    $mobile_url = trim((string) ($props['mobileImage']['url'] ?? ''));
    $desktop_url = trim((string) ($props['desktopImage']['url'] ?? ''));

    if ($mobile_url === '' && $desktop_url === '') {
      eai_render_template('templates/EAI-page-background.php', [
        'empty' => true,
      ]);
      return;
    }

    $result = eai_rc_render_html('PageBackground', $props);

    eai_render_template('templates/EAI-page-background.php', [
      'html' => is_wp_error($result) ? '' : $result['html'],
      'error' => is_wp_error($result) ? $result : null,
    ]);
  }
}
