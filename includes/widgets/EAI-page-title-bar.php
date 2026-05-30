<?php

class EAI_Page_Title_Bar_Widget extends \Elementor\Widget_Base
{

  public function get_name(): string
  {
    return 'eai_page_title_bar_widget';
  }

  public function get_title(): string
  {
    return esc_html__('ICHouse — Page Title Bar', 'eai');
  }

  public function get_icon(): string
  {
    return 'eicon-archive-title';
  }

  public function get_categories(): array
  {
    return eai_get_widget_categories();
  }

  public function get_keywords(): array
  {
    return ['title', 'breadcrumb', 'page title', 'heading', 'eai', 'ichouse'];
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
      'taxonomy',
      [
        'label' => esc_html__('Taxonomy', 'eai'),
        'type' => \Elementor\Controls_Manager::SELECT,
        'default' => 'category',
        'options' => eai_get_public_taxonomy_options(),
        'label_block' => true,
        'description' => esc_html__(
          'Terms gán trực tiếp cho bài đang xem (cấp breadcrumb thứ hai).',
          'eai'
        ),
      ]
    );

    $this->add_control(
      'home_label',
      [
        'label' => esc_html__('Home label', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => 'Home',
        'label_block' => true,
      ]
    );

    $this->add_control(
      'class_name',
      [
        'label' => esc_html__('CSS class (optional)', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => '',
        'label_block' => true,
      ]
    );

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

  protected function get_rc_props(): array
  {
    $settings = $this->get_settings_for_display();
    $post_id = $this->get_current_post_id();

    return eai_page_title_bar_get_rc_props($post_id, $settings);
  }

  protected function render(): void
  {
    $post_id = $this->get_current_post_id();

    if ($post_id <= 0) {
      eai_render_template('templates/EAI-page-title-bar.php', [
        'html' => '',
        'error' => null,
        'empty' => true,
      ]);
      return;
    }

    $props = $this->get_rc_props();
    $result = eai_rc_render_html('PageTitleBar', $props);

    eai_render_template('templates/EAI-page-title-bar.php', [
      'html' => is_wp_error($result) ? '' : $result['html'],
      'error' => is_wp_error($result) ? $result : null,
    ]);
  }
}
