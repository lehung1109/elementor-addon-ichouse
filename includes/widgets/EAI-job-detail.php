<?php

class EAI_Job_Detail_Widget extends \Elementor\Widget_Base
{
  public function get_name(): string { return 'eai_job_detail_widget'; }
  public function get_title(): string { return esc_html__('ICHouse — Chi tiết tuyển dụng', 'eai'); }
  public function get_icon(): string { return 'eicon-post-content'; }
  public function get_categories(): array { return eai_get_widget_categories(); }
  public function get_keywords(): array { return ['job', 'detail', 'recruitment', 'tuyển dụng', 'eai', 'ichouse']; }

  protected function register_controls(): void
  {
    $this->start_controls_section('section_content', ['label' => esc_html__('Nội dung', 'eai'), 'tab' => \Elementor\Controls_Manager::TAB_CONTENT]);
    foreach ([
      'title' => ['Tiêu đề', 'ICHOUSE tuyển dụng Content Creator - HCM'],
      'vacancies' => ['Số lượng cần tuyển', '1'],
      'salary' => ['Mức lương', '10.000.000 - 12.000.000'],
      'employment_type' => ['Tính chất công việc', 'Toàn thời gian'],
      'application_deadline' => ['Hạn ứng tuyển', '31/03/2026'],
      'apply_label' => ['Nhãn ứng tuyển', 'Ứng tuyển'],
      'sidebar_title' => ['Tiêu đề sidebar', 'Ứng tuyển khác'],
    ] as $name => [$label, $default]) {
      $this->add_control($name, ['label' => esc_html__($label, 'eai'), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => $default]);
    }
    $this->add_control('apply_link', [
      'label' => esc_html__('Liên kết ứng tuyển', 'eai'),
      'type' => \Elementor\Controls_Manager::URL,
      'options' => ['url', 'is_external', 'nofollow'],
      'default' => ['url' => '/ung-tuyen/content-creator', 'is_external' => false, 'nofollow' => false],
    ]);

    $repeater = new \Elementor\Repeater();
    $repeater->add_control('title', ['label' => esc_html__('Tiêu đề mục', 'eai'), 'type' => \Elementor\Controls_Manager::TEXT]);
    $repeater->add_control('items', ['label' => esc_html__('Các gạch đầu dòng', 'eai'), 'type' => \Elementor\Controls_Manager::TEXTAREA, 'description' => esc_html__('Mỗi dòng là một gạch đầu dòng.', 'eai')]);
    $this->add_control('sections', [
      'label' => esc_html__('Các mục nội dung', 'eai'),
      'type' => \Elementor\Controls_Manager::REPEATER,
      'fields' => $repeater->get_controls(),
      'default' => [
        [
          'title' => 'Mô tả công việc',
          'items' => "Lên ý tưởng, kịch bản và trực tiếp sản xuất nội dung trên các nền tảng truyền thông của công ty.\nPhối hợp cùng đội ngũ thiết kế để xây dựng hình ảnh, video và bài viết nhất quán với định hướng thương hiệu.\nTheo dõi hiệu quả nội dung, cập nhật xu hướng và đề xuất phương án cải thiện tương tác.",
        ],
        [
          'title' => 'Quyền lợi được hưởng',
          'items' => "Thu nhập cạnh tranh theo năng lực và hiệu quả công việc.\nĐược tham gia đầy đủ các chế độ bảo hiểm, nghỉ phép và hoạt động nội bộ.\nMôi trường sáng tạo, chủ động và có cơ hội phát triển cùng các dự án thực tế.",
        ],
        [
          'title' => 'Yêu cầu công việc',
          'items' => "Có kinh nghiệm sáng tạo nội dung cho website và mạng xã hội.\nKhả năng viết, biên tập và kể chuyện tốt; ưu tiên ứng viên có kỹ năng quay dựng cơ bản.\nChủ động, có trách nhiệm và phối hợp nhóm hiệu quả.",
        ],
        [
          'title' => 'Thông tin liên hệ',
          'items' => "Gửi CV và portfolio về email tuyển dụng của công ty.\nTiêu đề email: Content Creator - Họ và tên.",
        ],
      ],
    ]);
    $this->end_controls_section();

    $this->start_controls_section('section_sidebar', ['label' => esc_html__('Nguồn sidebar', 'eai'), 'tab' => \Elementor\Controls_Manager::TAB_CONTENT]);
    $this->add_control('employment_type_taxonomy', ['label' => esc_html__('Taxonomy loại hình làm việc', 'eai'), 'type' => \Elementor\Controls_Manager::SELECT, 'options' => ['' => esc_html__('Không hiển thị', 'eai')] + eai_get_public_taxonomy_options(), 'default' => '']);
    $this->add_control('location_field', ['label' => esc_html__('ACF vị trí làm việc', 'eai'), 'type' => \Elementor\Controls_Manager::SELECT, 'options' => ['' => esc_html__('Không hiển thị', 'eai')] + eai_get_acf_field_options_by_types(['text', 'textarea', 'select', 'radio']), 'default' => '']);
    $this->add_control('class_name', ['label' => esc_html__('Class tùy chỉnh', 'eai'), 'type' => \Elementor\Controls_Manager::TEXT, 'default' => '']);
    $this->end_controls_section();
  }

  protected function render(): void
  {
    $settings = $this->get_settings_for_display();
    $current_id = 0;
    if (is_singular()) {
      $current_id = (int) get_queried_object_id();
      if ($current_id <= 0) {
        $current_id = (int) get_the_ID();
      }
    }
    $props = eai_job_detail_get_rc_props($current_id, $settings);
    if (eai_is_elementor_edit_mode() && eai_job_detail_props_are_empty($props)) {
      $props = eai_job_detail_get_editor_sample_props($settings);
    } elseif (eai_job_detail_props_are_empty($props)) {
      eai_render_template('templates/EAI-job-detail.php', ['html' => '', 'error' => null, 'empty' => true]);
      return;
    }

    $result = eai_rc_render_html('JobDetail', $props);
    eai_render_template('templates/EAI-job-detail.php', [
      'html' => is_wp_error($result) ? '' : $result['html'],
      'error' => is_wp_error($result) ? $result : null,
    ]);
  }
}
