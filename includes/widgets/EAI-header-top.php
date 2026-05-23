<?php
class EAI_Header_Top_Widget extends \Elementor\Widget_Base
{

  public function get_name(): string
  {
    return 'eai_header_top_widget';
  }

  public function get_title(): string
  {
    return esc_html__('EAI Header Top Widget', 'eai');
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
      'text',
      [
        'label' => esc_html__('Text', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => 'text content here',
        'label_block' => true,
      ]
    );

    $this->add_control(
      'phone',
      [
        'label' => esc_html__('Phone', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'placeholder' => 'Enter phone...',
        'label_block' => true,
      ]
    );

    $this->add_control(
      'link_phone',
      [
        'label' => esc_html__('Link Phone', 'eai'),
        'type' => \Elementor\Controls_Manager::URL,
        'options' => ['url', 'is_external', 'nofollow'],
        'default' => [
          'url' => 'https://zalo.me/0345051295',
          'is_external' => true,
          'nofollow' => true,
        ],
        'label_block' => true,
      ]
    );

    $this->add_control(
      'search_placeholder',
      [
        'label' => esc_html__('Search Placeholder', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'placeholder' => 'Enter search placeholder...',
        'label_block' => true,
      ]
    );

    $this->end_controls_section();
  }

  protected function render(): void
  {
    $settings = $this->get_settings_for_display();
    $text = $settings['text'] ?? '';
    $phone = $settings['phone'] ?? '';
    $placeholder = $settings['placeholder'] ?? '';

    eai_render_template('templates/EAI-header-top.php', [
      'text' => $text,
      'phone' => $phone,
      'placeholder' => $placeholder,
    ]);
  }
}
