<?php

class EAI_Development_Partners_Widget extends \Elementor\Widget_Base
{
  private const TEMPLATE = 'templates/EAI-development-partners.php';

  public function get_name(): string
  {
    return 'eai_development_partners_widget';
  }

  public function get_title(): string
  {
    return esc_html__('ICHouse — Đối tác phát triển', 'eai');
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
    return ['development', 'partners', 'logo', 'doi tac', 'phat trien', 'eai', 'ichouse'];
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
      'title',
      [
        'label' => esc_html__('Title', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => 'ĐỐI TÁC PHÁT TRIỂN',
        'label_block' => true,
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
      'items',
      [
        'label' => esc_html__('Partners', 'eai'),
        'type' => \Elementor\Controls_Manager::REPEATER,
        'fields' => $repeater->get_controls(),
        'default' => [],
        'title_field' => '{{{ alt }}}',
      ]
    );

    $this->end_controls_section();
  }

  protected function render(): void
  {
    $settings = $this->get_settings_for_display();
    $widget_id = $this->get_id();
    $props = eai_development_partners_get_rc_props($settings, $widget_id);

    if (eai_is_elementor_edit_mode() && empty($props['items'])) {
      $props = eai_development_partners_get_editor_sample_props($settings, $widget_id);
      $result = eai_rc_render_html('DevelopmentPartners', $props);

      eai_render_template(self::TEMPLATE, [
        'html' => is_wp_error($result) ? '' : $result['html'],
        'error' => is_wp_error($result) ? $result : null,
      ]);
      return;
    }

    if (empty($props['items'])) {
      eai_render_template(self::TEMPLATE, [
        'empty' => true,
      ]);
      return;
    }

    $result = eai_rc_render_html('DevelopmentPartners', $props);

    eai_render_template(self::TEMPLATE, [
      'html' => is_wp_error($result) ? '' : $result['html'],
      'error' => is_wp_error($result) ? $result : null,
    ]);
  }
}
