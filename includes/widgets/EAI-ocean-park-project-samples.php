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

  protected function register_controls(): void
  {
    // ------------------------------------------------------------------
    // Section: Nội dung chung
    // ------------------------------------------------------------------
    $this->start_controls_section('section_content', [
      'label' => esc_html__('Nội dung chính', 'eai'),
      'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
    ]);

    $this->add_control('class_name', [
      'label'   => esc_html__('Class tùy chỉnh', 'eai'),
      'type'    => \Elementor\Controls_Manager::TEXT,
      'default' => '',
    ]);

    $this->add_control('eyebrow', [
      'label'       => esc_html__('Eyebrow (Nhãn trên tiêu đề)', 'eai'),
      'type'        => \Elementor\Controls_Manager::TEXT,
      'default'     => 'THƯ VIỆN MẪU THIẾT KẾ & CÔNG TRÌNH THI CÔNG THỰC TẾ TẠI VINHOMES OCEAN PARK',
      'label_block' => true,
    ]);

    $this->add_control('title', [
      'label'       => esc_html__('Tiêu đề chính', 'eai'),
      'type'        => \Elementor\Controls_Manager::TEXT,
      'default'     => 'Mẫu Thiết Kế & Thi Công Nội Thất Chung Cư Vinhomes Ocean Park 1 • 2 • 3 Theo Từng Phân Khu',
      'label_block' => true,
    ]);

    $this->add_control('description', [
      'label'   => esc_html__('Mô tả ngắn', 'eai'),
      'type'    => \Elementor\Controls_Manager::TEXTAREA,
      'rows'    => 3,
      'default' => 'Khám phá bộ sưu tập mặt bằng bố trí nội thất 2D, phối cảnh 3D Bespoke và công trình thực tế do đội ngũ KTS IC HOUSE (Văn phòng Số 01 Voi Phục – Nhà mẫu Sao Biển 23 – Nhà xưởng 2.000m² Như Quỳnh) thiết kế thi công trọn gói cho từng mã căn hộ tại Chung cư Ocean Park 1 (The London, The Beverly, The Paris), Ocean Park 2 (LUMIÈRE SpringBay, Masteri Trinity Square) và Dự án căn hộ Ocean Park 3.',
    ]);

    $this->add_control('mark_as_real_project', [
      'label'        => esc_html__('Đánh dấu là dự án thực tế', 'eai'),
      'type'         => \Elementor\Controls_Manager::SWITCHER,
      'label_on'     => esc_html__('Có', 'eai'),
      'label_off'    => esc_html__('Không', 'eai'),
      'return_value' => 'yes',
      'default'      => 'yes',
    ]);

    $this->end_controls_section();

    // ------------------------------------------------------------------
    // Section: Nhãn bộ lọc (nhập thủ công)
    // ------------------------------------------------------------------
    $this->start_controls_section('section_filter_labels', [
      'label' => esc_html__('Nhãn bộ lọc (Phân khu & Loại căn)', 'eai'),
      'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
    ]);

    $this->add_control('all_zones_label', [
      'label'   => esc_html__('Nhãn tab "Tất cả phân khu"', 'eai'),
      'type'    => \Elementor\Controls_Manager::TEXT,
      'default' => 'Tất cả phân khu',
    ]);

    $this->add_control('all_unit_types_label', [
      'label'   => esc_html__('Nhãn tab "Tất cả loại căn"', 'eai'),
      'type'    => \Elementor\Controls_Manager::TEXT,
      'default' => 'Tất cả loại căn',
    ]);

    $this->end_controls_section();

    // ------------------------------------------------------------------
    // Section: Danh sách dự án mẫu (nhập thủ công)
    // ------------------------------------------------------------------
    $this->start_controls_section('section_manual_entries', [
      'label' => esc_html__('Danh sách dự án mẫu (nhập thủ công)', 'eai'),
      'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
    ]);

    $manual_repeater = new \Elementor\Repeater();

    $manual_repeater->add_control('image', [
      'label' => esc_html__('Hình ảnh đại diện', 'eai'),
      'type'  => \Elementor\Controls_Manager::MEDIA,
    ]);

    $manual_repeater->add_control('title', [
      'label'       => esc_html__('Tiêu đề dự án', 'eai'),
      'type'        => \Elementor\Controls_Manager::TEXT,
      'label_block' => true,
      'default'     => '',
    ]);

    $manual_repeater->add_control('summary', [
      'label'   => esc_html__('Mô tả ngắn / Tóm tắt', 'eai'),
      'type'    => \Elementor\Controls_Manager::TEXTAREA,
      'rows'    => 3,
      'default' => '',
    ]);

    $manual_repeater->add_control('link', [
      'label'       => esc_html__('Liên kết chi tiết dự án', 'eai'),
      'type'        => \Elementor\Controls_Manager::URL,
      'placeholder' => 'https://',
      'options'     => ['url', 'is_external', 'nofollow'],
    ]);

    $manual_repeater->add_control('zone_id', [
      'label'       => esc_html__('Phân khu — ID (slug, dùng để lọc, VD: the-london)', 'eai'),
      'type'        => \Elementor\Controls_Manager::TEXT,
      'default'     => '',
      'description' => esc_html__('Slug không dấu, không khoảng trắng. Dùng "all" nếu thuộc tất cả phân khu.', 'eai'),
    ]);

    $manual_repeater->add_control('zone_label', [
      'label'       => esc_html__('Phân khu — Nhãn hiển thị (VD: The London Ocean Park 1)', 'eai'),
      'type'        => \Elementor\Controls_Manager::TEXT,
      'label_block' => true,
      'default'     => '',
    ]);

    $manual_repeater->add_control('unit_type_id', [
      'label'       => esc_html__('Loại căn — ID (slug, VD: 2pn)', 'eai'),
      'type'        => \Elementor\Controls_Manager::TEXT,
      'default'     => '',
      'description' => esc_html__('Slug không dấu. Dùng "all" nếu thuộc tất cả loại căn.', 'eai'),
    ]);

    $manual_repeater->add_control('unit_type_label', [
      'label'       => esc_html__('Loại căn — Nhãn hiển thị (VD: Căn hộ 2PN)', 'eai'),
      'type'        => \Elementor\Controls_Manager::TEXT,
      'label_block' => true,
      'default'     => '',
    ]);

    $manual_repeater->add_control('tower_label', [
      'label'       => esc_html__('Tòa căn hộ (VD: Tòa LD1 • LD2 • LD3)', 'eai'),
      'type'        => \Elementor\Controls_Manager::TEXT,
      'label_block' => true,
      'default'     => '',
    ]);

    $manual_repeater->add_control('area', [
      'label'   => esc_html__('Diện tích (VD: 62.8m²)', 'eai'),
      'type'    => \Elementor\Controls_Manager::TEXT,
      'default' => '',
    ]);

    $manual_repeater->add_control('apartment_code', [
      'label'   => esc_html__('Mã căn (VD: LD1-08)', 'eai'),
      'type'    => \Elementor\Controls_Manager::TEXT,
      'default' => '',
    ]);

    $manual_repeater->add_control('style', [
      'label'       => esc_html__('Phong cách (VD: Modern Luxury)', 'eai'),
      'type'        => \Elementor\Controls_Manager::TEXT,
      'label_block' => true,
      'default'     => '',
    ]);

    $manual_repeater->add_control('budget', [
      'label'   => esc_html__('Mức đầu tư tham khảo (VD: 185 – 230 triệu)', 'eai'),
      'type'    => \Elementor\Controls_Manager::TEXT,
      'label_block' => true,
      'default' => '',
    ]);

    $manual_repeater->add_control('is_locked', [
      'label'        => esc_html__('Làm mờ / Yêu cầu đăng ký', 'eai'),
      'type'         => \Elementor\Controls_Manager::SWITCHER,
      'label_on'     => esc_html__('Bật', 'eai'),
      'label_off'    => esc_html__('Tắt', 'eai'),
      'return_value' => 'yes',
      'default'      => '',
    ]);

    $this->add_control('manual_entries', [
      'label'       => esc_html__('Dự án mẫu', 'eai'),
      'type'        => \Elementor\Controls_Manager::REPEATER,
      'fields'      => $manual_repeater->get_controls(),
      'title_field' => '{{{ title }}}',
      'default'     => [
        // 1 — The London LD1·LD2·LD3 | 2PN
        [
          'image'          => ['url' => 'https://noithat.ichouse.vn/wp-content/uploads/2026/05/Du-an-noi-that-can-ho-toa-H2-masteri-OCP-2.jpg', 'id' => ''],
          'title'          => 'Thiết kế & Thi công nội thất Căn hộ The London Ocean Park 1 — Tòa LD1 • Tòa LD2 • Tòa LD3',
          'summary'        => 'Phương án thiết kế thi công nội thất trọn gói Căn hộ The London tại Tòa LD1, Tòa LD2, Tòa LD3 sản xuất trực tiếp tại nhà xưởng IC HOUSE Như Quỳnh: hệ tủ bếp kịch trần MDF lõi xanh An Cường, tủ áo cánh kính khung nhôm Anode và bàn đảo đá thạch anh Vicostone.',
          'link'           => ['url' => '#dang-ky-tu-van', 'is_external' => false, 'nofollow' => false],
          'zone_id'        => 'the-london',
          'zone_label'     => 'The London Ocean Park 1',
          'unit_type_id'   => '2pn',
          'unit_type_label'=> 'Căn hộ 2PN',
          'tower_label'    => 'Tòa LD1 • Tòa LD2 • Tòa LD3',
          'area'           => '62.8m²',
          'apartment_code' => 'LD1-08',
          'style'          => 'Modern British Luxury',
          'budget'         => '185 – 230 triệu',
        ],
        // 2 — The Beverly BE1·BE2 | 2PN+1
        [
          'image'          => ['url' => 'https://noithat.ichouse.vn/wp-content/uploads/2026/05/DSC08300-3-HDR-scaled.jpg', 'id' => ''],
          'title'          => 'Hoàn thiện nội thất Căn hộ The Beverly Ocean Park 1 — Tòa BE1 • Tòa BE2',
          'summary'        => 'Thiết kế thi công nội thất Căn hộ The Beverly tại Tòa BE1 và Tòa BE2 giữ nguyên hệ bếp bàn giao cao cấp của chủ đầu tư (tiết kiệm 45 triệu), nâng cấp phòng khách liền bếp, tủ rượu cánh kính LED và không gian +1 làm việc sang trọng như tại Nhà mẫu Sao Biển 23.',
          'link'           => ['url' => '#dang-ky-tu-van', 'is_external' => false, 'nofollow' => false],
          'zone_id'        => 'the-beverly',
          'zone_label'     => 'The Beverly Ocean Park 1',
          'unit_type_id'   => '2pn1',
          'unit_type_label'=> 'Căn hộ 2PN+1',
          'tower_label'    => 'Tòa BE1 • Tòa BE2',
          'area'           => '74.5m²',
          'apartment_code' => 'BE1-12A',
          'style'          => 'American Bespoke Luxury',
          'budget'         => '210 – 260 triệu',
        ],
        // 3 — The Beverly BE3·BE4 | Căn góc 3PN
        [
          'image'          => ['url' => 'https://noithat.ichouse.vn/wp-content/uploads/2026/05/du-an-noi-that-nha-pho-100m2-nha-a-chi-gia-lam-ha-noi-4.jpg', 'id' => ''],
          'title'          => 'Thi công nội thất Căn hộ góc 3PN The Beverly Ocean Park 1 — Tòa BE3 • Tòa BE4',
          'summary'        => 'Hoàn thiện nội thất cao cấp căn góc 3PN tại Tòa BE3 và Tòa BE4 phân khu The Beverly Ocean Park 1 với bề mặt sơn Inchem 5 lớp gia công tại xưởng 2.000m² Như Quỳnh, vách ốp Veneer óc chó tự nhiên, đá Vicostone và phụ kiện giảm chấn Blum đồng bộ.',
          'link'           => ['url' => '#dang-ky-tu-van', 'is_external' => false, 'nofollow' => false],
          'zone_id'        => 'the-beverly',
          'zone_label'     => 'The Beverly Ocean Park 1',
          'unit_type_id'   => '3pn',
          'unit_type_label'=> 'Căn góc 3PN VIP',
          'tower_label'    => 'Tòa BE3 • Tòa BE4',
          'area'           => '91.2m²',
          'apartment_code' => 'BE3-05',
          'style'          => 'Neoclassic Contemporary',
          'budget'         => '260 – 340 triệu',
        ],
        // 4 — The Paris | 1PN+1
        [
          'image'          => ['url' => 'https://noithat.ichouse.vn/wp-content/uploads/2026/05/du-an-thiet-ke-noi-that-nha-pho-80m2-tai-lac-dao-hung-yen-15.jpg', 'id' => ''],
          'title'          => 'Thiết kế & Thi công nội thất The Paris Ocean Park 1 — Chung cư Ocean Park 1',
          'summary'        => 'Tinh thần Pháp thanh lịch kết hợp đường nét Japandi tối giản cho căn hộ 1PN+1 tại phân khu The Paris Ocean Park 1 (Chung cư Ocean Park 1), tối ưu hệ tủ âm tường kịch trần, bàn ăn thông minh và ánh sáng tự nhiên hướng hồ San Hô.',
          'link'           => ['url' => '#dang-ky-tu-van', 'is_external' => false, 'nofollow' => false],
          'zone_id'        => 'the-paris',
          'zone_label'     => 'The Paris Ocean Park 1',
          'unit_type_id'   => '1pn1',
          'unit_type_label'=> 'Căn hộ 1PN+1',
          'tower_label'    => 'PR1 • PR2 • PR3 • PR5',
          'area'           => '48.6m²',
          'apartment_code' => 'PR2-15',
          'style'          => 'Parisian Chic & Japandi',
          'budget'         => '135 – 175 triệu',
        ],
        // 5 — LUMIÈRE SpringBay OCP2 | 2PN Luxury
        [
          'image'          => ['url' => 'https://noithat.ichouse.vn/wp-content/uploads/2026/05/thiet-ke-noi-that-chung-cu-tone-den-trang-hm18_1.jpg', 'id' => ''],
          'title'          => 'Thiết kế thi công nội thất Căn hộ LUMIÈRE SpringBay Ocean Park 2 — Chuẩn Masterise',
          'summary'        => 'May đo nội thất rời & hệ tủ âm tường chuẩn xác từng milimet từ nhà máy IC HOUSE Như Quỳnh (chỉ 10 phút vận chuyển sang Ocean Park 2), khớp nối hoàn hảo với hệ kính Low-E kịch trần cong của Căn hộ LUMIÈRE SpringBay.',
          'link'           => ['url' => '#dang-ky-tu-van', 'is_external' => false, 'nofollow' => false],
          'zone_id'        => 'lumiere-springbay',
          'zone_label'     => 'LUMIÈRE SpringBay Ocean Park 2',
          'unit_type_id'   => '2pn',
          'unit_type_label'=> 'Căn hộ 2PN Luxury',
          'tower_label'    => 'Vida • Feliz • Fira • Amor',
          'area'           => '68.4m²',
          'apartment_code' => 'LSB-09',
          'style'          => 'Organic Modern Luxury',
          'budget'         => '190 – 250 triệu',
        ],
        // 6 — Masteri Trinity Square OCP2 | Duplex
        [
          'image'          => ['url' => 'https://noithat.ichouse.vn/wp-content/uploads/2026/06/Phong-khach-su-dung-gach-Porcelain-kho-lon.png', 'id' => ''],
          'title'          => 'Thiết kế & Thi công nội thất Dự án Masteri Trinity Square Ocean Park 2',
          'summary'        => 'Không gian thông tầng & ghép căn đẳng cấp tại Dự án Masteri Trinity Square (Masteri Trinity Square Ocean Park 2) với đảo bếp đá thạch anh Vicostone nguyên khối, vách tivi ốp đá Porcelain khổ lớn, sofa da bò Ý và hệ đèn ray nam châm CRI>90.',
          'link'           => ['url' => '#dang-ky-tu-van', 'is_external' => false, 'nofollow' => false],
          'zone_id'        => 'masteri-trinity-square',
          'zone_label'     => 'Masteri Trinity Square Ocean Park 2',
          'unit_type_id'   => 'duplex',
          'unit_type_label'=> 'Duplex / Ghép căn',
          'tower_label'    => 'Southern • Central • Northern',
          'area'           => '112.0m²',
          'apartment_code' => 'MTS-18',
          'style'          => 'Bespoke Penthouse Luxury',
          'budget'         => '320 – 450 triệu',
        ],
        // 7 — Ocean Park 3 | Studio / 1PN Resort
        [
          'image'          => ['url' => 'https://noithat.ichouse.vn/wp-content/uploads/2026/05/du-an-noi-that-nha-pho-45m2-Gia-Lam-10.jpg', 'id' => ''],
          'title'          => 'Mẫu thiết kế thi công nội thất Căn hộ cao tầng Ocean Park 3 — Dự án căn hộ Ocean Park 3',
          'summary'        => 'Giải pháp thiết kế nội thất nghỉ dưỡng thông minh cho Căn hộ cao tầng Ocean Park 3 thuộc Dự án căn hộ Ocean Park 3, sản xuất trực tiếp tại xưởng IC HOUSE giáp ranh dự án, tối ưu công năng ở thực lẫn cho thuê Homestay cao cấp.',
          'link'           => ['url' => '#dang-ky-tu-van', 'is_external' => false, 'nofollow' => false],
          'zone_id'        => 'ocean-park-3',
          'zone_label'     => 'Ocean Park 3',
          'unit_type_id'   => 'studio',
          'unit_type_label'=> 'Studio / 1PN Resort',
          'tower_label'    => 'Phân khu Cao tầng OCP3',
          'area'           => '36.5m²',
          'apartment_code' => 'OCP3-06',
          'style'          => 'Modern Resort & Wabi Sabi',
          'budget'         => '120 – 150 triệu',
          'is_locked'      => '',
        ],
        // 8 — The London Căn góc 3PN (Locked)
        [
          'image'          => ['url' => 'https://noithat.ichouse.vn/wp-content/uploads/2026/05/Du-an-noi-that-can-ho-toa-H2-masteri-OCP-2.jpg', 'id' => ''],
          'title'          => 'Thiết kế & Thi công nội thất Căn góc 3PN The London Ocean Park 1 — Tòa LD2',
          'summary'        => 'Hồ sơ mặt bằng bố trí nội thất 2D, phối cảnh 3D Bespoke và bảng dự toán BOQ chi tiết căn góc 3PN Tòa LD2 phân khu The London Ocean Park 1 gia công tại xưởng IC HOUSE Như Quỳnh.',
          'link'           => ['url' => '#dang-ky-tu-van', 'is_external' => false, 'nofollow' => false],
          'zone_id'        => 'the-london',
          'zone_label'     => 'The London Ocean Park 1',
          'unit_type_id'   => '3pn',
          'unit_type_label'=> 'Căn góc 3PN',
          'tower_label'    => 'Tòa LD2',
          'area'           => '84.2m²',
          'apartment_code' => 'LD2-15',
          'style'          => 'Modern British Bespoke',
          'budget'         => '245 – 310 triệu',
          'is_locked'      => 'yes',
        ],
        // 9 — LUMIÈRE SpringBay 3PN VIP (Locked)
        [
          'image'          => ['url' => 'https://noithat.ichouse.vn/wp-content/uploads/2026/05/thiet-ke-noi-that-chung-cu-tone-den-trang-hm18_1.jpg', 'id' => ''],
          'title'          => 'Thiết kế & Thi công nội thất Căn hộ 3PN VIP LUMIÈRE SpringBay Ocean Park 2',
          'summary'        => 'Trọn bộ hồ sơ phối cảnh 3D, bản vẽ kỹ thuật hệ tủ cong kịch trần Low-E và bảng bóc tách khối lượng thi công thực tế căn hộ 3PN VIP LUMIÈRE SpringBay Ocean Park 2.',
          'link'           => ['url' => '#dang-ky-tu-van', 'is_external' => false, 'nofollow' => false],
          'zone_id'        => 'lumiere-springbay',
          'zone_label'     => 'LUMIÈRE SpringBay Ocean Park 2',
          'unit_type_id'   => '3pn',
          'unit_type_label'=> 'Căn góc 3PN Panorama',
          'tower_label'    => 'Tòa Feliz • Tòa Vida',
          'area'           => '96.8m²',
          'apartment_code' => 'LSB-18',
          'style'          => 'Quiet Luxury & Bespoke',
          'budget'         => '280 – 360 triệu',
          'is_locked'      => 'yes',
        ],
      ],
    ]);

    $this->end_controls_section();

    // ------------------------------------------------------------------
    // Section: Banner CTA cuối mục
    // ------------------------------------------------------------------
    $this->start_controls_section('section_cta', [
      'label' => esc_html__('Banner CTA cuối mục', 'eai'),
      'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
    ]);

    $this->add_control('cta_banner_title', [
      'label'       => esc_html__('Tiêu đề Banner CTA', 'eai'),
      'type'        => \Elementor\Controls_Manager::TEXT,
      'default'     => 'Bạn muốn xem mặt bằng 2D, phối cảnh 3D đúng mã căn & tham quan trực tiếp Nhà mẫu tại Sao Biển 23?',
      'label_block' => true,
    ]);

    $this->add_control('cta_banner_description', [
      'label'   => esc_html__('Mô tả Banner CTA', 'eai'),
      'type'    => \Elementor\Controls_Manager::TEXTAREA,
      'rows'    => 2,
      'default' => 'Văn phòng KTS IC HOUSE tại Số 01 Voi Phục Gia Lâm đã lưu trữ sẵn mặt bằng kỹ thuật toàn bộ các tòa LD1, LD2, LD3 (The London), BE1–BE4 (The Beverly), The Paris, LUMIÈRE SpringBay, Masteri Trinity Square và Ocean Park 3. Đăng ký nhận bản vẽ miễn phí và đặt lịch xem Nhà mẫu ngay trong KĐT!',
    ]);

    $this->add_control('cta_banner_button_label', [
      'label'       => esc_html__('Nhãn nút Banner CTA', 'eai'),
      'type'        => \Elementor\Controls_Manager::TEXT,
      'default'     => 'Nhận bản vẽ theo mã căn & Xem nhà mẫu',
      'label_block' => true,
    ]);

    $this->add_control('cta_banner_button_link', [
      'label'       => esc_html__('Liên kết nút Banner CTA', 'eai'),
      'type'        => \Elementor\Controls_Manager::URL,
      'options'     => ['url', 'is_external', 'nofollow'],
      'default'     => [
        'url'         => '#dang-ky-tu-van',
        'is_external' => false,
        'nofollow'    => false,
      ],
      'label_block' => true,
    ]);

    $this->end_controls_section();
  }

  /**
   * Build RC props from manually entered repeater data.
   *
   * @param array<string, mixed> $settings
   * @return array<string, mixed>
   */
  private function build_props_from_manual(array $settings): array
  {
    $entries        = is_array($settings['manual_entries'] ?? null) ? $settings['manual_entries'] : [];
    $is_real        = ($settings['mark_as_real_project'] ?? 'yes') === 'yes';
    $all_zones      = trim((string) ($settings['all_zones_label'] ?? '')) ?: 'Tất cả phân khu';
    $all_unit_types = trim((string) ($settings['all_unit_types_label'] ?? '')) ?: 'Tất cả loại căn';

    if (function_exists('eai_ocean_park_project_samples_prepare_manual_entries')) {
      $entries = eai_ocean_park_project_samples_prepare_manual_entries($entries);
    }

    $featured_projects = [];
    foreach ($entries as $entry) {
      $title = trim((string) ($entry['title'] ?? ''));
      if ($title === '') {
        continue;
      }

      // Build MediaModel via helper — handles id→URL resolution, srcSet,
      // and always produces the required display_dimensions field.
      $image_data = is_array($entry['image'] ?? null) ? $entry['image'] : [];
      $image      = eai_rc_map_media_model($image_data, [], null, 'large');
      if (trim((string) ($image['url'] ?? '')) === '') {
        continue;
      }
      // Fallback alt to project title if not set.
      if (trim((string) ($image['alt'] ?? '')) === '') {
        $image['alt'] = $title;
      }
      // Ensure display_dimensions always has valid integers (required by MediaModel).
      if (empty($image['display_dimensions']['width'])) {
        $image['display_dimensions'] = ['width' => 800, 'height' => 500];
      }

      $link_data      = is_array($entry['link'] ?? null) ? $entry['link'] : [];
      $zone_id        = trim((string) ($entry['zone_id'] ?? '')) ?: 'all';
      $zone_label     = trim((string) ($entry['zone_label'] ?? ''));
      $ut_id          = trim((string) ($entry['unit_type_id'] ?? '')) ?: 'all';
      $ut_label       = trim((string) ($entry['unit_type_label'] ?? ''));

      $featured_projects[] = [
        'title'         => $title,
        'zoneId'        => $zone_id,
        'zoneLabel'     => $zone_label,
        'unitTypeId'    => $ut_id,
        'unitTypeLabel' => $ut_label,
        'towerLabel'    => trim((string) ($entry['tower_label'] ?? '')),
        'area'          => trim((string) ($entry['area'] ?? '')),
        'apartmentCode' => trim((string) ($entry['apartment_code'] ?? '')),
        'style'         => trim((string) ($entry['style'] ?? '')),
        'budget'        => trim((string) ($entry['budget'] ?? '')),
        'summary'       => trim((string) ($entry['summary'] ?? '')),
        'image'         => $image,
        'detailLink'    => eai_rc_map_link($link_data),
        'isRealProject' => $is_real,
        'isLocked'      => ($entry['is_locked'] ?? '') === 'yes',
      ];
    }

    $cta_link_data = is_array($settings['cta_banner_button_link'] ?? null)
      ? $settings['cta_banner_button_link']
      : [];

    $props = [
      'eyebrow'         => trim((string) ($settings['eyebrow'] ?? '')),
      'title'           => trim((string) ($settings['title'] ?? '')),
      'description'     => trim((string) ($settings['description'] ?? '')),
      'zoneTabs'        => eai_ocean_park_project_samples_collect_filter_tabs(
        $featured_projects,
        'zoneId',
        'zoneLabel',
        $all_zones
      ),
      'unitTypeTabs'    => eai_ocean_park_project_samples_collect_filter_tabs(
        $featured_projects,
        'unitTypeId',
        'unitTypeLabel',
        $all_unit_types
      ),
      'featuredProjects' => $featured_projects,
      'ctaBanner'       => [
        'title'       => trim((string) ($settings['cta_banner_title'] ?? '')),
        'description' => trim((string) ($settings['cta_banner_description'] ?? '')),
        'buttonLabel' => trim((string) ($settings['cta_banner_button_label'] ?? '')),
        'buttonLink'  => eai_rc_map_link($cta_link_data),
      ],
    ];

    $class_name = trim((string) ($settings['class_name'] ?? ''));
    if ($class_name !== '') {
      $props['className'] = $class_name;
    }

    return $props;
  }

  protected function render(): void
  {
    $settings = $this->get_settings_for_display();
    $props    = $this->build_props_from_manual($settings);

    if (empty($props['featuredProjects'])) {
      eai_render_template(self::TEMPLATE, ['empty' => true]);
      return;
    }

    $result = eai_rc_render_html('OceanParkProjectSamplesWrapper', $props);
    eai_render_template(self::TEMPLATE, [
      'html'  => is_wp_error($result) ? '' : $result['html'],
      'error' => is_wp_error($result) ? $result : null,
    ]);
  }
}
