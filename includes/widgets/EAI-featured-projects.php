<?php

class EAI_Featured_Projects_Widget extends \Elementor\Widget_Base
{
  public function get_name(): string
  {
    return 'eai_featured_projects_widget';
  }

  public function get_title(): string
  {
    return esc_html__('ICHouse — Featured Projects', 'eai');
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
    return ['featured', 'projects', 'du an', 'noi bat', 'gallery', 'eai', 'ichouse'];
  }

  /** @return array<string, mixed> */
  private function get_select2_ajax_config(string $post_type): array
  {
    return [
      'url' => add_query_arg([
        'action' => 'eai_featured_projects_search_posts',
        'nonce' => wp_create_nonce('eai_featured_projects_editor'),
        'post_type' => $post_type,
      ], admin_url('admin-ajax.php')),
      'data_type' => 'json',
      'delay' => 250,
    ];
  }

  protected function register_controls(): void
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
    $this->add_control('subtitle', [
      'label' => esc_html__('Subtitle', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => 'DỰ ÁN',
      'label_block' => true,
    ]);
    $this->add_control('title', [
      'label' => esc_html__('Title', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => 'Dự án nổi bật tại ICHOUSE',
      'label_block' => true,
    ]);

    $post_type_options = eai_get_public_post_type_options();
    $default_post_type = isset($post_type_options['post']) ? 'post' : (string) array_key_first($post_type_options);
    $this->add_control('post_type', [
      'label' => esc_html__('Post type', 'eai'),
      'type' => \Elementor\Controls_Manager::SELECT,
      'options' => $post_type_options,
      'default' => $default_post_type,
    ]);

    foreach ($post_type_options as $post_type => $post_type_label) {
      $this->add_control('selected_posts_' . $post_type, [
        'label' => sprintf(
          esc_html__('Dự án (%s)', 'eai'),
          esc_html($post_type_label)
        ),
        'type' => \Elementor\Controls_Manager::SELECT2,
        'multiple' => true,
        'label_block' => true,
        'options' => [],
        'description' => esc_html__('Chọn tối đa 3 bài publish. Thứ tự lựa chọn là thứ tự hiển thị; ảnh, tiêu đề, mô tả và liên kết được lấy tự động từ bài.', 'eai'),
        'select2options' => [
          'maximumSelectionLength' => 3,
          'ajax' => $this->get_select2_ajax_config((string) $post_type),
        ],
        'condition' => ['post_type' => (string) $post_type],
      ]);
    }

    $this->add_control('image_resolution', [
      'label' => esc_html__('Kích thước ảnh đại diện', 'eai'),
      'type' => \Elementor\Controls_Manager::SELECT,
      'default' => 'large',
      'options' => eai_get_image_size_options(),
    ]);

    $taxonomy_options = ['' => esc_html__('Không hiển thị', 'eai')] + eai_get_public_taxonomy_options();
    $this->add_control('investor_taxonomy', [
      'label' => esc_html__('Taxonomy Chủ đầu tư', 'eai'),
      'type' => \Elementor\Controls_Manager::SELECT,
      'options' => $taxonomy_options,
      'default' => '',
    ]);
    $this->add_control('model_taxonomy', [
      'label' => esc_html__('Taxonomy Mô hình', 'eai'),
      'type' => \Elementor\Controls_Manager::SELECT,
      'options' => $taxonomy_options,
      'default' => '',
    ]);
    $this->end_controls_section();

    $this->start_controls_section('section_cta', [
      'label' => esc_html__('Call to action', 'eai'),
      'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
    ]);
    $this->add_control('button_label', [
      'label' => esc_html__('Button label', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => 'XEM TẤT CẢ DỰ ÁN',
      'label_block' => true,
    ]);
    $this->add_control('button_link', [
      'label' => esc_html__('Button link', 'eai'),
      'type' => \Elementor\Controls_Manager::URL,
      'options' => ['url', 'is_external', 'nofollow'],
      'default' => [
        'url' => '/du-an',
        'is_external' => false,
        'nofollow' => false,
      ],
      'label_block' => true,
    ]);
    $this->end_controls_section();
  }

  protected function render(): void
  {
    $settings = $this->get_settings_for_display();
    $props = eai_featured_projects_get_rc_props($settings, (string) $this->get_id());

    if (eai_is_elementor_edit_mode() && empty($props['items'])) {
      $props = eai_featured_projects_get_editor_sample_props($settings, (string) $this->get_id());
    } elseif (empty($props['items'])) {
      eai_render_template('templates/EAI-featured-projects.php', ['empty' => true]);
      return;
    }

    $result = eai_rc_render_html('FeaturedProjects', $props);
    eai_render_template('templates/EAI-featured-projects.php', [
      'html' => is_wp_error($result) ? '' : $result['html'],
      'error' => is_wp_error($result) ? $result : null,
    ]);
  }
}
