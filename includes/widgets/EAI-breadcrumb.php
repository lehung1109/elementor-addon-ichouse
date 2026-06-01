<?php

class EAI_Breadcrumb_Widget extends \Elementor\Widget_Base
{

  public function get_name(): string
  {
    return 'eai_breadcrumb_widget';
  }

  public function get_title(): string
  {
    return esc_html__('ICHouse — Breadcrumb', 'eai');
  }

  public function get_icon(): string
  {
    return 'eicon-navigation-horizontal';
  }

  public function get_categories(): array
  {
    return eai_get_widget_categories();
  }

  public function get_keywords(): array
  {
    return ['breadcrumb', 'navigation', 'eai', 'ichouse'];
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
      'level_1_label',
      [
        'label' => esc_html__('Level 1 label', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => 'Trang chủ',
        'label_block' => true,
      ]
    );

    $this->add_control(
      'level_1_link',
      [
        'label' => esc_html__('Level 1 link', 'eai'),
        'type' => \Elementor\Controls_Manager::URL,
        'options' => ['url', 'is_external', 'nofollow'],
        'default' => [
          'url' => home_url('/'),
          'is_external' => false,
          'nofollow' => false,
        ],
        'label_block' => true,
      ]
    );

    $this->add_control(
      'level_2_label',
      [
        'label' => esc_html__('Level 2 label', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => '',
        'label_block' => true,
        'description' => esc_html__('Để trống nếu không dùng cấp 2.', 'eai'),
      ]
    );

    $this->add_control(
      'level_2_link',
      [
        'label' => esc_html__('Level 2 link', 'eai'),
        'type' => \Elementor\Controls_Manager::URL,
        'options' => ['url', 'is_external', 'nofollow'],
        'default' => [
          'url' => '',
          'is_external' => false,
          'nofollow' => false,
        ],
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

  protected function render(): void
  {
    $settings = $this->get_settings_for_display();
    $post_id = $this->get_current_post_id();
    $props = eai_breadcrumb_get_rc_props($post_id, $settings);

    if (
      eai_is_elementor_edit_mode()
      && (
        $post_id <= 0
        || trim((string) ($props['currentLabel'] ?? '')) === ''
      )
    ) {
      $props = eai_breadcrumb_get_editor_sample_props($settings);
      $result = eai_rc_render_html('Breadcrumb', $props);

      eai_render_template('templates/EAI-breadcrumb.php', [
        'html' => is_wp_error($result) ? '' : $result['html'],
        'error' => is_wp_error($result) ? $result : null,
      ]);

      return;
    }

    if ($post_id <= 0) {
      eai_render_template('templates/EAI-breadcrumb.php', [
        'html' => '',
        'error' => null,
        'empty' => true,
      ]);
      return;
    }

    $result = eai_rc_render_html('Breadcrumb', $props);

    eai_render_template('templates/EAI-breadcrumb.php', [
      'html' => is_wp_error($result) ? '' : $result['html'],
      'error' => is_wp_error($result) ? $result : null,
    ]);
  }
}
