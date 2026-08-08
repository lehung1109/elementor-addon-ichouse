<?php

class EAI_News_List_Widget extends \Elementor\Widget_Base
{
  public function get_name(): string { return 'eai_news_list_widget'; }
  public function get_title(): string { return esc_html__('ICHouse — Danh sách tin tức', 'eai'); }
  public function get_icon(): string { return 'eicon-post-list'; }
  public function get_categories(): array { return eai_get_widget_categories(); }
  public function get_keywords(): array { return ['news', 'events', 'tin tức', 'bài viết', 'eai', 'ichouse']; }

  protected function register_controls(): void
  {
    $this->start_controls_section('section_query', [
      'label' => esc_html__('Danh sách tin tức', 'eai'),
      'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
    ]);
    $this->add_control('post_type', [
      'label' => esc_html__('Post type', 'eai'),
      'type' => \Elementor\Controls_Manager::SELECT,
      'options' => eai_get_public_post_type_options(),
      'default' => 'post',
    ]);
    $this->add_control('page_size', [
      'label' => esc_html__('Số bài mỗi trang', 'eai'),
      'type' => \Elementor\Controls_Manager::NUMBER,
      'default' => 5,
      'min' => 1,
      'max' => 24,
    ]);
    $this->add_control('image_size', [
      'label' => esc_html__('Kích thước ảnh', 'eai'),
      'type' => \Elementor\Controls_Manager::SELECT,
      'default' => 'large',
      'options' => eai_get_image_size_options(),
    ]);
    $this->add_control('featured_background_image', [
      'label' => esc_html__('Ảnh nền nội dung tin nổi bật', 'eai'),
      'type' => \Elementor\Controls_Manager::MEDIA,
      'default' => [],
    ]);
    $this->add_control('page_query_param', [
      'label' => esc_html__('Query key phân trang', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => 'paged',
      'description' => esc_html__('Dùng key riêng nếu trang có nhiều danh sách phân trang.', 'eai'),
    ]);
    $this->add_control('class_name', [
      'label' => esc_html__('Class tùy chỉnh', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => '',
    ]);
    $this->end_controls_section();
  }

  protected function render(): void
  {
    $settings = $this->get_settings_for_display();
    $props = eai_news_list_get_rc_props($settings);

    if (eai_is_elementor_edit_mode() && empty($props['items'])) {
      $props = eai_news_list_get_editor_sample_props($settings);
    } elseif (empty($props['items']) && (int) ($props['totalPages'] ?? 0) < 1) {
      eai_render_template('templates/EAI-news-list.php', [
        'html' => '',
        'error' => null,
        'empty' => true,
      ]);
      return;
    }

    $result = eai_rc_render_html('NewsListWrapper', $props);
    eai_render_template('templates/EAI-news-list.php', [
      'html' => is_wp_error($result) ? '' : $result['html'],
      'error' => is_wp_error($result) ? $result : null,
    ]);
  }
}
