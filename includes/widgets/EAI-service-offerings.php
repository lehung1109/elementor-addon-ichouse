<?php

class EAI_Service_Offerings_Widget extends \Elementor\Widget_Base
{
  private const TEMPLATE = 'templates/EAI-service-offerings.php';

  public function get_name(): string
  {
    return 'eai_service_offerings_widget';
  }

  public function get_title(): string
  {
    return esc_html__('ICHouse — Dịch vụ cung cấp', 'eai');
  }

  public function get_icon(): string
  {
    return 'eicon-gallery-grid';
  }

  public function get_categories(): array
  {
    return eai_get_widget_categories();
  }

  public function get_keywords(): array
  {
    return ['service', 'offerings', 'dich vu', 'thiet ke', 'thi cong', 'eai', 'ichouse'];
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

    $repeater = new \Elementor\Repeater();

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
      'description_html',
      [
        'label' => esc_html__('Description', 'eai'),
        'type' => \Elementor\Controls_Manager::WYSIWYG,
        'default' => '',
      ]
    );

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
        'label' => esc_html__('Service offerings', 'eai'),
        'type' => \Elementor\Controls_Manager::REPEATER,
        'fields' => $repeater->get_controls(),
        'default' => [],
        'title_field' => '{{{ title }}}',
      ]
    );

    $this->end_controls_section();
  }

  protected function render(): void
  {
    $settings = $this->get_settings_for_display();
    $widget_id = $this->get_id();
    $props = eai_service_offerings_get_rc_props($settings, $widget_id);

    if (eai_is_elementor_edit_mode() && empty($props['items'])) {
      $props = eai_service_offerings_get_editor_sample_props($settings, $widget_id);
      $result = eai_rc_render_html('ServiceOfferings', $props);

      eai_render_template(self::TEMPLATE, [
        'html' => is_wp_error($result) ? '' : $result['html'],
        'error' => is_wp_error($result) ? $result : null,
      ]);
      return;
    }

    if (empty($props['items'])) {
      eai_render_template(self::TEMPLATE, ['empty' => true]);
      return;
    }

    $result = eai_rc_render_html('ServiceOfferings', $props);

    eai_render_template(self::TEMPLATE, [
      'html' => is_wp_error($result) ? '' : $result['html'],
      'error' => is_wp_error($result) ? $result : null,
    ]);
  }
}
