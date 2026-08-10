<?php

class EAI_Post_Hero_Banner_Widget extends \Elementor\Widget_Base
{
  public function get_name(): string
  {
    return 'eai_post_hero_banner_widget';
  }

  public function get_title(): string
  {
    return esc_html__('ICHouse — Post Hero Banner', 'eai');
  }

  public function get_icon(): string
  {
    return 'eicon-banner';
  }

  public function get_categories(): array
  {
    return eai_get_widget_categories();
  }

  public function get_keywords(): array
  {
    return ['hero', 'banner', 'acf', 'breadcrumb', 'post', 'eai', 'ichouse'];
  }

  protected function register_controls(): void
  {
    $this->start_controls_section('section_content', [
      'label' => esc_html__('Content', 'eai'),
      'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
    ]);

    $this->add_control('manual_content', [
      'label' => esc_html__('Manual content', 'eai'),
      'type' => \Elementor\Controls_Manager::SWITCHER,
      'label_on' => esc_html__('Manual', 'eai'),
      'label_off' => esc_html__('Auto', 'eai'),
      'return_value' => 'yes',
      'default' => '',
    ]);

    $this->add_control('acf_image_field', [
      'label' => esc_html__('ACF background image', 'eai'),
      'type' => \Elementor\Controls_Manager::SELECT,
      'options' => ['' => esc_html__('Chọn field ảnh', 'eai')] + eai_get_acf_field_options_by_types(['image']),
      'default' => '',
      'condition' => ['manual_content!' => 'yes'],
    ]);

    $this->add_control('manual_title', [
      'label' => esc_html__('Manual title', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXTAREA,
      'default' => '',
      'rows' => 3,
      'condition' => ['manual_content' => 'yes'],
    ]);

    $this->add_control('manual_image', [
      'label' => esc_html__('Manual background image', 'eai'),
      'type' => \Elementor\Controls_Manager::MEDIA,
      'default' => ['url' => ''],
      'condition' => ['manual_content' => 'yes'],
    ]);

    $this->add_control('image_size', [
      'label' => esc_html__('Image size', 'eai'),
      'type' => \Elementor\Controls_Manager::SELECT,
      'options' => eai_get_image_size_options(),
      'default' => 'full',
    ]);

    $breadcrumb_repeater = new \Elementor\Repeater();

    $breadcrumb_repeater->add_control('label', [
      'label' => esc_html__('Label', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => '',
      'label_block' => true,
    ]);

    $breadcrumb_repeater->add_control('link', [
      'label' => esc_html__('URL', 'eai'),
      'type' => \Elementor\Controls_Manager::URL,
      'options' => ['url', 'is_external', 'nofollow'],
      'default' => ['url' => ''],
      'label_block' => true,
    ]);

    $this->add_control('breadcrumb_items', [
      'label' => esc_html__('Breadcrumb items', 'eai'),
      'type' => \Elementor\Controls_Manager::REPEATER,
      'fields' => $breadcrumb_repeater->get_controls(),
      'default' => [],
      'title_field' => '{{{ label }}}',
      'description' => esc_html__('Nhập tối đa 2 mục; mục thiếu nhãn hoặc URL sẽ không hiển thị.', 'eai'),
    ]);

    $this->add_control('title_heading', [
      'label' => esc_html__('Title heading', 'eai'),
      'type' => \Elementor\Controls_Manager::SELECT,
      'options' => ['h1' => 'H1', 'h2' => 'H2'],
      'default' => 'h1',
    ]);

    $this->add_control('class_name', [
      'label' => esc_html__('CSS class (optional)', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => '',
      'label_block' => true,
    ]);

    $this->end_controls_section();
  }

  protected function get_current_post_id(): int
  {
    $post_id = (int) get_queried_object_id();
    if ($post_id <= 0) {
      $post_id = (int) get_the_ID();
    }

    return $post_id;
  }

  protected function render(): void
  {
    $settings = $this->get_settings_for_display();
    $post_id = $this->get_current_post_id();
    $props = eai_post_hero_banner_get_rc_props($post_id, $settings);

    if (eai_is_elementor_edit_mode() && eai_post_hero_banner_props_are_empty($props)) {
      $props = eai_post_hero_banner_get_editor_sample_props($settings);
      $this->render_result(eai_rc_render_html('PostHeroBanner', $props));
      return;
    }

    if (eai_post_hero_banner_props_are_empty($props)) {
      eai_render_template('templates/EAI-post-hero-banner.php', [
        'html' => '',
        'error' => null,
        'empty' => true,
      ]);
      return;
    }

    $this->render_result(eai_rc_render_html('PostHeroBanner', $props));
  }

  private function render_result(mixed $result): void
  {
    eai_render_template('templates/EAI-post-hero-banner.php', [
      'html' => is_wp_error($result) ? '' : $result['html'],
      'error' => is_wp_error($result) ? $result : null,
    ]);
  }
}
