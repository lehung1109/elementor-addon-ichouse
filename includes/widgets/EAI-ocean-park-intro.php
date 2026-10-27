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
      'default' => 'IC HOUSE LUXURY — ĐƠN VỊ CÓ NHÀ MẪU TRONG OCEAN PARK • VĂN PHÒNG & NHÀ XƯỞNG SÁT KHU ĐÔ THỊ',
      'label_block' => true,
    ]);

    $this->add_control('title', [
      'label' => esc_html__('Tiêu đề (H1)', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => 'Thiết Kế & Thi Công Nội Thất Vinhomes Ocean Park Trọn Gói Cao Cấp — Chuẩn May Đo Bespoke',
      'label_block' => true,
    ]);

    $this->add_control('lead_html', [
      'label' => esc_html__('Đoạn mở đầu (HTML)', 'eai'),
      'type' => \Elementor\Controls_Manager::WYSIWYG,
      'default' => '<p><strong>IC HOUSE</strong> là đơn vị <strong>thiết kế và thi công nội thất Vinhomes Ocean Park trọn gói cao cấp</strong> uy tín hàng đầu tại Gia Lâm – Hà Nội, chuyên tư vấn mặt bằng công năng 2D chuyên sâu, thiết kế phối cảnh 3D Bespoke và sản xuất – lắp đặt hoàn thiện nội thất phân khúc <strong>Trung – Cao cấp &amp; Luxury</strong> cho toàn bộ căn hộ <strong>Studio, 1PN, 1PN+1, 2PN, 2PN+1, 3PN căn góc, Duplex &amp; Penthouse</strong> tại <strong>Ocean Park 1</strong> (<strong>Chung cư Ocean Park 1</strong>: <strong>The Paris Ocean Park 1</strong>, <strong>The London Ocean Park 1</strong> – <strong>Tòa LD1</strong>, <strong>Tòa LD2</strong>, <strong>Tòa LD3</strong>; <strong>The Beverly Ocean Park 1</strong> – <strong>Tòa BE1</strong>, <strong>Tòa BE2</strong>, <strong>Tòa BE3</strong>, <strong>Tòa BE4</strong>; Masteri Waterfront, The Zurich, The Pavilion, The Zenpark), <strong>Ocean Park 2</strong> (<strong>LUMIÈRE SpringBay Ocean Park 2</strong>, <strong>Masteri Trinity Square Ocean Park 2</strong>) và <strong>Dự án căn hộ cao tầng Ocean Park 3</strong>.</p><p>Khác biệt hoàn toàn với các đơn vị nội thất ở xa hay trung gian thương mại, <strong>IC HOUSE sở hữu hệ sinh thái 3 vị trí địa lý đắc địa bao quanh đại đô thị</strong>: <strong>(1) Nhà mẫu nội thất thực tế nằm NGAY TRONG khu đô thị Vinhomes Ocean Park (Sao Biển 23, Gia Lâm, Hà Nội)</strong> để cư dân trực tiếp xem mẫu vật liệu gỗ MDF lõi xanh chống ẩm An Cường, đá thạch anh Vicostone, sơn Inchem 5 lớp &amp; phụ kiện Blum/Hafele; <strong>(2) Văn phòng công ty GẦN khu đô thị (Số 01 Voi Phục, Gia Lâm, Hà Nội)</strong> sẵn sàng khảo sát đo đạc căn hộ sau 15 phút; và <strong>(3) Nhà xưởng – Nhà máy sản xuất nội thất 2.000m² GẦN Ocean Park (Thôn Ngọc Trai, Xã Như Quỳnh, Hưng Yên)</strong> mang lại báo giá gốc tại xưởng tiết kiệm 15–20%, cam kết chuẩn tiến độ bàn giao và bảo hành – bảo trì tận căn hộ trong 24 giờ.</p>',
    ]);

    $this->add_control('subdivision_badges', [
      'label' => esc_html__('Các phân khu trọng điểm (mỗi dòng 1 badge)', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXTAREA,
      'rows' => 9,
      'default' => "Nhà mẫu: Sao Biển 23 Vinhomes Ocean Park\nVăn phòng: Số 01 Voi Phục – Gia Lâm\nNhà xưởng 2.000m²: Ngọc Trai – Như Quỳnh (Cách OCP 10 phút)\nThe London Ocean Park 1 (Tòa LD1 • LD2 • LD3)\nThe Beverly Ocean Park 1 (Tòa BE1 • BE2 • BE3 • BE4)\nThe Paris Ocean Park 1 — Chung cư Ocean Park 1\nLUMIÈRE SpringBay Ocean Park 2\nMasteri Trinity Square Ocean Park 2\nCăn hộ cao tầng Ocean Park 3",
    ]);

    $this->add_control('primary_cta_label', [
      'label' => esc_html__('Nhãn nút CTA chính', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => 'Gọi KTS Trưởng: 0978 305 740',
      'label_block' => true,
    ]);

    $this->add_control('primary_cta_link', [
      'label' => esc_html__('Liên kết nút CTA chính', 'eai'),
      'type' => \Elementor\Controls_Manager::URL,
      'options' => ['url', 'is_external', 'nofollow'],
      'default' => ['url' => 'tel:0978305740', 'is_external' => false, 'nofollow' => false],
      'label_block' => true,
    ]);

    $this->add_control('secondary_cta_label', [
      'label' => esc_html__('Nhãn nút CTA phụ', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => 'Nhận báo giá & Đặt lịch xem Nhà mẫu',
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
      'default' => 'Nhà mẫu TRONG Ocean Park',
      'label_block' => true,
    ]);
    $hl_repeater->add_control('value', [
      'label' => esc_html__('Giá trị nổi bật', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => 'Sao Biển 23 OCP',
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
        ['label' => 'Nhà mẫu TRONG Ocean Park', 'value' => 'Sao Biển 23 OCP', 'note' => 'Xem thực tế gỗ An Cường, đá Vicostone, tủ bếp cánh kính ngay nội khu'],
        ['label' => 'Văn phòng GẦN khu đô thị', 'value' => 'Số 01 Voi Phục', 'note' => 'KTS trưởng 5–15 năm kinh nghiệm có mặt khảo sát tại tòa sau 15 phút'],
        ['label' => 'Nhà xưởng 2.000m² GẦN OCP', 'value' => 'Giá gốc tại xưởng', 'note' => 'Xưởng Ngọc Trai – Như Quỳnh cách 10 phút, tiết kiệm 15–20% chi phí'],
        ['label' => 'Thi công trọn gói Luxury', 'value' => 'Miễn phí 100% TK', 'note' => 'Trọn gói từ 120 triệu/căn (Luxury 180–260tr) • Bảo hành 2 năm, bảo trì 5 năm'],
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
        ['number' => '01', 'title' => 'Văn phòng Số 01 Voi Phục gần KĐT — Khảo sát căn hộ trong 15 phút', 'description' => 'Ngay khi nhận bàn giao nhà tại Chung cư Ocean Park 1 (The London LD1–LD3, The Beverly BE1–BE4, The Paris), Ocean Park 2 (LUMIÈRE SpringBay, Masteri Trinity Square) và Ocean Park 3, KTS trưởng từ văn phòng Số 01 Voi Phục Gia Lâm có mặt đo đạc laser và tư vấn cải tạo mặt bằng 2D tại chỗ.'],
        ['number' => '02', 'title' => 'Thiết kế 3D Bespoke & Trải nghiệm Nhà mẫu TRONG Ocean Park (Sao Biển 23)', 'description' => 'May đo cá nhân hóa phong cách Modern Luxury, Japandi, Wabi Sabi, Neoclassic Tân cổ điển, Indochine; mời gia chủ ghé Nhà mẫu nội thất tại Sao Biển 23 Vinhomes Ocean Park kiểm chứng trực tiếp cốt gỗ MDF lõi xanh An Cường, Plywood Veneer óc chó, đá Vicostone và sơn Inchem.'],
        ['number' => '03', 'title' => 'Sản xuất tại Nhà xưởng 2.000m² gần Ocean Park & Thi công trọn gói chuẩn BQL', 'description' => 'Gia công CNC tự động tại nhà xưởng Thôn Ngọc Trai – Như Quỳnh sát vách đại đô thị (mời cư dân nghiệm thu cốt mộc tại xưởng), bao trọn thủ tục xin phép thi công Ban quản lý Vinhomes & Masterise Homes, cam kết không phát sinh chi phí.'],
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
