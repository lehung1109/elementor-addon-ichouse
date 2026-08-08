<?php

class EAI_Job_Listing_List_Widget extends \Elementor\Widget_Base
{
  public function get_name(): string { return 'eai_job_listing_list_widget'; }
  public function get_title(): string { return esc_html__('ICHouse — Danh sách tuyển dụng', 'eai'); }
  public function get_icon(): string { return 'eicon-post-list'; }
  public function get_categories(): array { return eai_get_widget_categories(); }
  public function get_keywords(): array { return ['job', 'listing', 'recruitment', 'tuyển dụng', 'eai', 'ichouse']; }

  protected function register_controls(): void
  {
    $this->start_controls_section('section_query', [
      'label' => esc_html__('Query tuyển dụng', 'eai'),
      'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
    ]);
    $this->add_control('post_type', [
      'label' => esc_html__('Post type', 'eai'),
      'type' => \Elementor\Controls_Manager::SELECT,
      'options' => eai_get_public_post_type_options(),
      'default' => 'post',
    ]);
    $this->add_control('taxonomy', [
      'label' => esc_html__('Taxonomy giới hạn', 'eai'),
      'type' => \Elementor\Controls_Manager::SELECT,
      'options' => ['' => esc_html__('Không giới hạn', 'eai')] + eai_get_public_taxonomy_options(),
      'default' => '',
    ]);
    foreach (get_taxonomies(['public' => true], 'objects') as $taxonomy) {
      if (empty($taxonomy->name)) {
        continue;
      }
      $this->add_control('include_terms_' . $taxonomy->name, [
        'label' => esc_html__('Term giới hạn', 'eai'),
        'type' => \Elementor\Controls_Manager::SELECT2,
        'options' => eai_get_taxonomy_terms_as_options($taxonomy->name),
        'multiple' => true,
        'condition' => ['taxonomy' => $taxonomy->name],
      ]);
    }
    $this->add_control('employment_type_taxonomy', [
      'label' => esc_html__('Taxonomy loại hình làm việc', 'eai'),
      'type' => \Elementor\Controls_Manager::SELECT,
      'options' => ['' => esc_html__('Không hiển thị', 'eai')] + eai_get_public_taxonomy_options(),
      'default' => '',
    ]);
    $this->add_control('location_field', [
      'label' => esc_html__('ACF vị trí làm việc', 'eai'),
      'type' => \Elementor\Controls_Manager::SELECT,
      'options' => ['' => esc_html__('Không hiển thị', 'eai')] + eai_get_acf_field_options_by_types(['text', 'textarea', 'select', 'radio']),
      'default' => '',
    ]);
    $this->add_control('expiration_date_field', [
      'label' => esc_html__('ACF ngày hết hạn', 'eai'),
      'type' => \Elementor\Controls_Manager::SELECT,
      'options' => ['' => esc_html__('Không kiểm tra', 'eai')] + eai_get_acf_field_options_by_types(['date_picker']),
      'default' => '',
    ]);
    $this->add_control('page_size', [
      'label' => esc_html__('Số bài mỗi trang', 'eai'),
      'type' => \Elementor\Controls_Manager::NUMBER,
      'default' => 3,
      'min' => 1,
      'max' => 24,
    ]);
    $this->add_control('orderby', [
      'label' => esc_html__('Sắp xếp theo', 'eai'),
      'type' => \Elementor\Controls_Manager::SELECT,
      'default' => 'date',
      'options' => ['date' => 'Ngày đăng', 'modified' => 'Ngày cập nhật', 'title' => 'Tiêu đề', 'menu_order' => 'Menu order'],
    ]);
    $this->add_control('order', [
      'label' => esc_html__('Thứ tự', 'eai'),
      'type' => \Elementor\Controls_Manager::SELECT,
      'default' => 'DESC',
      'options' => ['DESC' => 'Giảm dần', 'ASC' => 'Tăng dần'],
    ]);
    $this->add_control('image_size', [
      'label' => esc_html__('Kích thước ảnh', 'eai'),
      'type' => \Elementor\Controls_Manager::SELECT,
      'default' => 'large',
      'options' => eai_get_image_size_options(),
    ]);
    $this->add_control('page_query_param', [
      'label' => esc_html__('Query key phân trang', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => 'jobs_page',
      'description' => esc_html__('Dùng key riêng nếu trang có nhiều danh sách phân trang.', 'eai'),
    ]);
    $this->add_control('class_name', [
      'label' => esc_html__('Class tùy chỉnh', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => '',
    ]);
    $this->end_controls_section();
  }

  protected function normalized_settings(): array
  {
    $settings = $this->get_settings_for_display();
    $taxonomy = sanitize_key((string) ($settings['taxonomy'] ?? ''));
    $dynamic_key = 'include_terms_' . $taxonomy;
    $settings['include_terms'] = is_array($settings[$dynamic_key] ?? null) ? $settings[$dynamic_key] : [];

    return $settings;
  }

  protected function render(): void
  {
    $settings = $this->normalized_settings();
    $props = eai_job_listing_list_get_rc_props($settings);

    if (eai_is_elementor_edit_mode() && empty($props['items'])) {
      $props = eai_job_listing_list_get_editor_sample_props($settings);
    } elseif (empty($props['items']) && (int) ($props['totalPages'] ?? 0) < 1) {
      eai_render_template('templates/EAI-job-listing-list.php', ['html' => '', 'error' => null, 'empty' => true]);
      return;
    }

    $result = eai_rc_render_html('JobListingListWrapper', $props);
    eai_render_template('templates/EAI-job-listing-list.php', [
      'html' => is_wp_error($result) ? '' : $result['html'],
      'error' => is_wp_error($result) ? $result : null,
    ]);
  }
}
