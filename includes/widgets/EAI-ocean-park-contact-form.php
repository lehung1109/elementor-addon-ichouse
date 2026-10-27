<?php

class EAI_Ocean_Park_Contact_Form_Widget extends \Elementor\Widget_Base
{
  private const TEMPLATE = 'templates/EAI-ocean-park-contact-form.php';

  public function get_name(): string
  {
    return 'eai_ocean_park_contact_form_widget';
  }

  public function get_title(): string
  {
    return esc_html__('ICHouse — Ocean Park: Form liên hệ', 'eai');
  }

  public function get_icon(): string
  {
    return 'eicon-form-horizontal';
  }

  public function get_categories(): array
  {
    return eai_get_widget_categories();
  }

  public function get_keywords(): array
  {
    return ['ocean park', 'vinhomes', 'contact', 'form', 'lien he', 'cf7', 'eai', 'ichouse'];
  }

  protected function register_controls(): void
  {
    $this->start_controls_section('section_contact_info', [
      'label' => esc_html__('Thông tin liên hệ & Địa chỉ', 'eai'),
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
      'default' => 'dang-ky-tu-van',
    ]);

    $this->add_control('form_key', [
      'label' => esc_html__('Form key (duy nhất trên trang)', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => 'ocean-park-consultation',
      'label_block' => true,
    ]);

    $this->add_control('cf7_form_id', [
      'label' => esc_html__('Contact Form 7 (tùy chọn)', 'eai'),
      'type' => \Elementor\Controls_Manager::SELECT,
      'default' => '',
      'options' => eai_contact_popup_get_cf7_options(),
      'label_block' => true,
      'description' => esc_html__('Nếu chọn form CF7, giao diện sẽ tự nhúng form CF7 thật vào cột phải; nếu để trống sẽ hiển thị form tư vấn tương tác mặc định.', 'eai'),
    ]);

    $this->add_control('eyebrow', [
      'label' => esc_html__('Nhãn Eyebrow', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => 'ĐĂNG KÝ TƯ VẤN MIỄN PHÍ • THAM QUAN NHÀ MẪU TRONG OCEAN PARK & NHÀ XƯỞNG 2.000M²',
      'label_block' => true,
    ]);

    $this->add_control('title', [
      'label' => esc_html__('Tiêu đề chính', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => 'Nhận Bản Vẽ Mặt Bằng 2D, Phối Cảnh 3D & Báo Giá Gốc Tại Xưởng Theo Mã Căn Hộ Ocean Park',
      'label_block' => true,
    ]);

    $this->add_control('description', [
      'label' => esc_html__('Mô tả mở đầu', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXTAREA,
      'rows' => 3,
      'default' => 'Đội ngũ Kiến trúc sư trưởng IC HOUSE từ Văn phòng Số 01 Voi Phục (gần khu đô thị) và Nhà mẫu Sao Biển 23 (trong Vinhomes Ocean Park) trực tiếp khảo sát đo đạc tại các tòa The London (LD1, LD2, LD3), The Beverly (BE1, BE2, BE3, BE4), The Paris Ocean Park 1, LUMIÈRE SpringBay Ocean Park 2, Masteri Trinity Square Ocean Park 2 và Căn hộ cao tầng Ocean Park 3.',
    ]);

    $this->add_control('hotline', [
      'label' => esc_html__('Số Hotline KTS Trưởng', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => '0978 305 740',
    ]);

    $this->add_control('zalo_phone', [
      'label' => esc_html__('Số Zalo Nhận Bản Vẽ 3D', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => '0978 305 740',
    ]);

    $this->add_control('email', [
      'label' => esc_html__('Email liên hệ', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => 'noithatichouse@gmail.com',
      'label_block' => true,
    ]);

    $this->add_control('website', [
      'label' => esc_html__('Website hiển thị', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => 'noithat.ichouse.vn',
      'label_block' => true,
    ]);

    $location_repeater = new \Elementor\Repeater();
    $location_repeater->add_control('label', [
      'label' => esc_html__('Tên cơ sở', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => '',
      'label_block' => true,
    ]);
    $location_repeater->add_control('address', [
      'label' => esc_html__('Địa chỉ chi tiết', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => '',
      'label_block' => true,
    ]);

    $this->add_control('locations', [
      'label' => esc_html__('Hệ thống Nhà mẫu • Văn phòng • Nhà máy', 'eai'),
      'type' => \Elementor\Controls_Manager::REPEATER,
      'fields' => $location_repeater->get_controls(),
      'title_field' => '{{{ label }}}',
      'default' => [
        [
          'label' => 'Nhà mẫu nội thất TRONG khu đô thị Vinhomes Ocean Park',
          'address' => 'Sao Biển 23, KĐT Vinhomes Ocean Park, Gia Lâm, Hà Nội',
        ],
        [
          'label' => 'Văn phòng công ty GẦN khu đô thị Vinhomes Ocean Park',
          'address' => 'Số 01 Voi Phục, Gia Lâm, Hà Nội',
        ],
        [
          'label' => 'Nhà xưởng sản xuất 2.000m² GẦN Ocean Park (Cách 10 phút)',
          'address' => 'Thôn Ngọc Trai, Xã Như Quỳnh, Tỉnh Hưng Yên',
        ],
      ],
    ]);

    $ref_link_repeater = new \Elementor\Repeater();
    $ref_link_repeater->add_control('label', [
      'label' => esc_html__('Nhãn liên kết', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => '',
      'label_block' => true,
    ]);
    $ref_link_repeater->add_control('url', [
      'label' => esc_html__('Đường dẫn (URL / Anchor)', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => '#du-an-mau-ocean-park',
      'label_block' => true,
    ]);

    $this->add_control('reference_links', [
      'label' => esc_html__('Danh mục liên kết nhanh theo Phân khu & Tòa', 'eai'),
      'type' => \Elementor\Controls_Manager::REPEATER,
      'fields' => $ref_link_repeater->get_controls(),
      'title_field' => '{{{ label }}}',
      'default' => [
        [
          'label' => 'Thiết kế thi công nội thất The London Ocean Park 1 (Tòa LD1 • LD2 • LD3)',
          'url' => '#du-an-mau-ocean-park',
        ],
        [
          'label' => 'Thiết kế thi công nội thất The Beverly Ocean Park 1 (Tòa BE1 • BE2 • BE3 • BE4)',
          'url' => '#du-an-mau-ocean-park',
        ],
        [
          'label' => 'Thiết kế thi công nội thất The Paris Ocean Park 1 — Chung cư Ocean Park 1',
          'url' => '#du-an-mau-ocean-park',
        ],
        [
          'label' => 'Thiết kế thi công nội thất Căn hộ LUMIÈRE SpringBay Ocean Park 2',
          'url' => '#du-an-mau-ocean-park',
        ],
        [
          'label' => 'Thiết kế thi công nội thất Dự án Masteri Trinity Square Ocean Park 2',
          'url' => '#du-an-mau-ocean-park',
        ],
        [
          'label' => 'Thiết kế thi công nội thất Căn hộ cao tầng Ocean Park 3 — Dự án căn hộ Ocean Park 3',
          'url' => '#du-an-mau-ocean-park',
        ],
      ],
    ]);

    $this->end_controls_section();

    $this->start_controls_section('section_form_options', [
      'label' => esc_html__('Cấu hình Form tư vấn (Cột phải)', 'eai'),
      'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
    ]);

    $this->add_control('form_title', [
      'label' => esc_html__('Tiêu đề Form', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => 'Đặt Lịch Tư Vấn 1:1, Xem Nhà Mẫu & Nhận Báo Giá Theo Mã Căn',
      'label_block' => true,
    ]);

    $this->add_control('form_subtitle', [
      'label' => esc_html__('Mô tả phụ dưới tiêu đề Form', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXTAREA,
      'rows' => 2,
      'default' => 'Miễn phí 100% khảo sát đo đạc tại tòa sau 15 phút, tư vấn mặt bằng công năng 2D, dự toán BOQ giá gốc tại xưởng và đưa đón tham quan Nhà mẫu Sao Biển 23 + Nhà xưởng Như Quỳnh.',
    ]);

    $this->add_control('zone_options_text', [
      'label' => esc_html__('Danh sách Phân khu / Tòa (mỗi dòng 1 lựa chọn)', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXTAREA,
      'rows' => 8,
      'default' => implode("\n", [
        'The London Ocean Park 1 — Tòa LD1 / Tòa LD2 / Tòa LD3',
        'The Beverly Ocean Park 1 — Tòa BE1 / Tòa BE2 / Tòa BE3 / Tòa BE4',
        'The Paris Ocean Park 1 — Chung cư Ocean Park 1',
        'LUMIÈRE SpringBay Ocean Park 2 — Căn hộ LUMIÈRE SpringBay',
        'Masteri Trinity Square Ocean Park 2 — Dự án Masteri Trinity Square',
        'Căn hộ cao tầng Ocean Park 3 — Dự án căn hộ Ocean Park 3',
        'Masteri Waterfront / The Zurich / The Pavilion / The Zenpark',
        'Phân khu khác tại Vinhomes Ocean Park 1 • 2 • 3',
      ]),
    ]);

    $this->add_control('submit_label', [
      'label' => esc_html__('Nhãn nút gửi form', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => 'Nhận Bản Vẽ 3D & Báo Giá Gốc Tại Xưởng',
      'label_block' => true,
    ]);

    $this->add_control('success_message', [
      'label' => esc_html__('Thông báo gửi thành công', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXTAREA,
      'rows' => 2,
      'default' => 'Cảm ơn Quý cư dân! Kiến trúc sư trưởng IC HOUSE tại Sao Biển 23 Vinhomes Ocean Park đã tiếp nhận yêu cầu và sẽ liên hệ qua số điện thoại/Zalo trong vòng 15 phút.',
    ]);

    $this->end_controls_section();
  }

  protected function render(): void
  {
    $settings = $this->get_settings_for_display();
    $widget_id = (string) $this->get_id();
    $props = eai_ocean_park_contact_form_get_rc_props($settings, $widget_id);

    if (eai_ocean_park_contact_form_props_are_empty($props)) {
      if (eai_is_elementor_edit_mode()) {
        $props = eai_ocean_park_contact_form_get_editor_sample_props($settings, $widget_id);
      } else {
        eai_render_template(self::TEMPLATE, ['empty' => true]);
        return;
      }
    }

    $result = eai_rc_render_html('OceanParkContactFormWrapper', $props);
    eai_render_template(self::TEMPLATE, [
      'html' => is_wp_error($result) ? '' : $result['html'],
      'error' => is_wp_error($result) ? $result : null,
      'form_key' => $props['formKey'],
      'cf7_form_id' => (int) ($settings['cf7_form_id'] ?? 0),
    ]);
  }
}
