<?php

class EAI_Footer_Widget extends \Elementor\Widget_Base
{

  public function get_name(): string
  {
    return 'eai_footer_widget';
  }

  public function get_title(): string
  {
    return esc_html__('ICHouse — Footer', 'eai');
  }

  public function get_icon(): string
  {
    return 'eicon-footer';
  }

  public function get_categories(): array
  {
    return eai_get_widget_categories();
  }

  public function get_keywords(): array
  {
    return ['footer', 'chan trang', 'menu', 'social', 'fanpage', 'eai', 'ichouse'];
  }

  protected function register_controls()
  {
    $this->register_menu_column_controls(1, 'VỀ CHÚNG TÔI');
    $this->register_menu_column_controls(2, 'CHÍNH SÁCH');
    $this->register_menu_column_controls(3, 'HỖ TRỢ KHÁCH HÀNG');
    $this->register_payment_controls();
    $this->register_social_controls();
    $this->register_brand_controls();
    $this->register_contact_controls();
    $this->register_fanpages_controls();
  }

  /**
   * @param int $index 1-based column index
   */
  protected function register_menu_column_controls(int $index, string $default_title): void
  {
    $this->start_controls_section(
      "section_menu_col_{$index}",
      [
        'label' => sprintf(
          /* translators: %d: menu column number */
          esc_html__('Menu cột %d', 'eai'),
          $index
        ),
        'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_control(
      "menu_col_{$index}_title",
      [
        'label' => esc_html__('Tiêu đề cột', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => $default_title,
        'label_block' => true,
      ]
    );

    $link_repeater = new \Elementor\Repeater();

    $link_repeater->add_control(
      'label',
      [
        'label' => esc_html__('Nhãn', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => '',
        'label_block' => true,
      ]
    );

    $link_repeater->add_control(
      'link',
      [
        'label' => esc_html__('Liên kết', 'eai'),
        'type' => \Elementor\Controls_Manager::URL,
        'options' => ['url', 'is_external', 'nofollow'],
        'default' => [
          'url' => '',
          'is_external' => false,
          'nofollow' => false,
        ],
        'label_block' => true,
      ]
    );

    $this->add_control(
      "menu_col_{$index}_links",
      [
        'label' => esc_html__('Danh sách liên kết', 'eai'),
        'type' => \Elementor\Controls_Manager::REPEATER,
        'fields' => $link_repeater->get_controls(),
        'default' => [],
        'title_field' => '{{{ label }}}',
      ]
    );

    $this->end_controls_section();
  }

  protected function register_payment_controls(): void
  {
    $this->start_controls_section(
      'section_payment',
      [
        'label' => esc_html__('Phương thức thanh toán', 'eai'),
        'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_control(
      'payment_title',
      [
        'label' => esc_html__('Tiêu đề', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => 'PHƯƠNG THỨC THANH TOÁN',
        'label_block' => true,
      ]
    );

    $logo_repeater = new \Elementor\Repeater();

    $logo_repeater->add_control(
      'image',
      [
        'label' => esc_html__('Logo', 'eai'),
        'type' => \Elementor\Controls_Manager::MEDIA,
        'label_block' => true,
      ]
    );

    $logo_repeater->add_control(
      'image_resolution',
      [
        'label' => esc_html__('Image Resolution', 'eai'),
        'type' => \Elementor\Controls_Manager::SELECT,
        'default' => 'medium',
        'options' => eai_get_image_size_options(),
      ]
    );

    $logo_repeater->add_control(
      'alt',
      [
        'label' => esc_html__('Alt text (optional)', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => '',
        'label_block' => true,
      ]
    );

    $this->add_control(
      'payment_logos',
      [
        'label' => esc_html__('Logo thanh toán', 'eai'),
        'type' => \Elementor\Controls_Manager::REPEATER,
        'fields' => $logo_repeater->get_controls(),
        'default' => [],
        'title_field' => '{{{ alt }}}',
      ]
    );

    $this->end_controls_section();
  }

  protected function register_social_controls(): void
  {
    $this->start_controls_section(
      'section_social',
      [
        'label' => esc_html__('Mạng xã hội', 'eai'),
        'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_control(
      'social_title',
      [
        'label' => esc_html__('Tiêu đề', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => 'KẾT NỐI VỚI CHÚNG TÔI',
        'label_block' => true,
      ]
    );

    $social_repeater = new \Elementor\Repeater();

    $social_repeater->add_control(
      'icon',
      [
        'label' => esc_html__('Icon', 'eai'),
        'type' => \Elementor\Controls_Manager::MEDIA,
        'label_block' => true,
      ]
    );

    $social_repeater->add_control(
      'icon_resolution',
      [
        'label' => esc_html__('Image Resolution', 'eai'),
        'type' => \Elementor\Controls_Manager::SELECT,
        'default' => 'thumbnail',
        'options' => eai_get_image_size_options(),
      ]
    );

    $social_repeater->add_control(
      'icon_alt',
      [
        'label' => esc_html__('Alt text (optional)', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => '',
        'label_block' => true,
      ]
    );

    $social_repeater->add_control(
      'link',
      [
        'label' => esc_html__('Liên kết', 'eai'),
        'type' => \Elementor\Controls_Manager::URL,
        'options' => ['url', 'is_external', 'nofollow'],
        'default' => [
          'url' => '',
          'is_external' => true,
          'nofollow' => false,
        ],
        'label_block' => true,
      ]
    );

    $this->add_control(
      'social_links',
      [
        'label' => esc_html__('Liên kết mạng xã hội', 'eai'),
        'type' => \Elementor\Controls_Manager::REPEATER,
        'fields' => $social_repeater->get_controls(),
        'default' => [],
        'title_field' => '{{{ icon_alt }}}',
      ]
    );

    $this->end_controls_section();
  }

  protected function register_brand_controls(): void
  {
    $this->start_controls_section(
      'section_brand',
      [
        'label' => esc_html__('Thương hiệu & hotline', 'eai'),
        'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_control(
      'brand_logo',
      [
        'label' => esc_html__('Logo', 'eai'),
        'type' => \Elementor\Controls_Manager::MEDIA,
        'label_block' => true,
      ]
    );

    $this->add_control(
      'brand_logo_resolution',
      [
        'label' => esc_html__('Logo resolution', 'eai'),
        'type' => \Elementor\Controls_Manager::SELECT,
        'default' => 'medium',
        'options' => eai_get_image_size_options(),
      ]
    );

    $this->add_control(
      'brand_logo_link',
      [
        'label' => esc_html__('Liên kết logo', 'eai'),
        'type' => \Elementor\Controls_Manager::URL,
        'options' => ['url', 'is_external', 'nofollow'],
        'default' => [
          'url' => '/',
          'is_external' => false,
          'nofollow' => false,
        ],
        'label_block' => true,
      ]
    );

    $this->add_control(
      'brand_description',
      [
        'label' => esc_html__('Mô tả', 'eai'),
        'type' => \Elementor\Controls_Manager::WYSIWYG,
        'default' => '',
        'label_block' => true,
      ]
    );

    $badge_repeater = new \Elementor\Repeater();

    $badge_repeater->add_control(
      'image',
      [
        'label' => esc_html__('Huy hiệu / chứng nhận', 'eai'),
        'type' => \Elementor\Controls_Manager::MEDIA,
        'label_block' => true,
      ]
    );

    $badge_repeater->add_control(
      'image_resolution',
      [
        'label' => esc_html__('Image Resolution', 'eai'),
        'type' => \Elementor\Controls_Manager::SELECT,
        'default' => 'medium',
        'options' => eai_get_image_size_options(),
      ]
    );

    $badge_repeater->add_control(
      'alt',
      [
        'label' => esc_html__('Alt text (optional)', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => '',
        'label_block' => true,
      ]
    );

    $this->add_control(
      'brand_badges',
      [
        'label' => esc_html__('Huy hiệu', 'eai'),
        'type' => \Elementor\Controls_Manager::REPEATER,
        'fields' => $badge_repeater->get_controls(),
        'default' => [],
        'title_field' => '{{{ alt }}}',
      ]
    );

    $this->add_control(
      'brand_hotline_label',
      [
        'label' => esc_html__('Nhãn hotline (tùy chọn)', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => 'Hotline',
        'label_block' => true,
      ]
    );

    $this->add_control(
      'brand_hotline_text',
      [
        'label' => esc_html__('Số hotline hiển thị', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => '1900 1234',
        'label_block' => true,
      ]
    );

    $this->add_control(
      'brand_hotline_link',
      [
        'label' => esc_html__('Liên kết hotline (tel:)', 'eai'),
        'type' => \Elementor\Controls_Manager::URL,
        'options' => ['url', 'is_external', 'nofollow'],
        'default' => [
          'url' => 'tel:19001234',
          'is_external' => false,
          'nofollow' => false,
        ],
        'label_block' => true,
      ]
    );

    $this->end_controls_section();
  }

  protected function register_contact_controls(): void
  {
    $this->start_controls_section(
      'section_contact',
      [
        'label' => esc_html__('Thông tin liên hệ', 'eai'),
        'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
      ]
    );

    $block_repeater = new \Elementor\Repeater();

    $block_repeater->add_control(
      'title',
      [
        'label' => esc_html__('Tiêu đề', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => '',
        'label_block' => true,
      ]
    );

    $block_repeater->add_control(
      'content',
      [
        'label' => esc_html__('Nội dung', 'eai'),
        'type' => \Elementor\Controls_Manager::WYSIWYG,
        'default' => '',
        'label_block' => true,
      ]
    );

    $this->add_control(
      'contact_blocks',
      [
        'label' => esc_html__('Khối liên hệ', 'eai'),
        'type' => \Elementor\Controls_Manager::REPEATER,
        'fields' => $block_repeater->get_controls(),
        'default' => [],
        'title_field' => '{{{ title }}}',
      ]
    );

    $this->end_controls_section();
  }

  protected function register_fanpages_controls(): void
  {
    $this->start_controls_section(
      'section_fanpages',
      [
        'label' => esc_html__('Nhà máy & Fanpage', 'eai'),
        'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_control(
      'factories_title',
      [
        'label' => esc_html__('Tiêu đề nhà máy', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => 'NHÀ MÁY SẢN XUẤT',
        'label_block' => true,
      ]
    );

    $this->add_control(
      'factories_content',
      [
        'label' => esc_html__('Nội dung nhà máy', 'eai'),
        'type' => \Elementor\Controls_Manager::WYSIWYG,
        'default' => '',
        'label_block' => true,
      ]
    );

    $this->add_control(
      'fanpage_title',
      [
        'label' => esc_html__('Tiêu đề fanpage', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => 'Fanpage Chính Thức',
        'label_block' => true,
      ]
    );

    $embed_repeater = new \Elementor\Repeater();

    $embed_repeater->add_control(
      'facebook_page_url',
      [
        'label' => esc_html__('URL trang Facebook', 'eai'),
        'type' => \Elementor\Controls_Manager::URL,
        'options' => ['url'],
        'default' => ['url' => ''],
        'label_block' => true,
        'description' => esc_html__('Tự tạo iframe Page Plugin. Bỏ trống nếu dùng HTML tùy chỉnh bên dưới.', 'eai'),
      ]
    );

    $embed_repeater->add_control(
      'embed_html',
      [
        'label' => esc_html__('HTML embed (tùy chọn)', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXTAREA,
        'default' => '',
        'rows' => 4,
        'description' => esc_html__('Ưu tiên hơn URL Facebook khi có nội dung.', 'eai'),
      ]
    );

    $this->add_control(
      'fanpage_embeds',
      [
        'label' => esc_html__('Fanpage embed', 'eai'),
        'type' => \Elementor\Controls_Manager::REPEATER,
        'fields' => $embed_repeater->get_controls(),
        'default' => [],
        'title_field' => '{{{ facebook_page_url.url }}}',
      ]
    );

    $this->end_controls_section();
  }

  /**
   * @return array<int, array{title: string, links: array<int, array<string, mixed>>}>
   */
  protected function get_menu_columns_from_settings(array $settings): array
  {
    $columns = [];

    for ($i = 1; $i <= 3; $i++) {
      $columns[] = [
        'title' => (string) ($settings["menu_col_{$i}_title"] ?? ''),
        'links' => is_array($settings["menu_col_{$i}_links"] ?? null)
          ? $settings["menu_col_{$i}_links"]
          : [],
      ];
    }

    return $columns;
  }

  /**
   * @param array<int, array<string, mixed>> $logos
   * @return array<int, array<string, mixed>>
   */
  protected function map_payment_logos(array $logos): array
  {
    $mapped = eai_rc_map_partner_logos($logos);
    $dimensions = ['width' => 48, 'height' => 32];

    foreach ($mapped as $index => $media) {
      $mapped[$index]['display_dimensions'] = $dimensions;
    }

    return $mapped;
  }

  protected function get_rc_props(): array
  {
    $settings = $this->get_settings_for_display();
    $payment_logos = is_array($settings['payment_logos'] ?? null) ? $settings['payment_logos'] : [];
    $social_links = is_array($settings['social_links'] ?? null) ? $settings['social_links'] : [];
    $contact_blocks = is_array($settings['contact_blocks'] ?? null) ? $settings['contact_blocks'] : [];
    $fanpage_embeds = is_array($settings['fanpage_embeds'] ?? null) ? $settings['fanpage_embeds'] : [];

    $payment_title = trim((string) ($settings['payment_title'] ?? ''));
    $social_title = trim((string) ($settings['social_title'] ?? ''));
    $fanpage_title = trim((string) ($settings['fanpage_title'] ?? ''));

    return [
      'top' => [
        'menuColumns' => eai_rc_map_footer_menu_columns(
          $this->get_menu_columns_from_settings($settings)
        ),
        'payment' => array_filter([
          'title' => $payment_title !== '' ? $payment_title : null,
          'logos' => $this->map_payment_logos($payment_logos),
        ], static function ($value) {
          return $value !== null;
        }),
        'social' => array_filter([
          'title' => $social_title !== '' ? $social_title : null,
          'links' => eai_rc_map_footer_social_links($social_links),
        ], static function ($value) {
          return $value !== null;
        }),
      ],
      'bottom' => [
        'brand' => eai_rc_map_footer_brand($settings),
        'contact' => [
          'blocks' => eai_rc_map_footer_contact_blocks($contact_blocks),
        ],
        'fanpages' => array_filter([
          'factories' => [
            'title' => (string) ($settings['factories_title'] ?? ''),
            'contentHtml' => (string) ($settings['factories_content'] ?? ''),
          ],
          'fanpageTitle' => $fanpage_title !== '' ? $fanpage_title : null,
          'embeds' => eai_rc_map_footer_fanpage_embeds($fanpage_embeds),
        ], static function ($value) {
          return $value !== null;
        }),
      ],
    ];
  }

  protected function render(): void
  {
    $props = $this->get_rc_props();
    $result = eai_rc_render_html('Footer', $props);

    eai_render_template('templates/EAI-footer.php', [
      'html' => is_wp_error($result) ? '' : $result['html'],
      'error' => is_wp_error($result) ? $result : null,
    ]);
  }
}
