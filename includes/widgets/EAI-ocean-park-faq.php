<?php

class EAI_Ocean_Park_Faq_Widget extends \Elementor\Widget_Base
{
  private const TEMPLATE = 'templates/EAI-ocean-park-faq.php';

  public function get_name(): string
  {
    return 'eai_ocean_park_faq_widget';
  }

  public function get_title(): string
  {
    return esc_html__('ICHouse — Ocean Park: Câu hỏi thường gặp (FAQ)', 'eai');
  }

  public function get_icon(): string
  {
    return 'eicon-accordion';
  }

  public function get_categories(): array
  {
    return eai_get_widget_categories();
  }

  public function get_keywords(): array
  {
    return ['ocean park', 'vinhomes', 'faq', 'cau hoi', 'accordion', 'eai', 'ichouse'];
  }

  protected function register_controls(): void
  {
    $this->start_controls_section('section_faq_content', [
      'label' => esc_html__('Nội dung Câu hỏi thường gặp (FAQ)', 'eai'),
      'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
    ]);

    $this->add_control('class_name', [
      'label' => esc_html__('Class tùy chỉnh', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => '',
    ]);

    $this->add_control('section_id', [
      'label' => esc_html__('ID Section (Anchor)', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => 'ocean-park-faq',
    ]);

    $this->add_control('eyebrow', [
      'label' => esc_html__('Nhãn Eyebrow', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => 'GIẢI ĐÁP CHUYÊN SÂU TỪ KIẾN TRÚC SƯ TRƯỞNG IC HOUSE (VĂN PHÒNG VOI PHỤC & NHÀ MẪU SAO BIỂN 23)',
      'label_block' => true,
    ]);

    $this->add_control('title', [
      'label' => esc_html__('Tiêu đề chính', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => 'Câu Hỏi Thường Gặp Khi Thiết Kế & Thi Công Nội Thất Vinhomes Ocean Park Trọn Gói Cao Cấp 1 • 2 • 3',
      'label_block' => true,
    ]);

    $this->add_control('description', [
      'label' => esc_html__('Mô tả mở đầu', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXTAREA,
      'rows' => 3,
      'default' => 'Tổng hợp kinh nghiệm thực chiến từ hơn 1.500+ công trình thiết kế và thi công nội thất chung cư trọn gói tại Vinhomes Ocean Park 1 (The London, The Beverly, The Paris, Masteri Waterfront, The Zurich), Ocean Park 2 (LUMIÈRE SpringBay, Masteri Trinity Square) và Dự án căn hộ Ocean Park 3.',
    ]);

    $faq_repeater = new \Elementor\Repeater();
    $faq_repeater->add_control('question', [
      'label' => esc_html__('Câu hỏi', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => '',
      'label_block' => true,
    ]);
    $faq_repeater->add_control('answer_html', [
      'label' => esc_html__('Câu trả lời (HTML)', 'eai'),
      'type' => \Elementor\Controls_Manager::WYSIWYG,
      'default' => '',
    ]);
    $faq_repeater->add_control('default_open', [
      'label' => esc_html__('Mở sẵn mặc định', 'eai'),
      'type' => \Elementor\Controls_Manager::SWITCHER,
      'label_on' => esc_html__('Có', 'eai'),
      'label_off' => esc_html__('Không', 'eai'),
      'return_value' => 'yes',
      'default' => '',
    ]);

    $this->add_control('items', [
      'label' => esc_html__('Danh sách Câu hỏi & Giải đáp', 'eai'),
      'type' => \Elementor\Controls_Manager::REPEATER,
      'fields' => $faq_repeater->get_controls(),
      'title_field' => '{{{ question }}}',
      'default' => [
        [
          'question' => 'Chi phí thiết kế và thi công nội thất trọn gói cao cấp tại Vinhomes Ocean Park 1, 2, 3 là bao nhiêu?',
          'answer_html' => '<p>Tại <strong>IC HOUSE</strong>, đơn giá thiết kế nội thất 3D cá nhân hóa (Bespoke) dao động từ <strong>250.000đ – 350.000đ/m²</strong> và được <strong>miễn phí 100%</strong> khi quý cư dân ký hợp đồng thi công nội thất trọn gói. Nhờ sản xuất trực tiếp tại <strong>Nhà xưởng 2.000m² ngay gần Ocean Park (Ngọc Trai – Như Quỳnh)</strong> không qua trung gian, tổng mức đầu tư hoàn thiện nội thất cao cấp khởi điểm chỉ từ <strong>120 triệu đồng/căn</strong> (Studio/1PN); trong đó gói <strong>Bespoke Luxury phổ biến từ 180 – 260 triệu đồng/căn</strong> (2PN – 2PN+1) sử dụng 100% cốt gỗ MDF lõi xanh chống ẩm An Cường, Plywood phủ Veneer óc chó tự nhiên, sơn Inchem 5 lớp, tủ cánh kính khung nhôm Anode, đá thạch anh Vicostone và phụ kiện giảm chấn Blum/Hafele chính hãng.</p>',
          'default_open' => 'yes',
        ],
        [
          'question' => 'Vì sao nên chọn IC HOUSE — đơn vị có Nhà mẫu trong Ocean Park, Văn phòng và Nhà xưởng ngay gần khu đô thị?',
          'answer_html' => '<p>Khác với các công ty nội thất ở xa nội thành hoặc đơn vị thương mại phải thuê xưởng gia công bên ngoài, <strong>IC HOUSE sở hữu 3 cơ sở hiện hữu ngay tại Vinhomes Ocean Park</strong> giúp quý cư dân hoàn toàn an tâm về chất lượng, giá thành và bảo hành:</p><ul><li><strong>1. Có Nhà mẫu nội thất TRONG khu đô thị Vinhomes Ocean Park:</strong> Đặt tại <strong>Sao Biển 23, KĐT Vinhomes Ocean Park, Gia Lâm, Hà Nội</strong> (chỉ 3 phút di chuyển nội khu) để gia chủ trực tiếp chạm tay kiểm tra cốt gỗ An Cường, đá Vicostone, tủ bếp cánh kính và độ sắc nét của đường dán cạnh trước khi xuống tiền.</li><li><strong>2. Có Văn phòng công ty GẦN khu đô thị:</strong> Đặt tại <strong>Số 01 Voi Phục, Gia Lâm, Hà Nội</strong> (cách 5 phút), đội ngũ 30+ Kiến trúc sư trưởng sẵn sàng có mặt khảo sát đo đạc căn hộ chỉ sau 15 phút và giám sát công trình hàng ngày.</li><li><strong>3. Có Nhà xưởng sản xuất 2.000m² GẦN Ocean Park:</strong> Đặt tại <strong>Thôn Ngọc Trai, Xã Như Quỳnh, Tỉnh Hưng Yên</strong> (cách 10 phút vận chuyển tới chân tòa), giúp tiết kiệm 15–20% chi phí trung gian, mời cư dân nghiệm thu cốt mộc tại xưởng và xử lý bảo hành siêu tốc trong 2–24 giờ.</li></ul>',
          'default_open' => '',
        ],
        [
          'question' => 'Thiết kế & thi công nội thất Căn hộ The London Ocean Park 1 (Tòa LD1, Tòa LD2, Tòa LD3) cần lưu ý gì?',
          'answer_html' => '<p>Phân khu <strong>The London</strong> (<strong>The London Ocean Park 1</strong>) bao gồm <strong>Tòa LD1</strong>, <strong>Tòa LD2</strong> và <strong>Tòa LD3</strong> sở hữu mặt bằng vuông vắn cùng tiêu chuẩn bàn giao hiện đại. Khi thiết kế thi công <strong>Căn hộ The London</strong>, KTS IC HOUSE tập trung tối ưu hệ tủ bếp &amp; tủ quần áo kịch trần đồng bộ tông màu với sàn gỗ và cửa bàn giao, bố trí đảo bếp kết hợp bàn ăn thông minh và giải pháp đèn LED ray nam châm kiến trúc giúp không gian rộng thoáng hơn 20–25%.</p>',
          'default_open' => '',
        ],
        [
          'question' => 'Căn hộ The Beverly Ocean Park 1 (Tòa BE1, Tòa BE2, Tòa BE3, Tòa BE4) bàn giao có sẵn bếp thì nên hoàn thiện thế nào?',
          'answer_html' => '<p>Tại phân khu <strong>The Beverly</strong> (<strong>The Beverly Ocean Park 1</strong>) gồm <strong>Tòa BE1</strong>, <strong>Tòa BE2</strong>, <strong>Tòa BE3</strong> và <strong>Tòa BE4</strong>, chủ đầu tư đã bàn giao sẵn hệ tủ bếp, thiết bị vệ sinh và điều hòa âm trần. Do đó, gói thi công nội thất <strong>Căn hộ The Beverly</strong> tại IC HOUSE sẽ <strong>khấu trừ toàn bộ hạng mục đã có sẵn</strong> (giúp gia chủ tiết kiệm ngay 35 – 60 triệu đồng), tập trung ngân sách nâng cấp vách ốp tivi phòng khách liền bếp, hệ tủ quần áo cánh kính kịch trần, tủ rượu trang trí và không gian đa năng +1 đẳng cấp.</p>',
          'default_open' => '',
        ],
        [
          'question' => 'IC HOUSE có mặt bằng thiết kế mẫu cho phân khu The Paris Ocean Park 1 (Chung cư Ocean Park 1) không?',
          'answer_html' => '<p>Văn phòng KTS IC HOUSE tại Số 01 Voi Phục đã hoàn thiện trọn bộ hồ sơ mặt bằng bố trí nội thất 2D và concept 3D cho tất cả các mã căn Studio, 1PN+1, 2PN, 2PN+1 và 3PN thuộc phân khu <strong>The Paris</strong> (<strong>The Paris Ocean Park 1</strong>) tại <strong>Chung cư Ocean Park 1</strong>. Các phong cách thiết kế nội thất được ưa chuộng nhất tại The Paris là <em>Parisian Chic</em>, <em>Modern Luxury</em>, <em>Neoclassic Tân cổ điển</em> và <em>Japandi</em> thanh lịch, khai thác tối đa tầm nhìn hướng hồ San Hô và quảng trường trung tâm.</p>',
          'default_open' => '',
        ],
        [
          'question' => 'Tiêu chuẩn thiết kế thi công nội thất cho Căn hộ LUMIÈRE SpringBay và Dự án Masteri Trinity Square tại Ocean Park 2 có gì đặc biệt?',
          'answer_html' => '<p>Tại đại đô thị <strong>Ocean Park 2</strong>, hai phân khu cao tầng hạng sang là <strong>LUMIÈRE SpringBay</strong> (<strong>LUMIÈRE SpringBay Ocean Park 2</strong>) và <strong>Masteri Trinity Square</strong> (<strong>Masteri Trinity Square Ocean Park 2</strong>) có hệ kính Low-E kịch trần cong/phẳng cùng tiêu chuẩn bàn giao Masterise Homes khắt khe. Khi thi công <strong>Căn hộ LUMIÈRE SpringBay</strong> và <strong>Dự án Masteri Trinity Square</strong>, nhà xưởng IC HOUSE Như Quỳnh sử dụng công nghệ cắt CNC laser và hệ liên kết âm giấu vít, nẹp đồng/nhôm Anode chuẩn xác từng milimet để bảo toàn tuyệt đối hệ trần thạch cao, điều hòa âm trần và sàn gỗ nguyên bản.</p>',
          'default_open' => '',
        ],
        [
          'question' => 'Phương án hoàn thiện nội thất cho Căn hộ cao tầng Ocean Park 3 (Dự án căn hộ Ocean Park 3) được tối ưu ra sao?',
          'answer_html' => '<p>Đối với các tòa thuộc <strong>Ocean Park 3</strong> (bao gồm <strong>Căn hộ cao tầng Ocean Park 3</strong> và <strong>Dự án căn hộ Ocean Park 3</strong>), nhờ vị trí nhà xưởng Ngọc Trai – Như Quỳnh nằm ngay sát dự án, IC HOUSE phát triển 2 dòng giải pháp nội thất giá xưởng chuyên biệt: (1) Gói <em>Bespoke Resort Living</em> dành cho gia đình ở thực hoặc nghỉ dưỡng cuối tuần với vật liệu chống ẩm tuyệt đối, và (2) Gói <em>Luxury Homestay Signature</em> thi công nhanh trong 15 ngày, tối ưu công năng và hiệu suất cho thuê chuyên gia quốc tế.</p>',
          'default_open' => '',
        ],
        [
          'question' => 'Quy trình xin giấy phép thi công với Ban quản lý Vinhomes và chế độ bảo hành tại chỗ của IC HOUSE như thế nào?',
          'answer_html' => '<p>IC HOUSE hỗ trợ <strong>trọn gói 100% thủ tục hồ sơ kỹ thuật</strong> (bản vẽ cải tạo cấp thoát nước, sơ đồ điện chiếu sáng, biện pháp thi công an toàn) để xin cấp phép tại Ban quản lý Vinhomes Ocean Park 1, 2, 3 và Masterise Homes. Toàn bộ công trình được cam kết tiến độ bằng hợp đồng (15 – 25 ngày làm việc), <strong>bảo hành 24 tháng</strong> và <strong>bảo trì định kỳ 5 năm</strong>; đội ngũ kỹ thuật trực tại <strong>Sao Biển 23</strong> và <strong>Số 01 Voi Phục</strong> có mặt xử lý tận căn hộ chỉ trong 2 giờ khi cư dân cần.</p>',
          'default_open' => '',
        ],
      ],
    ]);

    $this->end_controls_section();

    $this->start_controls_section('section_faq_keywords', [
      'label' => esc_html__('Từ khóa SEO theo Phân khu & Tòa', 'eai'),
      'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
    ]);

    $this->add_control('keyword_group_title', [
      'label' => esc_html__('Tiêu đề cụm từ khóa', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => 'Từ khóa tìm kiếm phổ biến: Thiết kế & Thi công nội thất Vinhomes Ocean Park 1 • 2 • 3 trọn gói cao cấp',
      'label_block' => true,
    ]);

    $pill_repeater = new \Elementor\Repeater();
    $pill_repeater->add_control('label', [
      'label' => esc_html__('Từ khóa', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => '',
    ]);
    $pill_repeater->add_control('url', [
      'label' => esc_html__('Đường dẫn (URL / Anchor)', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => '#du-an-mau-ocean-park',
      'label_block' => true,
    ]);

    $this->add_control('keyword_pills', [
      'label' => esc_html__('Danh sách thẻ từ khóa (Pills)', 'eai'),
      'type' => \Elementor\Controls_Manager::REPEATER,
      'fields' => $pill_repeater->get_controls(),
      'title_field' => '{{{ label }}}',
      'default' => [
        ['label' => 'Thiết kế nội thất Vinhomes Ocean Park', 'url' => '#du-an-mau-ocean-park'],
        ['label' => 'Thi công nội thất Vinhomes Ocean Park trọn gói', 'url' => '#du-an-mau-ocean-park'],
        ['label' => 'Nhà mẫu nội thất Sao Biển 23 Ocean Park', 'url' => '#dang-ky-tu-van'],
        ['label' => 'Xưởng sản xuất nội thất gần Ocean Park', 'url' => '#dang-ky-tu-van'],
        ['label' => 'Công ty nội thất gần Vinhomes Ocean Park', 'url' => '#dang-ky-tu-van'],
        ['label' => 'Báo giá thi công nội thất Vinhomes Ocean Park', 'url' => '#dang-ky-tu-van'],
        ['label' => 'Ocean Park 1', 'url' => '#du-an-mau-ocean-park'],
        ['label' => 'Chung cư Ocean Park 1', 'url' => '#du-an-mau-ocean-park'],
        ['label' => 'The Paris', 'url' => '#du-an-mau-ocean-park'],
        ['label' => 'The Paris Ocean Park 1', 'url' => '#du-an-mau-ocean-park'],
        ['label' => 'The London', 'url' => '#du-an-mau-ocean-park'],
        ['label' => 'The London Ocean Park 1', 'url' => '#du-an-mau-ocean-park'],
        ['label' => 'Căn hộ The London', 'url' => '#du-an-mau-ocean-park'],
        ['label' => 'Tòa LD1', 'url' => '#du-an-mau-ocean-park'],
        ['label' => 'Tòa LD2', 'url' => '#du-an-mau-ocean-park'],
        ['label' => 'Tòa LD3', 'url' => '#du-an-mau-ocean-park'],
        ['label' => 'The Beverly', 'url' => '#du-an-mau-ocean-park'],
        ['label' => 'The Beverly Ocean Park 1', 'url' => '#du-an-mau-ocean-park'],
        ['label' => 'Căn hộ The Beverly', 'url' => '#du-an-mau-ocean-park'],
        ['label' => 'Tòa BE1', 'url' => '#du-an-mau-ocean-park'],
        ['label' => 'Tòa BE2', 'url' => '#du-an-mau-ocean-park'],
        ['label' => 'Tòa BE3', 'url' => '#du-an-mau-ocean-park'],
        ['label' => 'Tòa BE4', 'url' => '#du-an-mau-ocean-park'],
        ['label' => 'Ocean Park 2', 'url' => '#du-an-mau-ocean-park'],
        ['label' => 'LUMIÈRE SpringBay', 'url' => '#du-an-mau-ocean-park'],
        ['label' => 'LUMIÈRE SpringBay Ocean Park 2', 'url' => '#du-an-mau-ocean-park'],
        ['label' => 'Căn hộ LUMIÈRE SpringBay', 'url' => '#du-an-mau-ocean-park'],
        ['label' => 'Masteri Trinity Square', 'url' => '#du-an-mau-ocean-park'],
        ['label' => 'Masteri Trinity Square Ocean Park 2', 'url' => '#du-an-mau-ocean-park'],
        ['label' => 'Dự án Masteri Trinity Square', 'url' => '#du-an-mau-ocean-park'],
        ['label' => 'Ocean Park 3', 'url' => '#du-an-mau-ocean-park'],
        ['label' => 'Căn hộ cao tầng Ocean Park 3', 'url' => '#du-an-mau-ocean-park'],
        ['label' => 'Dự án căn hộ Ocean Park 3', 'url' => '#du-an-mau-ocean-park'],
        ['label' => 'Nội thất gỗ An Cường căn hộ Ocean Park', 'url' => '#du-an-mau-ocean-park'],
      ],
    ]);

    $this->end_controls_section();
  }

  protected function render(): void
  {
    $settings = $this->get_settings_for_display();
    $widget_id = (string) $this->get_id();
    $props = eai_ocean_park_faq_get_rc_props($settings, $widget_id);

    if (eai_ocean_park_faq_props_are_empty($props)) {
      if (eai_is_elementor_edit_mode()) {
        $props = eai_ocean_park_faq_get_editor_sample_props($settings, $widget_id);
      } else {
        eai_render_template(self::TEMPLATE, ['empty' => true]);
        return;
      }
    }

    $result = eai_rc_render_html('OceanParkFaq', $props);
    eai_render_template(self::TEMPLATE, [
      'html' => is_wp_error($result) ? '' : $result['html'],
      'error' => is_wp_error($result) ? $result : null,
    ]);
  }
}
