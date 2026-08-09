<?php

class EAI_Project_Category_Gallery_Widget extends \Elementor\Widget_Base
{
  public function get_name(): string { return 'eai_project_category_gallery_widget'; }
  public function get_title(): string { return esc_html__('ICHouse — Thư viện dự án theo danh mục', 'eai'); }
  public function get_icon(): string { return 'eicon-gallery-grid'; }
  public function get_categories(): array { return eai_get_widget_categories(); }
  public function get_keywords(): array { return ['project', 'category', 'gallery', 'filter', 'eai', 'ichouse']; }

  protected function register_controls(): void
  {
    $this->start_controls_section('section_query', [
      'label' => esc_html__('Query dự án', 'eai'),
      'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
    ]);
    $this->add_control('post_type', [
      'label' => esc_html__('Post type', 'eai'), 'type' => \Elementor\Controls_Manager::SELECT,
      'options' => eai_get_public_post_type_options(), 'default' => 'post',
    ]);
    $this->add_control('taxonomy', [
      'label' => esc_html__('Taxonomy danh mục', 'eai'), 'type' => \Elementor\Controls_Manager::SELECT,
      'options' => eai_get_public_taxonomy_options(), 'default' => 'category',
    ]);
    foreach (get_taxonomies(['public' => true], 'objects') as $taxonomy) {
      if (empty($taxonomy->name)) { continue; }
      $this->add_control('include_terms_' . $taxonomy->name, [
        'label' => esc_html__('Danh mục hiển thị', 'eai'), 'type' => \Elementor\Controls_Manager::SELECT2,
        'options' => eai_get_taxonomy_terms_as_options($taxonomy->name), 'multiple' => true,
        'condition' => ['taxonomy' => $taxonomy->name],
      ]);
    }
    $taxonomy_options = ['' => esc_html__('Không hiển thị', 'eai')] + eai_get_public_taxonomy_options();
    $this->add_control('investor_taxonomy', [
      'label' => esc_html__('Taxonomy Chủ đầu tư', 'eai'), 'type' => \Elementor\Controls_Manager::SELECT,
      'options' => $taxonomy_options, 'default' => '',
    ]);
    $this->add_control('model_taxonomy', [
      'label' => esc_html__('Taxonomy Mô hình', 'eai'), 'type' => \Elementor\Controls_Manager::SELECT,
      'options' => $taxonomy_options, 'default' => '',
    ]);
    $this->add_control('page_size', [
      'label' => esc_html__('Số dự án mỗi trang', 'eai'), 'type' => \Elementor\Controls_Manager::NUMBER,
      'default' => 6, 'min' => 1, 'max' => 24,
    ]);
    $this->add_control('orderby', [
      'label' => esc_html__('Sắp xếp theo', 'eai'), 'type' => \Elementor\Controls_Manager::SELECT,
      'default' => 'date', 'options' => ['date' => 'Ngày đăng', 'modified' => 'Ngày cập nhật', 'title' => 'Tiêu đề', 'menu_order' => 'Menu order'],
    ]);
    $this->add_control('order', [
      'label' => esc_html__('Thứ tự', 'eai'), 'type' => \Elementor\Controls_Manager::SELECT,
      'default' => 'DESC', 'options' => ['DESC' => 'Giảm dần', 'ASC' => 'Tăng dần'],
    ]);
    $this->add_control('image_size', [
      'label' => esc_html__('Kích thước ảnh', 'eai'), 'type' => \Elementor\Controls_Manager::SELECT,
      'default' => 'large', 'options' => eai_get_image_size_options(),
    ]);
    $this->add_control('load_more_label', [
      'label' => esc_html__('Nhãn xem thêm', 'eai'), 'type' => \Elementor\Controls_Manager::TEXT,
      'default' => 'XEM THÊM',
    ]);
    $this->add_control('class_name', [
      'label' => esc_html__('Class tùy chỉnh', 'eai'), 'type' => \Elementor\Controls_Manager::TEXT,
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
    $props = eai_project_category_gallery_get_rc_props($settings, (string) $this->get_id());
    if (eai_is_elementor_edit_mode() && empty($props['items'])) {
      $props = eai_project_category_gallery_get_editor_sample_props($settings, (string) $this->get_id());
    } elseif (empty($props['items']) && count($props['filters']) <= 1) {
      eai_render_template('templates/EAI-project-category-gallery.php', ['html' => '', 'error' => null, 'empty' => true]);
      return;
    }

    $result = eai_rc_render_html('ProjectCategoryGalleryWrapper', $props);
    eai_render_template('templates/EAI-project-category-gallery.php', [
      'html' => is_wp_error($result) ? '' : $result['html'],
      'error' => is_wp_error($result) ? $result : null,
    ]);
  }
}
