<?php

class EAI_Feature_Cards_Carousel_Widget extends \Elementor\Widget_Base
{

  public function get_name(): string
  {
    return 'eai_feature_cards_carousel_widget';
  }

  public function get_title(): string
  {
    return esc_html__('ICHouse — Carousel thẻ tính năng', 'eai');
  }

  public function get_icon(): string
  {
    return 'eicon-posts-carousel';
  }

  public function get_categories(): array
  {
    return eai_get_widget_categories();
  }

  public function get_keywords(): array
  {
    return ['feature', 'cards', 'carousel', 'slider', 'the tinh nang', 'eai', 'ichouse'];
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
        'options' => eai_get_image_size_options(),
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

    $repeater->add_control(
      'title',
      [
        'label' => esc_html__('Title', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => '',
        'label_block' => true,
      ]
    );

    $repeater->add_control(
      'description',
      [
        'label' => esc_html__('Description', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXTAREA,
        'default' => '',
        'rows' => 3,
      ]
    );

    $this->add_control(
      'items',
      [
        'label' => esc_html__('Cards', 'eai'),
        'type' => \Elementor\Controls_Manager::REPEATER,
        'fields' => $repeater->get_controls(),
        'default' => [],
        'title_field' => '{{{ title }}}',
      ]
    );

    $this->end_controls_section();

    $this->start_controls_section(
      'section_carousel',
      [
        'label' => esc_html__('Carousel', 'eai'),
        'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_control(
      'slides_per_view',
      [
        'label' => esc_html__('Slides per view', 'eai'),
        'type' => \Elementor\Controls_Manager::NUMBER,
        'min' => 1,
        'max' => 6,
        'step' => 1,
        'default' => 3,
      ]
    );

    $this->add_control(
      'space_between',
      [
        'label' => esc_html__('Space between (px)', 'eai'),
        'type' => \Elementor\Controls_Manager::NUMBER,
        'min' => 0,
        'max' => 64,
        'step' => 1,
        'default' => 16,
      ]
    );

    $this->add_control(
      'loop',
      [
        'label' => esc_html__('Loop', 'eai'),
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label_on' => esc_html__('Yes', 'eai'),
        'label_off' => esc_html__('No', 'eai'),
        'return_value' => 'yes',
        'default' => 'yes',
      ]
    );

    $this->end_controls_section();
  }

  protected function get_rc_props(): array
  {
    $settings = $this->get_settings_for_display();
    $items = is_array($settings['items'] ?? null) ? $settings['items'] : [];
    $slides_per_view = (int) ($settings['slides_per_view'] ?? 3);

    if ($slides_per_view < 1) {
      $slides_per_view = 1;
    }

    $space_between = (int) ($settings['space_between'] ?? 16);
    if ($space_between < 0) {
      $space_between = 0;
    }

    return [
      'items' => eai_rc_map_feature_cards_carousel_items($items),
      'slidesPerView' => $slides_per_view,
      'spaceBetween' => $space_between,
      'loop' => ($settings['loop'] ?? 'yes') === 'yes',
    ];
  }

  protected function render(): void
  {
    $props = $this->get_rc_props();

    if (empty($props['items'])) {
      eai_render_template('templates/EAI-feature-cards-carousel.php', [
        'empty' => true,
      ]);
      return;
    }

    $result = eai_rc_render_html('FeatureCardsCarouselWrapper', $props);

    eai_render_template('templates/EAI-feature-cards-carousel.php', [
      'html' => is_wp_error($result) ? '' : $result['html'],
      'error' => is_wp_error($result) ? $result : null,
    ]);
  }
}
