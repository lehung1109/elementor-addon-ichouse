<?php

class EAI_Carousel_Widget extends \Elementor\Widget_Base
{

  public function get_name(): string
  {
    return 'eai_carousel_widget';
  }

  public function get_title(): string
  {
    return esc_html__('EAI Carousel Widget', 'eai');
  }

  public function get_icon(): string
  {
    return 'eicon-slides';
  }

  public function get_categories(): array
  {
    return ['basic'];
  }

  public function get_keywords(): array
  {
    return ['carousel', 'slider', 'eai'];
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

    $repeater = new \Elementor\Repeater();

    $repeater->add_control(
      'image',
      [
        'label' => esc_html__('Image', 'eai'),
        'type' => \Elementor\Controls_Manager::MEDIA,
        'label_block' => true,
      ]
    );

    $repeater->add_control(
      'image_resolution',
      [
        'label' => esc_html__('Image Resolution', 'eai'),
        'type' => \Elementor\Controls_Manager::SELECT,
        'default' => 'large',
        'options' => \eai_get_image_size_options(),
      ]
    );

    $repeater->add_control(
      'link',
      [
        'label' => esc_html__('Link', 'eai'),
        'type' => \Elementor\Controls_Manager::URL,
        'options' => ['url', 'is_external', 'nofollow'],
        'label_block' => true,
      ]
    );

    $this->add_control(
      'slides',
      [
        'label' => esc_html__('Slides', 'eai'),
        'type' => \Elementor\Controls_Manager::REPEATER,
        'fields' => $repeater->get_controls(),
        'default' => [],
        'title_field' => '{{{ image.url }}}',
      ]
    );

    $this->end_controls_section();
  }

  protected function render(): void
  {
    $settings = $this->get_settings_for_display();
    $slides = is_array($settings['slides'] ?? null) ? $settings['slides'] : [];

    $props = [
      'slides' => eai_rc_map_carousel_slides($slides),
    ];

    $result = eai_rc_render_html('CarouselWrapper', $props);

    eai_render_template('templates/EAI-carousel.php', [
      'html' => is_wp_error($result) ? '' : $result['html'],
      'error' => is_wp_error($result) ? $result : null,
    ]);
  }
}
