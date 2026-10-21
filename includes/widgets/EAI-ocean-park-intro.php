<?php

class EAI_Ocean_Park_Intro_Widget extends \Elementor\Widget_Base
{
  private const TEMPLATE = 'templates/EAI-ocean-park-intro.php';

  public function get_name(): string
  {
    return 'eai_ocean_park_intro_widget';
  }

  public function get_title(): string
  {
    return esc_html__('ICHouse — Ocean Park: Giới thiệu', 'eai');
  }

  public function get_icon(): string
  {
    return 'eicon-call-to-action';
  }

  public function get_categories(): array
  {
    return eai_get_widget_categories();
  }

  public function get_keywords(): array
  {
    return ['ocean park', 'vinhomes', 'intro', 'gioi thieu', 'noi that', 'eai', 'ichouse'];
  }

  protected function register_controls(): void
  {
    $this->start_controls_section('section_content', [
      'label' => esc_html__('Nội dung chính', 'eai'),
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
      'default' => 'IC HOUSE LUXURY — NỘI THẤT MAY ĐO ĐẠI ĐÔ THỊ OCEAN PARK 1 • 2 • 3',
      'label_block' => true,
    ]);

    $this->add_control('title', [
      'label' => esc_html__('Tiêu đề (H1)', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => 'Chuyên Thiết Kế & Thi Công Nội Thất Cao Cấp Tại Vinhomes Ocean Park',
      'label_block' => true,
    ]);

    $this->add_control('lead_html', [
      'label' => esc_html__('Đoạn mở đầu (HTML)', 'eai'),
      'type' => \Elementor\Controls_Manager::WYSIWYG,
      'default' => '<p><strong>IC HOUSE</strong> định vị giải pháp tư vấn mặt bằng chuyên sâu, thiết kế 3D Bespoke và sản xuất – thi công nội thất trọn gói phân khúc <strong>Trung – Cao cấp & Luxury</strong> cho các tòa căn hộ tại <strong>Ocean Park 1</strong> (<strong>The Paris Ocean Park 1</strong>, <strong>The London Ocean Park 1</strong> – <strong>Tòa LD1</strong>, <strong>Tòa LD2</strong>, <strong>Tòa LD3</strong>; <strong>The Beverly Ocean Park 1</strong> – <strong>Tòa BE1</strong>, <strong>Tòa BE2</strong>, <strong>Tòa BE3</strong>, <strong>Tòa BE4</strong>), <strong>Ocean Park 2</strong> (<strong>LUMIÈRE SpringBay Ocean Park 2</strong>, <strong>Masteri Trinity Square Ocean Park 2</strong>) và <strong>Dự án căn hộ cao tầng Ocean Park 3</strong>.</p><p>Sở hữu <strong>Nhà mẫu ngay tại Sao Biển 23 Vinhomes Ocean Park</strong>, <strong>Văn phòng tại Số 01 Voi Phục Gia Lâm</strong> và <strong>Nhà máy sản xuất 2.000m² tại Như Quỳnh</strong> sát vách đại đô thị, IC HOUSE cam kết tiến độ chuẩn xác và chất lượng hoàn thiện tinh xảo cho từng căn hộ.</p>',
    ]);

    $this->add_control('subdivision_badges', [
      'label' => esc_html__('Các phân khu trọng điểm (mỗi dòng 1 badge)', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXTAREA,
      'rows' => 6,
      'default' => "The London (Tòa LD1 • LD2 • LD3)\nThe Beverly (Tòa BE1 • BE2 • BE3 • BE4)\nThe Paris Ocean Park 1\nLUMIÈRE SpringBay Ocean Park 2\nMasteri Trinity Square Ocean Park 2\nCăn hộ cao tầng Ocean Park 3",
    ]);

    $this->add_control('primary_cta_label', [
      'label' => esc_html__('Nhãn nút CTA chính', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => 'Gọi tư vấn: 0869 855 885',
      'label_block' => true,
    ]);

    $this->add_control('primary_cta_link', [
      'label' => esc_html__('Liên kết nút CTA chính', 'eai'),
      'type' => \Elementor\Controls_Manager::URL,
      'options' => ['url', 'is_external', 'nofollow'],
      'default' => ['url' => 'tel:0869855885', 'is_external' => false, 'nofollow' => false],
      'label_block' => true,
    ]);

    $this->add_control('secondary_cta_label', [
      'label' => esc_html__('Nhãn nút CTA phụ', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => 'Nhận báo giá theo mã căn',
      'label_block' => true,
    ]);

    $this->add_control('secondary_cta_link', [
      'label' => esc_html__('Liên kết nút CTA phụ', 'eai'),
      'type' => \Elementor\Controls_Manager::URL,
      'options' => ['url', 'is_external', 'nofollow'],
      'default' => ['url' => '#dang-ky-tu-van', 'is_external' => false, 'nofollow' => false],
      'label_block' => true,
    ]);

    $this->end_controls_section();

    $this->start_controls_section('section_highlights', [
      'label' => esc_html__('Thẻ điểm nhấn (Highlights)', 'eai'),
      'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
    ]);

    $hl_repeater = new \Elementor\Repeater();
    $hl_repeater->add_control('label', [
      'label' => esc_html__('Nhãn', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => 'Thiết kế 3D Bespoke',
      'label_block' => true,
    ]);
    $hl_repeater->add_control('value', [
      'label' => esc_html__('Giá trị nổi bật', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => '250.000đ – 350.000đ/m²',
      'label_block' => true,
    ]);
    $hl_repeater->add_control('note', [
      'label' => esc_html__('Ghi chú phụ', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => '',
      'label_block' => true,
    ]);

    $this->add_control('highlights', [
      'label' => esc_html__('Danh sách thẻ điểm nhấn', 'eai'),
      'type' => \Elementor\Controls_Manager::REPEATER,
      'fields' => $hl_repeater->get_controls(),
      'title_field' => '{{{ label }}} — {{{ value }}}',
      'default' => [
        ['label' => 'Thiết kế 3D Bespoke', 'value' => '250.000đ – 350.000đ/m²', 'note' => 'KTS trưởng 5–15 năm kinh nghiệm chủ trì'],
        ['label' => 'Thi công trọn gói Cao cấp', 'value' => 'Từ 120 triệu/căn', 'note' => 'Gói Luxury đồng bộ: 180 – 260 triệu/căn'],
        ['label' => 'Ưu đãi ký HĐ thi công', 'value' => 'Miễn phí 100% TK', 'note' => 'Hoàn trả toàn bộ phí thiết kế 3D & hồ sơ kỹ thuật'],
        ['label' => 'Nhà mẫu & Xưởng tại chỗ', 'value' => 'Sao Biển 23 OCP', 'note' => 'Bảo hành 2 năm • Bảo trì 5 năm tận căn hộ'],
      ],
    ]);

    $this->end_controls_section();

    $this->start_controls_section('section_steps', [
      'label' => esc_html__('Quy trình 3 bước', 'eai'),
      'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
    ]);

    $step_repeater = new \Elementor\Repeater();
    $step_repeater->add_control('number', [
      'label' => esc_html__('Số thứ tự (VD: 01)', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => '01',
    ]);
    $step_repeater->add_control('title', [
      'label' => esc_html__('Tiêu đề bước', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => '',
      'label_block' => true,
    ]);
    $step_repeater->add_control('description', [
      'label' => esc_html__('Mô tả bước', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXTAREA,
      'rows' => 3,
      'default' => '',
    ]);

    $this->add_control('steps', [
      'label' => esc_html__('Các bước thực hiện', 'eai'),
      'type' => \Elementor\Controls_Manager::REPEATER,
      'fields' => $step_repeater->get_controls(),
      'title_field' => '{{{ number }}} — {{{ title }}}',
      'default' => [
        ['number' => '01', 'title' => 'Khảo sát thực tế tại tòa trong 15 phút', 'description' => 'KTS từ văn phòng Số 01 Voi Phục có mặt đo đạc trực tiếp từng góc căn hộ Chung cư Ocean Park 1, Ocean Park 2, Ocean Park 3 và tư vấn công năng tại chỗ.'],
        ['number' => '02', 'title' => 'Thiết kế 3D & Trải nghiệm Nhà mẫu Sao Biển 23', 'description' => 'Cá nhân hóa phong cách Modern Luxury, Japandi, Neoclassic, Wabi Sabi; mời gia chủ kiểm chứng trực tiếp cốt gỗ An Cường, đá Vicostone và sơn Inchem tại nhà mẫu.'],
        ['number' => '03', 'title' => 'Sản xuất giá xưởng & Thi công đúng cam kết', 'description' => 'Gia công CNC tự động tại nhà máy Ngọc Trai – Như Quỳnh ngay sát dự án, báo giá minh bạch không phát sinh, hỗ trợ trọn gói thủ tục ban quản lý tòa nhà.'],
      ],
    ]);

    $this->end_controls_section();
  }

  protected function render(): void
  {
    $settings = $this->get_settings_for_display();
    $props = eai_ocean_park_intro_get_rc_props($settings);

    if (eai_is_elementor_edit_mode() && eai_ocean_park_intro_props_are_empty($props)) {
      $props = eai_ocean_park_intro_get_editor_sample_props($settings);
    } elseif (eai_ocean_park_intro_props_are_empty($props)) {
      eai_render_template(self::TEMPLATE, ['empty' => true]);
      return;
    }

    $result = eai_rc_render_html('OceanParkIntro', $props);
    eai_render_template(self::TEMPLATE, [
      'html' => is_wp_error($result) ? '' : $result['html'],
      'error' => is_wp_error($result) ? $result : null,
    ]);
  }
}
