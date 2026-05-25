<?php
class EAI_Header_Inner_Widget extends \Elementor\Widget_Base
{

  public function get_name(): string
  {
    return 'eai_header_inner_widget';
  }

  public function get_title(): string
  {
    return esc_html__('EAI Header Inner Widget', 'eai');
  }

  public function get_icon(): string
  {
    return 'eicon-header';
  }

  public function get_categories(): array
  {
    return ['basic'];
  }

  public function get_keywords(): array
  {
    return ['header', 'eai'];
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
      'logo',
      [
        'label' => esc_html__('Logo', 'eai'),
        'type' => \Elementor\Controls_Manager::MEDIA,
        'label_block' => true,
      ]
    );

    // add logo dimensions control for logo
    $this->add_control(
      'logo_dimensions',
      [
        'label' => esc_html__('Logo Dimensions', 'eai'),
        'type' => \Elementor\Controls_Manager::IMAGE_DIMENSIONS,
        'label_block' => true,
      ]
    );

    // add logo link control for logo
    $this->add_control(
      'logo_link',
      [
        'label' => esc_html__('Logo Link', 'eai'),
        'type' => \Elementor\Controls_Manager::URL,
        'label_block' => true,
      ]
    );

    $repeater = new \Elementor\Repeater();

    $repeater->add_control(
      'icon',
      [
        'label' => esc_html__('Icon', 'eai'),
        'type' => \Elementor\Controls_Manager::MEDIA,
        'label_block' => true,
      ]
    );

    // add icon dimensions control for icon
    $repeater->add_control(
      'icon_dimensions',
      [
        'label' => esc_html__('Icon Dimensions', 'eai'),
        'type' => \Elementor\Controls_Manager::IMAGE_DIMENSIONS,
        'label_block' => true,
      ]
    );

    $repeater->add_control(
      'text',
      [
        'label' => esc_html__('Text', 'eai'),
        'type' => \Elementor\Controls_Manager::WYSIWYG,
        'label_block' => true,
      ]
    );

    $this->add_control(
      'info_list',
      [
        'label' => esc_html__('Information List', 'eai'),
        'type' => \Elementor\Controls_Manager::REPEATER,
        'title_field' => '{{{ text }}}',
        'fields' => $repeater->get_controls(),
      ]
    );

    $this->end_controls_section();
  }

  protected function render(): void
  {
    $settings = $this->get_settings_for_display();
    $logo = is_array($settings['logo'] ?? null) ? $settings['logo'] : [];
    $logo_dimensions = is_array($settings['logo_dimensions'] ?? null)
      ? $settings['logo_dimensions']
      : [];
    $logo_link = is_array($settings['logo_link'] ?? null) ? $settings['logo_link'] : [];
    $info_list = is_array($settings['info_list'] ?? null) ? $settings['info_list'] : [];

    $logo_link_props = ! empty($logo_link['url']) ? $logo_link : null;

    $props = [
      'logo' => eai_rc_map_media_model($logo, $logo_dimensions, $logo_link_props),
      'info_list' => eai_rc_map_header_inner_info_list($info_list),
    ];

    $result = eai_rc_render_html('HeaderInner', $props);

    eai_render_template('templates/EAI-header-inner.php', [
      'html' => is_wp_error($result) ? '' : $result['html'],
      'error' => is_wp_error($result) ? $result : null,
    ]);
  }
}
