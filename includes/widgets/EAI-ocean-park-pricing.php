<?php

class EAI_Ocean_Park_Pricing_Widget extends \Elementor\Widget_Base
{
  private const TEMPLATE = 'templates/EAI-ocean-park-pricing.php';

  public function get_name(): string
  {
    return 'eai_ocean_park_pricing_widget';
  }

  public function get_title(): string
  {
    return esc_html__('ICHouse — Ocean Park: Bảng giá', 'eai');
  }

  public function get_icon(): string
  {
    return 'eicon-price-table';
  }

  public function get_categories(): array
  {
    return eai_get_widget_categories();
  }

  public function get_keywords(): array
  {
    return ['ocean park', 'vinhomes', 'pricing', 'bang gia', 'chi phi', 'eai', 'ichouse'];
  }

  protected function register_controls(): void
  {
    $this->start_controls_section('section_header', [
      'label' => esc_html__('Tiêu đề & Gói chủ đạo (Featured Package)', 'eai'),
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
      'default' => 'BẢNG BÁO GIÁ GỐC TẠI NHÀ XƯỞNG 2.000M² GẦN OCEAN PARK — MINH BẠCH TỪNG HẠNG MỤC',
      'label_block' => true,
    ]);

    $this->add_control('title', [
      'label' => esc_html__('Tiêu đề chính', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => 'Báo Giá Thiết Kế & Thi Công Nội Thất Vinhomes Ocean Park Trọn Gói Cao Cấp 1 • 2 • 3',
      'label_block' => true,
    ]);

    $this->add_control('description', [
      'label' => esc_html__('Mô tả mở đầu', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXTAREA,
      'rows' => 3,
      'default' => 'Nhờ sở hữu Nhà máy sản xuất nội thất 2.000m² ngay tại Ngọc Trai – Như Quỳnh (cách Ocean Park chỉ 10 phút), Nhà mẫu nội khu tại Sao Biển 23 và Văn phòng KTS tại Số 01 Voi Phục Gia Lâm, IC HOUSE mang đến báo giá gốc tại xưởng tiết kiệm 15–20% so với thị trường, cam kết 100% vật liệu chính hãng An Cường, đá thạch anh Vicostone, sơn Inchem và phụ kiện Blum/Hafele.',
    ]);

    $this->add_control('featured_badge', [
      'label' => esc_html__('Badge gói chủ đạo', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => 'GÓI THI CÔNG NỘI THẤT TRỌN GÓI BESPOKE LUXURY — GIÁ XƯỞNG TRỰC TIẾP SÁT OCEAN PARK',
      'label_block' => true,
    ]);

    $this->add_control('featured_title', [
      'label' => esc_html__('Tiêu đề gói chủ đạo', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => 'Hoàn Thiện Nội Thất Căn Hộ Vinhomes Ocean Park Trọn Gói Cao Cấp (Từ 120 triệu/căn • Phổ biến 180 – 260 triệu/căn)',
      'label_block' => true,
    ]);

    $this->add_control('featured_price', [
      'label' => esc_html__('Mức giá nổi bật', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => '180 – 260 triệu',
    ]);

    $this->add_control('featured_unit', [
      'label' => esc_html__('Đơn vị / Loại căn áp dụng', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => '/ căn hộ 2PN – 2PN+1 hoàn thiện đồng bộ chuẩn Luxury tại xưởng IC HOUSE',
      'label_block' => true,
    ]);

    $this->add_control('featured_note', [
      'label' => esc_html__('Ghi chú gói chủ đạo', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXTAREA,
      'rows' => 2,
      'default' => 'Gói khởi điểm từ 120 triệu/căn (Studio/1PN). Miễn phí 100% phí thiết kế 3D Bespoke (250.000đ – 350.000đ/m²) và miễn phí khảo sát tại tòa khi ký hợp đồng thi công nội thất trọn gói.',
    ]);

    $this->add_control('featured_handover_saving_note', [
      'label' => esc_html__('Ghi chú tiết kiệm theo tiêu chuẩn bàn giao', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXTAREA,
      'rows' => 2,
      'default' => 'Đặc quyền tối ưu ngân sách theo tiêu chuẩn bàn giao: Khấu trừ & tiết kiệm ngay 35 – 60 triệu/căn cho các tòa đã có sẵn hệ tủ bếp, điều hòa âm trần & thiết bị cao cấp như Căn hộ The Beverly (BE1–BE4), Căn hộ The London (LD1–LD3), The Paris (Ocean Park 1), Căn hộ LUMIÈRE SpringBay và Dự án Masteri Trinity Square (Ocean Park 2).',
    ]);

    $this->add_control('primary_cta_label', [
      'label' => esc_html__('Nhãn nút CTA chính', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => 'Gọi nhận báo giá xưởng: 0869 855 885',
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
      'default' => 'Nhận bảng dự toán BOQ theo mã căn',
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

    $this->start_controls_section('section_estimates', [
      'label' => esc_html__('Dự toán theo Loại căn & Không gian', 'eai'),
      'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
    ]);

    $this->add_control('unit_estimates_title', [
      'label' => esc_html__('Tiêu đề bảng dự toán loại căn', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => 'Bảng Giá Dự Toán Thi Công Nội Thất Trọn Gói Theo Loại Căn Hộ Ocean Park',
      'label_block' => true,
    ]);

    $this->add_control('unit_estimates_subtitle', [
      'label' => esc_html__('Mô tả phụ bảng dự toán loại căn', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => 'Báo giá sản xuất trực tiếp tại Nhà xưởng IC HOUSE gần Ocean Park — Áp dụng cho Căn hộ The London, Căn hộ The Beverly, The Paris, Căn hộ LUMIÈRE SpringBay, Dự án Masteri Trinity Square & Căn hộ cao tầng Ocean Park 3',
      'label_block' => true,
    ]);

    $unit_repeater = new \Elementor\Repeater();
    $unit_repeater->add_control('unit_type', [
      'label' => esc_html__('Loại căn hộ', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => '2PN / 2PN+1',
    ]);
    $unit_repeater->add_control('area_range', [
      'label' => esc_html__('Khoảng diện tích', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => '58m² – 76m²',
    ]);
    $unit_repeater->add_control('price_range', [
      'label' => esc_html__('Ngân sách dự kiến', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => '180 – 260 triệu',
    ]);
    $unit_repeater->add_control('target_zones', [
      'label' => esc_html__('Phân khu áp dụng', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => '',
      'label_block' => true,
    ]);
    $unit_repeater->add_control('highlight', [
      'label' => esc_html__('Làm nổi bật (Phổ biến nhất)', 'eai'),
      'type' => \Elementor\Controls_Manager::SWITCHER,
      'label_on' => esc_html__('Có', 'eai'),
      'label_off' => esc_html__('Không', 'eai'),
      'return_value' => 'yes',
      'default' => '',
    ]);

    $this->add_control('unit_estimates', [
      'label' => esc_html__('Danh sách dự toán theo loại căn', 'eai'),
      'type' => \Elementor\Controls_Manager::REPEATER,
      'fields' => $unit_repeater->get_controls(),
      'title_field' => '{{{ unit_type }}} — {{{ price_range }}}',
      'default' => [
        ['unit_type' => 'Studio / 1PN', 'area_range' => '28m² – 43m²', 'price_range' => '120 – 155 triệu', 'target_zones' => 'The London (LD1–LD3), The Paris, Cao tầng Ocean Park 3', 'highlight' => ''],
        ['unit_type' => '1PN+1', 'area_range' => '45m² – 54m²', 'price_range' => '145 – 185 triệu', 'target_zones' => 'The Beverly (BE1–BE4), The London, LUMIÈRE SpringBay', 'highlight' => ''],
        ['unit_type' => '2PN / 2PN+1', 'area_range' => '58m² – 76m²', 'price_range' => '180 – 260 triệu', 'target_zones' => 'The Beverly, The London, LUMIÈRE SpringBay, Masteri Trinity Square', 'highlight' => 'yes'],
        ['unit_type' => '3PN / Căn góc VIP', 'area_range' => '82m² – 108m²', 'price_range' => '260 – 380 triệu', 'target_zones' => 'Căn góc Tòa BE1–BE4, Tòa LD1–LD3, Masteri Trinity Square', 'highlight' => ''],
        ['unit_type' => 'Duplex / Penthouse / Ghép căn', 'area_range' => '115m² – 180m²+', 'price_range' => '420 – 750+ triệu', 'target_zones' => 'LUMIÈRE SpringBay, Masteri Trinity Square, Ocean Park 1-2-3', 'highlight' => ''],
      ],
    ]);

    $room_repeater = new \Elementor\Repeater();
    $room_repeater->add_control('room_name', [
      'label' => esc_html__('Tên không gian phòng', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => 'Nội thất Phòng khách liền Bếp',
      'label_block' => true,
    ]);
    $room_repeater->add_control('english_name', [
      'label' => esc_html__('Tên tiếng Anh phụ', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => 'Living & Kitchen Bespoke',
    ]);
    $room_repeater->add_control('price_range', [
      'label' => esc_html__('Chi phí tham khảo', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => '75 – 125 triệu',
    ]);

    $this->add_control('room_estimates', [
      'label' => esc_html__('Dự toán theo từng không gian phòng', 'eai'),
      'type' => \Elementor\Controls_Manager::REPEATER,
      'fields' => $room_repeater->get_controls(),
      'title_field' => '{{{ room_name }}} — {{{ price_range }}}',
      'default' => [
        ['room_name' => 'Nội thất Phòng khách liền Bếp', 'english_name' => 'Living & Kitchen Bespoke', 'price_range' => '75 – 125 triệu'],
        ['room_name' => 'Nội thất Phòng ngủ Master Luxury', 'english_name' => 'Master Bedroom Suite', 'price_range' => '45 – 75 triệu'],
        ['room_name' => 'Nội thất Phòng ngủ phụ / Trẻ em', 'english_name' => 'Second / Kids Bedroom', 'price_range' => '30 – 50 triệu'],
        ['room_name' => 'Không gian +1 Đa năng & Logia', 'english_name' => 'Multi-function +1 & Balcony', 'price_range' => '18 – 35 triệu'],
      ],
    ]);

    $this->end_controls_section();

    $this->start_controls_section('section_commitments', [
      'label' => esc_html__('Cam kết vật liệu & Banner cuối mục', 'eai'),
      'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
    ]);

    $commit_repeater = new \Elementor\Repeater();
    $commit_repeater->add_control('title', [
      'label' => esc_html__('Tiêu đề cột cam kết', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => 'Hạng mục thi công trọn gói từ A–Z',
      'label_block' => true,
    ]);
    $commit_repeater->add_control('items_text', [
      'label' => esc_html__('Các mục (Mỗi dòng: Tiêu đề | Nội dung chi tiết)', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXTAREA,
      'rows' => 5,
      'default' => '',
    ]);

    $this->add_control('commitments', [
      'label' => esc_html__('Các cột cam kết', 'eai'),
      'type' => \Elementor\Controls_Manager::REPEATER,
      'fields' => $commit_repeater->get_controls(),
      'title_field' => '{{{ title }}}',
      'default' => [
        [
          'title' => 'Hạng mục thi công trọn gói từ A–Z',
          'items_text' => "Cải tạo trần thạch cao – sàn gỗ – tường & điện ánh sáng | Trần thạch cao Vĩnh Tường chống ẩm, hệ đèn LED ray nam châm / âm trần CRI>90, vách ốp tường PVC vân đá / than tre / Veneer, phào chỉ PU và sàn gỗ xương cá cao cấp.\nHệ tủ bếp cánh kính & Đảo bếp đá Vicostone | Tủ bếp kịch trần MDF lõi xanh An Cường / Plywood chịu nước, mặt đá thạch anh Vicostone chống thấm ố, kính ốp bếp cường lực Hải Long và phụ kiện giá bát nâng hạ Inox 304.\nNội thất liền tường & Đồ rời may đo đồng bộ | Tủ quần áo cánh kính khung nhôm Anode tích hợp đèn LED cảm biến, giường bọc da/nỉ Bỉ, sofa may đo theo kích thước phòng khách, bàn ăn mặt đá và rèm cửa 2 lớp.",
        ],
        [
          'title' => 'Cam kết vật liệu chính hãng tại Xưởng & Nhà mẫu',
          'items_text' => "100% cốt gỗ An Cường & Sơn Inchem 5 lớp | Sử dụng gỗ MDF lõi xanh chống ẩm An Cường chính hãng (có hóa đơn & phiếu xuất kho), Plywood phủ Veneer óc chó/sồi tự nhiên và buồng sơn Inchem 5 lớp tại nhà máy Như Quỳnh.\nĐá thạch anh Vicostone & Nhôm kính Anode | Đá Vicostone chính hãng bảo hành 15 năm, khung nhôm Anode mạ Champagne chống bám vân tay và chi tiết inox 304 mạ PVD sang trọng.\nPhụ kiện Blum (Áo) / Hafele (Đức) chính hãng | Toàn bộ bản lề, ray âm giảm chấn, tay nâng tủ bếp vận hành êm ái; cam kết phạt hợp đồng 200% nếu phát hiện pha trộn vật liệu kém chất lượng.",
        ],
        [
          'title' => 'Đặc quyền 3 vị trí địa lý sát vách Ocean Park',
          'items_text' => "Xem Nhà mẫu tại Sao Biển 23 & Nghiệm thu mộc tại Xưởng | Cư dân đi bộ/xe điện nội khu tới Nhà mẫu Sao Biển 23 Vinhomes Ocean Park xem thực tế, và được mời sang Nhà xưởng 2.000m² tại Ngọc Trai – Như Quỳnh kiểm tra phôi gỗ trước khi sơn.\nVăn phòng Số 01 Voi Phục bao trọn thủ tục Ban quản lý | Đội ngũ kỹ sư IC HOUSE tại Gia Lâm đại diện lập toàn bộ bản vẽ kỹ thuật MEP và nộp hồ sơ xin cấp phép thi công với BQL Vinhomes & Masterise Homes.\nBảo hành 2 năm • Bảo trì 5 năm có mặt trong 2 giờ | Nhờ có Văn phòng Số 01 Voi Phục, Nhà mẫu Sao Biển 23 và Nhà xưởng ngay cạnh khu đô thị, đội kỹ thuật IC HOUSE xử lý bảo hành tận căn hộ chỉ sau 2–24 giờ.",
        ],
      ],
    ]);

    $this->add_control('bottom_prompt_title', [
      'label' => esc_html__('Tiêu đề Banner cuối mục', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => 'Nhận bảng dự toán BOQ bóc tách từng hạng mục giá xưởng theo đúng mã căn hộ của bạn',
      'label_block' => true,
    ]);

    $this->add_control('bottom_prompt_description', [
      'label' => esc_html__('Mô tả Banner cuối mục', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXTAREA,
      'rows' => 2,
      'default' => 'Chỉ cần gửi mã tòa (LD1–LD3 The London, BE1–BE4 The Beverly, The Paris, LUMIÈRE SpringBay, Masteri Trinity Square, Ocean Park 3), KTS Trưởng IC HOUSE tại Số 01 Voi Phục & Sao Biển 23 sẽ gửi bảng báo giá chi tiết trong 30 phút.',
    ]);

    $this->add_control('bottom_prompt_button_label', [
      'label' => esc_html__('Nhãn nút Banner cuối mục', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => 'Yêu cầu bảng báo giá chi tiết',
      'label_block' => true,
    ]);

    $this->add_control('bottom_prompt_button_link', [
      'label' => esc_html__('Liên kết nút Banner cuối mục', 'eai'),
      'type' => \Elementor\Controls_Manager::URL,
      'options' => ['url', 'is_external', 'nofollow'],
      'default' => ['url' => '#dang-ky-tu-van', 'is_external' => false, 'nofollow' => false],
      'label_block' => true,
    ]);

    $this->end_controls_section();
  }

  protected function render(): void
  {
    $settings = $this->get_settings_for_display();
    $props = eai_ocean_park_pricing_get_rc_props($settings);

    if (eai_is_elementor_edit_mode() && eai_ocean_park_pricing_props_are_empty($props)) {
      $props = eai_ocean_park_pricing_get_editor_sample_props($settings);
    } elseif (eai_ocean_park_pricing_props_are_empty($props)) {
      eai_render_template(self::TEMPLATE, ['empty' => true]);
      return;
    }

    $result = eai_rc_render_html('OceanParkPricing', $props);
    eai_render_template(self::TEMPLATE, [
      'html' => is_wp_error($result) ? '' : $result['html'],
      'error' => is_wp_error($result) ? $result : null,
    ]);
  }
}
