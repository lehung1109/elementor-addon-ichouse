<?php

class EAI_Partner_Logos_Widget extends \Elementor\Widget_Base
{

  public function get_name(): string
  {
    return 'eai_partner_logos_widget';
  }

  public function get_title(): string
  {
    return esc_html__('ICHouse — Logo đối tác', 'eai');
  }

  public function get_icon(): string
  {
    return 'eicon-logo';
  }

  public function get_categories(): array
  {
    return eai_get_widget_categories();
  }

  public function get_keywords(): array
  {
    return ['partner', 'logo', 'brand', 'doi tac', 'carousel', 'slider', 'eai', 'ichouse'];
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
        'label' => esc_html__('Logo', 'eai'),
        'type' => \Elementor\Controls_Manager::MEDIA,
        'label_block' => true,
      ]
    );

    $repeater->add_control(
      'image_resolution',
      [
        'label' => esc_html__('Image Resolution', 'eai'),
        'type' => \Elementor\Controls_Manager::SELECT,
        'default' => 'medium',
        'options' => eai_get_image_size_options(),
      ]
    );

    $repeater->add_control(
      'alt',
      [
        'label' => esc_html__('Alt text (optional)', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => '',
        'label_block' => true,
        'description' => esc_html__('Overrides attachment alt when set.', 'eai'),
      ]
    );

    $this->add_control(
      'logos',
      [
        'label' => esc_html__('Logos', 'eai'),
        'type' => \Elementor\Controls_Manager::REPEATER,
        'fields' => $repeater->get_controls(),
        'default' => [],
        'title_field' => '{{{ alt }}}',
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
        'label' => esc_html__('Slides per view (tablet/desktop)', 'eai'),
        'type' => \Elementor\Controls_Manager::NUMBER,
        'min' => 1,
        'max' => 7,
        'step' => 1,
        'default' => 5,
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
        'default' => 32,
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
    $logos = is_array($settings['logos'] ?? null) ? $settings['logos'] : [];
    $slides_per_view = (int) ($settings['slides_per_view'] ?? 5);

    if ($slides_per_view < 1) {
      $slides_per_view = 1;
    }
    if ($slides_per_view > 7) {
      $slides_per_view = 7;
    }

    $space_between = (int) ($settings['space_between'] ?? 32);
    if ($space_between < 0) {
      $space_between = 0;
    }

    return [
      'logos' => eai_rc_map_partner_logos($logos),
      'slidesPerView' => $slides_per_view,
      'spaceBetween' => $space_between,
      'loop' => ($settings['loop'] ?? 'yes') === 'yes',
    ];
  }

  protected function render(): void
  {
    $props = $this->get_rc_props();

    if (empty($props['logos'])) {
      eai_render_template('templates/EAI-partner-logos.php', [
        'empty' => true,
      ]);
      return;
    }

    $result = eai_rc_render_html('PartnerLogosWrapper', $props);

    eai_render_template('templates/EAI-partner-logos.php', [
      'html' => is_wp_error($result) ? '' : $result['html'],
      'error' => is_wp_error($result) ? $result : null,
    ]);
  }
}
