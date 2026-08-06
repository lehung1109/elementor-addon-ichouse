<?php

class EAI_News_Events_Widget extends \Elementor\Widget_Base
{
  public function get_name(): string
  {
    return 'eai_news_events_widget';
  }

  public function get_title(): string
  {
    return esc_html__('ICHouse — News Events', 'eai');
  }

  public function get_icon(): string
  {
    return 'eicon-posts-grid';
  }

  public function get_categories(): array
  {
    return eai_get_widget_categories();
  }

  public function get_keywords(): array
  {
    return ['news', 'events', 'tin tuc', 'su kien', 'posts', 'eai', 'ichouse'];
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
        'default' => 'TIN TỨC - SỰ KIỆN',
        'label_block' => true,
      ]
    );

    $this->add_control(
      'post_type',
      [
        'label' => esc_html__('Post type', 'eai'),
        'type' => \Elementor\Controls_Manager::SELECT,
        'default' => 'post',
        'options' => eai_get_public_post_type_options(),
        'description' => esc_html__(
          'Tự lấy 5 bài publish mới cập nhật nhất (có featured image) theo post type.',
          'eai'
        ),
      ]
    );

    $this->add_control(
      'image_resolution',
      [
        'label' => esc_html__('Image Resolution', 'eai'),
        'type' => \Elementor\Controls_Manager::SELECT,
        'default' => 'large',
        'options' => eai_get_image_size_options(),
      ]
    );

    $this->add_control(
      'scroll_reveal_target_id',
      [
        'label' => esc_html__('Scroll reveal target ID', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => 'news-events',
        'description' => esc_html__('DOM id trên section (IntersectionObserver).', 'eai'),
      ]
    );

    $this->end_controls_section();

    $this->start_controls_section(
      'section_cta',
      [
        'label' => esc_html__('Call to action', 'eai'),
        'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_control(
      'button_label',
      [
        'label' => esc_html__('Button label', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => 'TÌM HIỂU THÊM',
        'label_block' => true,
      ]
    );

    $this->add_control(
      'button_link',
      [
        'label' => esc_html__('Button link', 'eai'),
        'type' => \Elementor\Controls_Manager::URL,
        'options' => ['url', 'is_external', 'nofollow'],
        'default' => [
          'url' => '/tin-tuc',
          'is_external' => false,
          'nofollow' => false,
        ],
        'label_block' => true,
      ]
    );

    $this->end_controls_section();
  }

  protected function get_rc_props(): array
  {
    return eai_news_events_get_rc_props($this->get_settings_for_display());
  }

  protected function render(): void
  {
    $settings = $this->get_settings_for_display();
    $props = eai_news_events_get_rc_props($settings);

    if (eai_is_elementor_edit_mode() && empty($props['items'])) {
      $props = eai_news_events_get_editor_sample_props($settings);
      $result = eai_rc_render_html('NewsEvents', $props);

      eai_render_template('templates/EAI-news-events.php', [
        'html' => is_wp_error($result) ? '' : $result['html'],
        'error' => is_wp_error($result) ? $result : null,
      ]);
      return;
    }

    if (empty($props['items'])) {
      eai_render_template('templates/EAI-news-events.php', [
        'empty' => true,
      ]);
      return;
    }

    $result = eai_rc_render_html('NewsEvents', $props);

    eai_render_template('templates/EAI-news-events.php', [
      'html' => is_wp_error($result) ? '' : $result['html'],
      'error' => is_wp_error($result) ? $result : null,
    ]);
  }
}
