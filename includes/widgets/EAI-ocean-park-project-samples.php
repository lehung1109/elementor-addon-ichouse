<?php

class EAI_Ocean_Park_Project_Samples_Widget extends \Elementor\Widget_Base
{
  private const TEMPLATE = 'templates/EAI-ocean-park-project-samples.php';

  public function get_name(): string
  {
    return 'eai_ocean_park_project_samples_widget';
  }

  public function get_title(): string
  {
    return esc_html__('ICHouse — Ocean Park: Dự án mẫu', 'eai');
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
    return ['ocean park', 'vinhomes', 'projects', 'samples', 'du an mau', 'filter', 'eai', 'ichouse'];
  }

  /** @return array<string, mixed> */
  private function get_select2_ajax_config(string $post_type): array
  {
    return [
      'url' => add_query_arg([
        'action' => 'eai_ocean_park_project_samples_search_posts',
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
      'label' => esc_html__('Nội dung & Chọn bài viết', 'eai'),
      'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
    ]);

    $this->add_control('class_name', [
      'label' => esc_html__('Class tùy chỉnh', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => '',
    ]);

    $this->add_control('eyebrow', [
      'label' => esc_html__('Eyebrow (Nhãn trên tiêu đề)', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => 'THƯ VIỆN MẪU THIẾT KẾ & CÔNG TRÌNH THI CÔNG THỰC TẾ TẠI VINHOMES OCEAN PARK',
      'label_block' => true,
    ]);

    $this->add_control('title', [
      'label' => esc_html__('Tiêu đề chính', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => 'Mẫu Thiết Kế & Thi Công Nội Thất Chung Cư Vinhomes Ocean Park 1 • 2 • 3 Theo Từng Phân Khu',
      'label_block' => true,
    ]);

    $this->add_control('description', [
      'label' => esc_html__('Mô tả ngắn', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXTAREA,
      'rows' => 3,
      'default' => 'Khám phá bộ sưu tập mặt bằng bố trí nội thất 2D, phối cảnh 3D Bespoke và công trình thực tế do đội ngũ KTS IC HOUSE (Văn phòng Số 01 Voi Phục – Nhà mẫu Sao Biển 23 – Nhà xưởng 2.000m² Như Quỳnh) thiết kế thi công trọn gói cho từng mã căn hộ tại Chung cư Ocean Park 1 (The London, The Beverly, The Paris), Ocean Park 2 (LUMIÈRE SpringBay, Masteri Trinity Square) và Dự án căn hộ Ocean Park 3.',
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
        'description' => esc_html__('Chọn các bài publish. Thứ tự lựa chọn là thứ tự hiển thị; ảnh đại diện, tiêu đề, mô tả ngắn, liên kết và các nhãn phân khu/loại căn được lấy tự động từ bài.', 'eai'),
        'select2options' => [
          'maximumSelectionLength' => 24,
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

    $this->add_control('mark_as_real_project', [
      'label' => esc_html__('Đánh dấu là dự án thực tế', 'eai'),
      'type' => \Elementor\Controls_Manager::SWITCHER,
      'label_on' => esc_html__('Có', 'eai'),
      'label_off' => esc_html__('Không', 'eai'),
      'return_value' => 'yes',
      'default' => 'yes',
    ]);

    $this->add_control('show_sample_when_empty', [
      'label' => esc_html__('Hiển thị 7 dự án mẫu mặc định khi chưa chọn bài viết', 'eai'),
      'type' => \Elementor\Controls_Manager::SWITCHER,
      'label_on' => esc_html__('Có', 'eai'),
      'label_off' => esc_html__('Không', 'eai'),
      'return_value' => 'yes',
      'default' => 'yes',
    ]);

    $this->end_controls_section();

    $this->start_controls_section('section_metadata', [
      'label' => esc_html__('Bộ lọc Phân khu, Loại căn & Thông số', 'eai'),
      'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
    ]);

    $taxonomy_options = ['' => esc_html__('Không hiển thị', 'eai')] + eai_get_public_taxonomy_options();
    $acf_options = ['' => esc_html__('Không dùng ACF', 'eai')] + eai_get_acf_field_options_by_types(['text', 'textarea', 'select', 'radio']);

    $this->add_control('all_zones_label', [
      'label' => esc_html__('Nhãn tab Tất cả phân khu', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => 'Tất cả phân khu',
    ]);

    $this->add_control('zone_taxonomy', [
      'label' => esc_html__('Taxonomy Phân khu (Zone Tabs)', 'eai'),
      'type' => \Elementor\Controls_Manager::SELECT,
      'options' => $taxonomy_options,
      'default' => '',
    ]);

    $this->add_control('all_unit_types_label', [
      'label' => esc_html__('Nhãn tab Tất cả loại căn', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => 'Tất cả loại căn',
    ]);

    $this->add_control('unit_type_taxonomy', [
      'label' => esc_html__('Taxonomy Loại căn (Unit Type Tabs)', 'eai'),
      'type' => \Elementor\Controls_Manager::SELECT,
      'options' => $taxonomy_options,
      'default' => '',
    ]);

    $this->add_control('tower_taxonomy', [
      'label' => esc_html__('Taxonomy Tòa căn hộ (VD: Tòa LD1 • LD2)', 'eai'),
      'type' => \Elementor\Controls_Manager::SELECT,
      'options' => $taxonomy_options,
      'default' => '',
    ]);

    $this->add_control('tower_field', [
      'label' => esc_html__('ACF Tòa căn hộ (khi không dùng Taxonomy)', 'eai'),
      'type' => \Elementor\Controls_Manager::SELECT,
      'options' => $acf_options,
      'default' => '',
    ]);

    $this->add_control('area_taxonomy', [
      'label' => esc_html__('Taxonomy Diện tích', 'eai'),
      'type' => \Elementor\Controls_Manager::SELECT,
      'options' => $taxonomy_options,
      'default' => '',
    ]);

    $this->add_control('area_field', [
      'label' => esc_html__('ACF Diện tích (khi không dùng Taxonomy)', 'eai'),
      'type' => \Elementor\Controls_Manager::SELECT,
      'options' => $acf_options,
      'default' => '',
    ]);

    $this->add_control('apartment_code_taxonomy', [
      'label' => esc_html__('Taxonomy Mã căn', 'eai'),
      'type' => \Elementor\Controls_Manager::SELECT,
      'options' => $taxonomy_options,
      'default' => '',
    ]);

    $this->add_control('apartment_code_field', [
      'label' => esc_html__('ACF Mã căn (khi không dùng Taxonomy)', 'eai'),
      'type' => \Elementor\Controls_Manager::SELECT,
      'options' => $acf_options,
      'default' => '',
    ]);

    $this->add_control('style_taxonomy', [
      'label' => esc_html__('Taxonomy Phong cách', 'eai'),
      'type' => \Elementor\Controls_Manager::SELECT,
      'options' => $taxonomy_options,
      'default' => '',
    ]);

    $this->add_control('style_field', [
      'label' => esc_html__('ACF Phong cách (khi không dùng Taxonomy)', 'eai'),
      'type' => \Elementor\Controls_Manager::SELECT,
      'options' => $acf_options,
      'default' => '',
    ]);

    $this->add_control('budget_taxonomy', [
      'label' => esc_html__('Taxonomy Mức đầu tư tham khảo', 'eai'),
      'type' => \Elementor\Controls_Manager::SELECT,
      'options' => $taxonomy_options,
      'default' => '',
    ]);

    $this->add_control('budget_field', [
      'label' => esc_html__('ACF Mức đầu tư (khi không dùng Taxonomy)', 'eai'),
      'type' => \Elementor\Controls_Manager::SELECT,
      'options' => $acf_options,
      'default' => '',
    ]);

    $this->end_controls_section();

    $this->start_controls_section('section_cta', [
      'label' => esc_html__('Banner CTA cuối mục', 'eai'),
      'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
    ]);

    $this->add_control('cta_banner_title', [
      'label' => esc_html__('Tiêu đề Banner CTA', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => 'Bạn muốn xem mặt bằng 2D, phối cảnh 3D đúng mã căn & tham quan trực tiếp Nhà mẫu tại Sao Biển 23?',
      'label_block' => true,
    ]);

    $this->add_control('cta_banner_description', [
      'label' => esc_html__('Mô tả Banner CTA', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXTAREA,
      'rows' => 2,
      'default' => 'Văn phòng KTS IC HOUSE tại Số 01 Voi Phục Gia Lâm đã lưu trữ sẵn mặt bằng kỹ thuật toàn bộ các tòa LD1, LD2, LD3 (The London), BE1–BE4 (The Beverly), The Paris, LUMIÈRE SpringBay, Masteri Trinity Square và Ocean Park 3. Đăng ký nhận bản vẽ miễn phí và đặt lịch xem Nhà mẫu ngay trong KĐT!',
    ]);

    $this->add_control('cta_banner_button_label', [
      'label' => esc_html__('Nhãn nút Banner CTA', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => 'Nhận bản vẽ theo mã căn & Xem nhà mẫu',
      'label_block' => true,
    ]);

    $this->add_control('cta_banner_button_link', [
      'label' => esc_html__('Liên kết nút Banner CTA', 'eai'),
      'type' => \Elementor\Controls_Manager::URL,
      'options' => ['url', 'is_external', 'nofollow'],
      'default' => [
        'url' => '#dang-ky-tu-van',
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
    $props = eai_ocean_park_project_samples_get_rc_props($settings);

    if ((eai_is_elementor_edit_mode() || ($settings['show_sample_when_empty'] ?? 'yes') === 'yes') && empty($props['featuredProjects'])) {
      $props = eai_ocean_park_project_samples_get_editor_sample_props($settings);
    } elseif (empty($props['featuredProjects'])) {
      eai_render_template(self::TEMPLATE, ['empty' => true]);
      return;
    }

    $result = eai_rc_render_html('OceanParkProjectSamplesWrapper', $props);
    eai_render_template(self::TEMPLATE, [
      'html' => is_wp_error($result) ? '' : $result['html'],
      'error' => is_wp_error($result) ? $result : null,
    ]);
  }
}
