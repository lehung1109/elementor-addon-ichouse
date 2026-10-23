<?php

class EAI_Ocean_Park_Advantages_Widget extends \Elementor\Widget_Base
{
  private const TEMPLATE = 'templates/EAI-ocean-park-advantages.php';

  public function get_name(): string
  {
    return 'eai_ocean_park_advantages_widget';
  }

  public function get_title(): string
  {
    return esc_html__('ICHouse — Ocean Park: Lợi thế', 'eai');
  }

  public function get_icon(): string
  {
    return 'eicon-star-o';
  }

  public function get_categories(): array
  {
    return eai_get_widget_categories();
  }

  public function get_keywords(): array
  {
    return ['ocean park', 'vinhomes', 'advantages', 'loi the', 'nha mau', 'nha may', 'eai', 'ichouse'];
  }

  protected function register_controls(): void
  {
    $this->start_controls_section('section_header', [
      'label' => esc_html__('Tiêu đề & Banner cuối mục', 'eai'),
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
      'default' => '3 LỢI THẾ ĐỊA LÝ ĐỘC QUYỀN CHỈ CÓ TẠI IC HOUSE — PHỤC VỤ TRỰC TIẾP CƯ DÂN VINHOMES OCEAN PARK',
      'label_block' => true,
    ]);

    $this->add_control('title', [
      'label' => esc_html__('Tiêu đề chính', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => 'Có Nhà Mẫu Trong Ocean Park • Văn Phòng Công Ty Gần Khu Đô Thị • Nhà Xưởng 2.000m² Ngay Sát Ocean Park',
      'label_block' => true,
    ]);

    $this->add_control('description', [
      'label' => esc_html__('Mô tả mở đầu', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXTAREA,
      'rows' => 3,
      'default' => 'Không phải di chuyển xa vào nội thành Hà Nội hay lo lắng thuê phải đơn vị thương mại trung gian không có xưởng, cư dân thiết kế & thi công nội thất Vinhomes Ocean Park 1, Ocean Park 2 và Ocean Park 3 được IC HOUSE phục vụ trực tiếp bằng hệ sinh thái 3 cơ sở hiện hữu ngay tại địa phương: Nhà mẫu trong KĐT + Văn phòng KTS gần KĐT + Nhà xưởng sản xuất sát vách đại đô thị.',
    ]);

    $this->add_control('proof_banner_title', [
      'label' => esc_html__('Tiêu đề Banner đặt lịch tham quan', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => 'Đặt lịch tham quan Nhà mẫu trong Ocean Park (Sao Biển 23), Văn phòng Voi Phục & Nhà xưởng Như Quỳnh',
      'label_block' => true,
    ]);

    $this->add_control('proof_banner_description', [
      'label' => esc_html__('Mô tả Banner đặt lịch tham quan', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXTAREA,
      'rows' => 2,
      'default' => 'IC HOUSE có xe ô tô đưa đón miễn phí quý cư dân từ tất cả các tòa căn hộ Vinhomes Ocean Park 1, 2, 3 sang tham quan trực tiếp Nhà mẫu nội thất và Nhà xưởng sản xuất 2.000m².',
    ]);

    $this->add_control('proof_banner_button_label', [
      'label' => esc_html__('Nhãn nút Banner', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => 'Đặt lịch tham quan Nhà mẫu & Nhà xưởng',
      'label_block' => true,
    ]);

    $this->add_control('proof_banner_button_link', [
      'label' => esc_html__('Liên kết nút Banner', 'eai'),
      'type' => \Elementor\Controls_Manager::URL,
      'options' => ['url', 'is_external', 'nofollow'],
      'default' => ['url' => '#dang-ky-tu-van', 'is_external' => false, 'nofollow' => false],
      'label_block' => true,
    ]);

    $this->end_controls_section();

    $this->start_controls_section('section_local_strengths', [
      'label' => esc_html__('3 Lợi thế địa lý (Local Strengths)', 'eai'),
      'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
    ]);

    $ls_repeater = new \Elementor\Repeater();
    $ls_repeater->add_control('badge', [
      'label' => esc_html__('Badge (VD: 01 • NHÀ MẪU NỘI THẤT TRONG OCEAN PARK)', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => '01 • NHÀ MẪU NỘI THẤT TRONG OCEAN PARK',
      'label_block' => true,
    ]);
    $ls_repeater->add_control('title', [
      'label' => esc_html__('Tiêu đề', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => '',
      'label_block' => true,
    ]);
    $ls_repeater->add_control('address', [
      'label' => esc_html__('Địa chỉ', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => '',
      'label_block' => true,
    ]);
    $ls_repeater->add_control('distance_note', [
      'label' => esc_html__('Ghi chú khoảng cách', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => '',
    ]);
    $ls_repeater->add_control('description', [
      'label' => esc_html__('Mô tả', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXTAREA,
      'rows' => 3,
      'default' => '',
    ]);
    $ls_repeater->add_control('highlight_points', [
      'label' => esc_html__('Các điểm nhấn (mỗi dòng 1 ý)', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXTAREA,
      'rows' => 4,
      'default' => '',
    ]);
    $ls_repeater->add_control('image', [
      'label' => esc_html__('Hình ảnh', 'eai'),
      'type' => \Elementor\Controls_Manager::MEDIA,
      'default' => [
        'url' => 'https://noithat.ichouse.vn/wp-content/uploads/2026/05/DSC08300-3-HDR-scaled.jpg',
      ],
    ]);
    $ls_repeater->add_control('image_resolution', [
      'label' => esc_html__('Kích thước ảnh', 'eai'),
      'type' => \Elementor\Controls_Manager::SELECT,
      'default' => 'large',
      'options' => eai_get_image_size_options(),
    ]);
    $ls_repeater->add_control('image_alt', [
      'label' => esc_html__('Alt ảnh (tùy chọn)', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => '',
      'label_block' => true,
    ]);

    $this->add_control('local_strengths', [
      'label' => esc_html__('Danh sách lợi thế địa lý', 'eai'),
      'type' => \Elementor\Controls_Manager::REPEATER,
      'fields' => $ls_repeater->get_controls(),
      'title_field' => '{{{ badge }}} — {{{ title }}}',
      'default' => [
        [
          'badge' => '01 • NHÀ MẪU NỘI THẤT TRONG OCEAN PARK',
          'title' => 'Nhà Mẫu Nội Thất Thực Tế Nằm Ngay Trong KĐT Vinhomes Ocean Park',
          'address' => 'Sao Biển 23, KĐT Vinhomes Ocean Park, Gia Lâm, Hà Nội',
          'distance_note' => 'Chỉ 3 phút di chuyển nội khu',
          'description' => 'Không chỉ xem qua bản vẽ 3D trên máy tính, cư dân Căn hộ The London, Căn hộ The Beverly, The Paris, LUMIÈRE SpringBay, Masteri Trinity Square và Căn hộ cao tầng Ocean Park 3 có thể ghé ngay Nhà mẫu IC HOUSE tại Sao Biển 23 trong lòng khu đô thị để mắt thấy – tay chạm từng chi tiết hoàn thiện thực tế.',
          'highlight_points' => "Trực tiếp trải nghiệm tủ bếp cánh kính khung nhôm Anode, cốt gỗ MDF lõi xanh chống ẩm An Cường, sơn Inchem 5 lớp và mặt đá thạch anh Vicostone.\nKiểm chứng độ êm ái của phụ kiện bản lề, ray trượt giảm chấn Blum/Hafele và đường nét dán cạnh không đường line tinh xảo.\nĐối chiếu hơn 300+ mã màu Melamine, Laminate, Acrylic, Veneer óc chó và mẫu da/nỉ dưới ánh sáng thực tế trước khi ký hợp đồng thi công.",
          'image' => ['url' => 'https://noithat.ichouse.vn/wp-content/uploads/2026/05/DSC08300-3-HDR-scaled.jpg'],
          'image_resolution' => 'large',
          'image_alt' => 'Nhà mẫu nội thất IC HOUSE tại Sao Biển 23, KĐT Vinhomes Ocean Park, Gia Lâm, Hà Nội',
        ],
        [
          'badge' => '02 • VĂN PHÒNG CÔNG TY GẦN KHU ĐÔ THỊ',
          'title' => 'Trụ Sở Văn Phòng Công Ty & Đội Ngũ KTS Ngay Gần Khu Đô Thị Ocean Park',
          'address' => 'Số 01 Voi Phục, Gia Lâm, Hà Nội',
          'distance_note' => '5 phút tới các tòa căn hộ',
          'description' => 'Văn phòng công ty nội thất IC HOUSE đặt ngay tại cửa ngõ Gia Lâm – sát khu đô thị Vinhomes Ocean Park. Đội ngũ 30+ Kiến trúc sư trưởng và Kỹ sư giám sát làm việc thường trực, sẵn sàng có mặt tại các tòa LD1, LD2, LD3 (The London), BE1, BE2, BE3, BE4 (The Beverly), The Paris, Ocean Park 2 & 3 chỉ sau 15 phút hẹn lịch.',
          'highlight_points' => "Khảo sát đo đạc laser hiện trạng căn hộ miễn phí 100% ngay trong buổi nhận bàn giao nhà từ chủ đầu tư.\nLàm việc 1:1 trực tiếp với Kiến trúc sư trưởng để tinh chỉnh mặt bằng công năng 2D và phối cảnh 3D bất cứ lúc nào thuận tiện.\nGiám sát thi công sát sao mỗi ngày và hỗ trợ bảo hành – bảo trì tận căn hộ siêu tốc trong vòng 2 – 24 giờ.",
          'image' => ['url' => 'https://noithat.ichouse.vn/wp-content/uploads/2026/06/doi-ngu-kts-1024x400.jpg'],
          'image_resolution' => 'large',
          'image_alt' => 'Văn phòng công ty nội thất IC HOUSE tại Số 01 Voi Phục, Gia Lâm, Hà Nội gần Vinhomes Ocean Park',
        ],
        [
          'badge' => '03 • NHÀ XƯỞNG 2.000M² GẦN OCEAN PARK',
          'title' => 'Nhà Xưởng / Nhà Máy Sản Xuất Nội Thất 2.000m² Ngay Gần Ocean Park 1 • 2 • 3',
          'address' => 'Thôn Ngọc Trai, Xã Như Quỳnh, Tỉnh Hưng Yên',
          'distance_note' => '10 phút vận chuyển tới chân tòa',
          'description' => 'Sở hữu nhà xưởng sản xuất nội thất trực tiếp quy mô 2.000m² đặt ngay tại Ngọc Trai – Như Quỳnh (giáp ranh Vinhomes Ocean Park 1, Ocean Park 2 và Ocean Park 3), IC HOUSE chủ động 100% tiến độ sản xuất, kiểm soát chất lượng từng tấm ván và cắt giảm hoàn toàn chi phí trung gian.',
          'highlight_points' => "Quý cư dân được mời sang tận nhà xưởng gần Ocean Park để nghiệm thu cốt gỗ MDF lõi xanh An Cường & phôi mộc trước khi sơn phủ và lắp đặt.\nKhoảng cách vận chuyển chỉ 10 phút tới chân tòa nhà giúp triệt tiêu rủi ro trầy xước; cần điều chỉnh hay bổ sung hạng mục đều xử lý ngay trong ngày.\nBáo giá gốc trực tiếp từ nhà xưởng IC HOUSE — tiết kiệm 15–20% chi phí so với các công ty thiết kế phải đi thuê xưởng gia công bên ngoài.",
          'image' => ['url' => 'https://noithat.ichouse.vn/wp-content/uploads/2024/02/1-1.jpg'],
          'image_resolution' => 'large',
          'image_alt' => 'Nhà xưởng sản xuất nội thất IC HOUSE 2.000m2 gần Vinhomes Ocean Park tại Thôn Ngọc Trai, Xã Như Quỳnh',
        ],
      ],
    ]);

    $this->end_controls_section();

    $this->start_controls_section('section_capability_pillars', [
      'label' => esc_html__('Năng lực sản xuất & Thi công (Capability Pillars)', 'eai'),
      'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
    ]);

    $cp_repeater = new \Elementor\Repeater();
    $cp_repeater->add_control('title', [
      'label' => esc_html__('Tiêu đề năng lực', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => '',
      'label_block' => true,
    ]);
    $cp_repeater->add_control('description', [
      'label' => esc_html__('Mô tả', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXTAREA,
      'rows' => 3,
      'default' => '',
    ]);
    $cp_repeater->add_control('checkpoints', [
      'label' => esc_html__('Các tiêu chuẩn kiểm định (mỗi dòng 1 ý)', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXTAREA,
      'rows' => 3,
      'default' => '',
    ]);
    $cp_repeater->add_control('image', [
      'label' => esc_html__('Hình ảnh', 'eai'),
      'type' => \Elementor\Controls_Manager::MEDIA,
      'default' => [
        'url' => 'https://noithat.ichouse.vn/wp-content/uploads/2024/02/2-1.jpg',
      ],
    ]);
    $cp_repeater->add_control('image_resolution', [
      'label' => esc_html__('Kích thước ảnh', 'eai'),
      'type' => \Elementor\Controls_Manager::SELECT,
      'default' => 'large',
      'options' => eai_get_image_size_options(),
    ]);
    $cp_repeater->add_control('image_alt', [
      'label' => esc_html__('Alt ảnh (tùy chọn)', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => '',
      'label_block' => true,
    ]);

    $this->add_control('capability_pillars', [
      'label' => esc_html__('Danh sách trụ cột năng lực', 'eai'),
      'type' => \Elementor\Controls_Manager::REPEATER,
      'fields' => $cp_repeater->get_controls(),
      'title_field' => '{{{ title }}}',
      'default' => [
        [
          'title' => 'Nhà xưởng sản xuất trực tiếp 2.000m² gần Ocean Park — Dây chuyền CNC & Buồng sơn Inchem khép kín',
          'description' => 'Hệ thống máy cắt CNC trung tâm tự động, máy dán cạnh 8 chức năng không đường line và phòng sơn Inchem 5 lớp tiêu chuẩn quốc tế giúp đồ gỗ nội thất IC HOUSE đạt độ hoàn thiện sắc nét vượt trội cho các căn hộ cao cấp.',
          'checkpoints' => "Cam kết 100% cốt gỗ MDF lõi xanh chống ẩm An Cường, Plywood phủ Veneer tự nhiên và đá thạch anh Vicostone có đầy đủ chứng từ xuất xứ.\nChuẩn xác kích thước đến từng milimet, lắp đặt khít tuyệt đối với tường, dầm và hệ trần thạch cao căn hộ Vinhomes Ocean Park.",
          'image' => ['url' => 'https://noithat.ichouse.vn/wp-content/uploads/2024/02/2-1.jpg'],
          'image_resolution' => 'large',
          'image_alt' => 'Dây chuyền máy CNC tại nhà xưởng sản xuất nội thất 2.000m² IC HOUSE gần Ocean Park',
        ],
        [
          'title' => 'Kinh nghiệm thực chiến 1.500+ căn hộ chuẩn kỹ thuật Vinhomes & Masterise Homes',
          'description' => 'Am hiểu tường tận quy định thi công nội thất, sơ đồ kỹ thuật điện nước MEP, điều hòa âm trần và kết cấu tường chịu lực tại các tòa Chung cư Ocean Park 1 (The London, The Beverly, The Paris), LUMIÈRE SpringBay, Masteri Trinity Square và Dự án căn hộ Ocean Park 3.',
          'checkpoints' => "Hỗ trợ trọn gói 100% hồ sơ bản vẽ kỹ thuật xin cấp phép thi công, đặt cọc và nghiệm thu với Ban quản lý Vinhomes & Masterise.\nThi công văn minh, bọc lót bảo vệ sàn – thang máy – hành lang đúng quy định, vệ sinh công nghiệp sạch sẽ trước khi bàn giao chìa khóa trao tay.",
          'image' => ['url' => 'https://noithat.ichouse.vn/wp-content/uploads/2026/05/Du-an-noi-that-can-ho-toa-H2-masteri-OCP-2.jpg'],
          'image_resolution' => 'large',
          'image_alt' => 'Thi công nội thất thực tế căn hộ Vinhomes Ocean Park chuẩn Vinhomes & Masterise Homes',
        ],
      ],
    ]);

    $this->end_controls_section();
  }

  protected function render(): void
  {
    $settings = $this->get_settings_for_display();
    $props = eai_ocean_park_advantages_get_rc_props($settings);

    if (eai_is_elementor_edit_mode() && eai_ocean_park_advantages_props_are_empty($props)) {
      $props = eai_ocean_park_advantages_get_editor_sample_props($settings);
    } elseif (eai_ocean_park_advantages_props_are_empty($props)) {
      eai_render_template(self::TEMPLATE, ['empty' => true]);
      return;
    }

    $result = eai_rc_render_html('OceanParkAdvantages', $props);
    eai_render_template(self::TEMPLATE, [
      'html' => is_wp_error($result) ? '' : $result['html'],
      'error' => is_wp_error($result) ? $result : null,
    ]);
  }
}
