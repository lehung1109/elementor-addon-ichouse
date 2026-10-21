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
      'default' => '3 LỢI THẾ ĐỊA LÝ ĐỘC QUYỀN CỦA IC HOUSE TẠI VINHOMES OCEAN PARK',
      'label_block' => true,
    ]);

    $this->add_control('title', [
      'label' => esc_html__('Tiêu đề chính', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => 'Nhà Mẫu Trong Khu Đô Thị • Văn Phòng Kế Cận • Nhà Máy Sản Xuất Ngay Sát Ocean Park',
      'label_block' => true,
    ]);

    $this->add_control('description', [
      'label' => esc_html__('Mô tả mở đầu', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXTAREA,
      'rows' => 3,
      'default' => 'Không phải di chuyển xa vào nội thành hay chờ đợi đội thợ từ nơi khác, cư dân Ocean Park 1, Ocean Park 2 và Ocean Park 3 được IC HOUSE phục vụ trực tiếp bằng hệ sinh thái Nhà mẫu – Văn phòng KTS – Nhà máy sản xuất bao quanh đại đô thị.',
    ]);

    $this->add_control('proof_banner_title', [
      'label' => esc_html__('Tiêu đề Banner đặt lịch tham quan', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => 'Đặt lịch tham quan Nhà mẫu Sao Biển 23 & Nhà máy Như Quỳnh ngay hôm nay',
      'label_block' => true,
    ]);

    $this->add_control('proof_banner_description', [
      'label' => esc_html__('Mô tả Banner đặt lịch tham quan', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXTAREA,
      'rows' => 2,
      'default' => 'IC HOUSE có xe đưa đón miễn phí quý cư dân từ các tòa căn hộ Ocean Park 1, 2, 3 sang tham quan Nhà mẫu và Nhà máy sản xuất.',
    ]);

    $this->add_control('proof_banner_button_label', [
      'label' => esc_html__('Nhãn nút Banner', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => 'Đặt lịch tham quan Nhà mẫu',
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
      'label' => esc_html__('Badge (VD: 01 • NHÀ MẪU TRONG ĐẠI ĐÔ THỊ)', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => '01 • NHÀ MẪU TRONG ĐẠI ĐÔ THỊ',
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
        'url' => 'https://placehold.co/800x450/152243/f5a623/png?text=IC+HOUSE+Ocean+Park',
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
          'badge' => '01 • NHÀ MẪU TRONG ĐẠI ĐÔ THỊ',
          'title' => 'Nhà Mẫu Nội Thất Thực Tế Ngay Tại Vinhomes Ocean Park',
          'address' => 'Sao Biển 23, KĐT Vinhomes Ocean Park, Gia Lâm, Hà Nội',
          'distance_note' => 'Chỉ 3 phút di chuyển nội khu',
          'description' => 'Không chỉ xem qua bản vẽ 3D trên màn hình, gia chủ Căn hộ The London, Căn hộ The Beverly, The Paris, LUMIÈRE SpringBay và Masteri Trinity Square có thể ghé ngay Sao Biển 23 để chạm tận tay từng đường nét hoàn thiện.',
          'highlight_points' => "Trải nghiệm trực tiếp chất lượng tủ bếp cánh kính Anode, gỗ An Cường, sơn Inchem và đá Vicostone.\nKiểm chứng độ êm của phụ kiện Blum/Hafele và chi tiết bo cạnh, chỉ nẹp tinh xảo.\nĐối chiếu trực tiếp bảng mẫu vật liệu dưới ánh sáng thực tế trước khi chốt sản xuất.",
          'image' => ['url' => 'https://placehold.co/800x450/152243/f5a623/png?text=Nha+Mau+Sao+Bien+23+Ocean+Park'],
          'image_resolution' => 'large',
          'image_alt' => 'Nhà mẫu nội thất IC HOUSE tại Sao Biển 23, KĐT Vinhomes Ocean Park',
        ],
        [
          'badge' => '02 • TRỤ SỞ VĂN PHÒNG KẾ CẬN',
          'title' => 'Trụ Sở Văn Phòng Kiến Trúc Sư Ngay Cửa Ngõ Đại Đô Thị',
          'address' => 'Số 01 Voi Phục, Gia Lâm, Hà Nội',
          'distance_note' => '5 phút tới các tòa căn hộ',
          'description' => 'Đội ngũ Kiến trúc sư trưởng và Kỹ sư giám sát làm việc thường trực ngay tại Gia Lâm, sẵn sàng có mặt tại các tòa LD1, LD2, LD3, BE1, BE2, BE3, BE4 chỉ sau 15 phút hẹn lịch.',
          'highlight_points' => "Khảo sát laser hiện trạng căn hộ miễn phí ngay trong ngày nhận bàn giao nhà.\nHọp chỉnh sửa phương án 3D trực tiếp cùng KTS Trưởng bất cứ lúc nào thuận tiện cho gia chủ.\nPhản ứng bảo hành – bảo trì siêu tốc trong vòng 24 giờ ngay tại địa phương.",
          'image' => ['url' => 'https://placehold.co/800x450/152243/f5a623/png?text=Van+Phong+So+01+Voi+Phuc+Gia+Lam'],
          'image_resolution' => 'large',
          'image_alt' => 'Trụ sở văn phòng IC HOUSE tại Số 01 Voi Phục, Gia Lâm, Hà Nội',
        ],
        [
          'badge' => '03 • NHÀ MÁY SẢN XUẤT SÁT VÁCH',
          'title' => 'Nhà Máy Sản Xuất Nội Thất Quy Mô Lớn Sát Ocean Park 1 • 2 • 3',
          'address' => 'Thôn Ngọc Trai, Xã Như Quỳnh, Tỉnh Hưng Yên',
          'distance_note' => '10 phút vận chuyển tới chân tòa',
          'description' => 'Nhà máy sản xuất trực tiếp rộng 2.000m² đặt ngay tại Ngọc Trai – Như Quỳnh (giáp ranh Vinhomes Ocean Park 1, 2, 3), giúp tối ưu chi phí vận chuyển và kiểm soát chất lượng từng tấm ván.',
          'highlight_points' => "Gia chủ được mời tới xưởng nghiệm thu cốt gỗ MDF lõi xanh An Cường & phôi mộc trước khi sơn phủ.\nGiảm thiểu 100% rủi ro trầy xước do vận chuyển đường xa, cần bổ sung hoặc bảo hành có ngay trong ngày.\nGiá gốc trực tiếp từ nhà máy sản xuất IC HOUSE — tiết kiệm 15–20% so với đơn vị thương mại trung gian.",
          'image' => ['url' => 'https://placehold.co/800x450/152243/f5a623/png?text=Nha+May+Ngoc+Trai+Nhu+Quynh'],
          'image_resolution' => 'large',
          'image_alt' => 'Nhà máy sản xuất nội thất IC HOUSE tại Thôn Ngọc Trai, Xã Như Quỳnh, Tỉnh Hưng Yên',
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
        'url' => 'https://placehold.co/800x350/152243/f5a623/png?text=IC+HOUSE+Capability',
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
          'title' => 'Xưởng sản xuất trực tiếp 2.000m² — Dây chuyền CNC & Sơn Inchem khép kín',
          'description' => 'Hệ thống máy cắt CNC trung tâm, máy dán cạnh tự động 8 chức năng không đường line và phòng sơn Inchem tiêu chuẩn giúp sản phẩm nội thất IC HOUSE đạt độ sắc nét vượt trội.',
          'checkpoints' => "Kiểm soát 100% nguồn gốc cốt gỗ An Cường, Plywood phủ Veneer tự nhiên và đá Vicostone.\nĐộ chính xác kích thước đến từng milimet, lắp đặt khít tuyệt đối với tường và trần căn hộ.",
          'image' => ['url' => 'https://placehold.co/800x350/152243/f5a623/png?text=Day+Chuyen+CNC+2000m2+IC+HOUSE'],
          'image_resolution' => 'large',
          'image_alt' => 'Xưởng sản xuất trực tiếp 2.000m² IC HOUSE',
        ],
        [
          'title' => 'Thi công thực tế chuẩn Vinhomes & Masterise Homes',
          'description' => 'Am hiểu sâu sắc quy định thi công, hệ thống kỹ thuật MEP, điều hòa âm trần và kết cấu tường chịu lực của các tòa Chung cư Ocean Park 1, LUMIÈRE SpringBay, Masteri Trinity Square và Dự án căn hộ Ocean Park 3.',
          'checkpoints' => "Bao trọn thủ tục xin cấp phép thi công, bảo lãnh và nghiệm thu với Ban quản lý tòa nhà.\nThi công sạch sẽ, bọc lót thang máy – hành lang đúng quy chuẩn, bàn giao vệ sinh công nghiệp.",
          'image' => ['url' => 'https://placehold.co/800x350/152243/f5a623/png?text=Thi+Cong+Chuan+Vinhomes+Masterise'],
          'image_resolution' => 'large',
          'image_alt' => 'Thi công thực tế chuẩn Vinhomes & Masterise Homes',
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
