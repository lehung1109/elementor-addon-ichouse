<?php

class EAI_Collaboration_Intro_Widget extends \Elementor\Widget_Base
{
  private const TEMPLATE = 'templates/EAI-collaboration-intro.php';

  public function get_name(): string
  {
    return 'eai_collaboration_intro_widget';
  }

  public function get_title(): string
  {
    return esc_html__('ICHouse — Giới thiệu hợp tác', 'eai');
  }

  public function get_icon(): string
  {
    return 'eicon-person';
  }

  public function get_categories(): array
  {
    return eai_get_widget_categories();
  }

  public function get_keywords(): array
  {
    return ['collaboration', 'intro', 'cooperation', 'hop tac', 'doi tac', 'eai', 'ichouse'];
  }

  protected function register_controls()
  {
    $this->start_controls_section('section_content', [
      'label' => esc_html__('Content', 'eai'),
      'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
    ]);

    $this->add_control('class_name', [
      'label' => esc_html__('Class name', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => '',
    ]);

    $this->add_media_controls('background_image', esc_html__('Background image', 'eai'), 'large');

    $this->add_control('subtitle', [
      'label' => esc_html__('Subtitle', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => '',
      'label_block' => true,
    ]);

    $this->add_control('title_html', [
      'label' => esc_html__('Title', 'eai'),
      'type' => \Elementor\Controls_Manager::WYSIWYG,
      'default' => '',
    ]);

    $this->add_media_controls('image', esc_html__('Top image', 'eai'), 'large');

    $this->add_control('bottom_title', [
      'label' => esc_html__('Bottom title', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXTAREA,
      'default' => '',
      'rows' => 3,
    ]);

    $repeater = new \Elementor\Repeater();
    $repeater->add_control('image', [
      'label' => esc_html__('Icon image', 'eai'),
      'type' => \Elementor\Controls_Manager::MEDIA,
      'label_block' => true,
    ]);
    $repeater->add_control('image_resolution', [
      'label' => esc_html__('Image Resolution', 'eai'),
      'type' => \Elementor\Controls_Manager::SELECT,
      'default' => 'thumbnail',
      'options' => eai_get_image_size_options(),
    ]);
    $repeater->add_control('image_alt', [
      'label' => esc_html__('Alt text (optional)', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => '',
      'label_block' => true,
    ]);
    $repeater->add_control('title', [
      'label' => esc_html__('Title', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => '',
      'label_block' => true,
    ]);

    $this->add_control('items', [
      'label' => esc_html__('Collaboration fields', 'eai'),
      'type' => \Elementor\Controls_Manager::REPEATER,
      'fields' => $repeater->get_controls(),
      'default' => [],
      'title_field' => '{{{ title }}}',
    ]);

    $this->add_control('note', [
      'label' => esc_html__('Note', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXTAREA,
      'default' => '',
      'rows' => 4,
    ]);

    $this->end_controls_section();

    $this->start_controls_section('section_cta', [
      'label' => esc_html__('Call to action', 'eai'),
      'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
    ]);
    $this->add_control('button_label', [
      'label' => esc_html__('Button label', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => '',
      'label_block' => true,
    ]);
    $this->add_control('button_link', [
      'label' => esc_html__('Button link', 'eai'),
      'type' => \Elementor\Controls_Manager::URL,
      'options' => ['url', 'is_external', 'nofollow'],
      'default' => ['url' => ''],
    ]);
    $this->end_controls_section();
  }

  private function add_media_controls(string $field, string $label, string $default_resolution): void
  {
    $this->add_control($field, [
      'label' => $label,
      'type' => \Elementor\Controls_Manager::MEDIA,
      'label_block' => true,
    ]);
    $this->add_control($field . '_resolution', [
      'label' => esc_html__('Image Resolution', 'eai'),
      'type' => \Elementor\Controls_Manager::SELECT,
      'default' => $default_resolution,
      'options' => eai_get_image_size_options(),
    ]);
    $this->add_control($field . '_alt', [
      'label' => esc_html__('Alt text (optional)', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => '',
      'label_block' => true,
    ]);
  }

  protected function render(): void
  {
    $settings = $this->get_settings_for_display();
    $widget_id = $this->get_id();
    $props = eai_collaboration_intro_get_rc_props($settings, $widget_id);

    if (eai_is_elementor_edit_mode() && eai_collaboration_intro_props_are_empty($props)) {
      $props = eai_collaboration_intro_get_editor_sample_props($settings, $widget_id);
      $this->render_props($props);
      return;
    }

    if (eai_collaboration_intro_props_are_empty($props)) {
      eai_render_template(self::TEMPLATE, ['empty' => true]);
      return;
    }

    $this->render_props($props);
  }

  /** @param array<string, mixed> $props */
  private function render_props(array $props): void
  {
    $result = eai_rc_render_html('CollaborationIntro', $props);
    eai_render_template(self::TEMPLATE, [
      'html' => is_wp_error($result) ? '' : $result['html'],
      'error' => is_wp_error($result) ? $result : null,
    ]);
  }
}
