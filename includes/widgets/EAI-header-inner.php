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

    $repeater = new \Elementor\Repeater();

    $repeater->add_control(
      'icon',
      [
        'label' => esc_html__('Icon', 'eai'),
        'type' => \Elementor\Controls_Manager::MEDIA,
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
    $logo = $settings['logo'] ?? '';
    $info_list = $settings['info_list'] ?? [];

    eai_render_template('templates/EAI-header-inner.php', [
      'logo' => $logo,
      'info_list' => $info_list,
    ]);
  }
}
