<?php

class EAI_Construction_Footer_Widget extends \Elementor\Widget_Base
{
  public function get_name(): string
  {
    return 'eai_construction_footer_widget';
  }

  public function get_title(): string
  {
    return esc_html__('ICHouse — Construction Footer', 'eai');
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
    return ['construction', 'footer', 'ICHOUSE', 'chan trang', 'eai', 'ichouse'];
  }

  protected function register_controls()
  {
    $this->start_controls_section(
      'section_brand',
      [
        'label' => esc_html__('Brand', 'eai'),
        'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_control(
      'class_name',
      [
        'label' => esc_html__('Class name', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => '',
      ]
    );

    $this->add_control(
      'logo',
      [
        'label' => esc_html__('Logo', 'eai'),
        'type' => \Elementor\Controls_Manager::MEDIA,
        'default' => [
          'url' => 'https://placehold.co/220x80/png?text=ICHOUSE',
        ],
      ]
    );

    $this->add_control(
      'logo_resolution',
      [
        'label' => esc_html__('Logo Resolution', 'eai'),
        'type' => \Elementor\Controls_Manager::SELECT,
        'default' => 'medium',
        'options' => eai_get_image_size_options(),
      ]
    );

    $this->end_controls_section();

    $this->start_controls_section(
      'section_menu',
      [
        'label' => esc_html__('Menu', 'eai'),
        'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
      ]
    );

    $menu_repeater = new \Elementor\Repeater();

    $menu_repeater->add_control(
      'label',
      [
        'label' => esc_html__('Label', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => esc_html__('Trang chủ', 'eai'),
        'label_block' => true,
      ]
    );

    $menu_repeater->add_control(
      'link',
      [
        'label' => esc_html__('Link', 'eai'),
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
      'menu_items',
      [
        'label' => esc_html__('Menu items', 'eai'),
        'type' => \Elementor\Controls_Manager::REPEATER,
        'fields' => $menu_repeater->get_controls(),
        'default' => [
          [
            'label' => 'Trang chủ',
            'link' => ['url' => '/', 'is_external' => false, 'nofollow' => false],
          ],
          [
            'label' => 'Về chúng tôi',
            'link' => ['url' => '/ve-chung-toi', 'is_external' => false, 'nofollow' => false],
          ],
          [
            'label' => 'Lĩnh vực',
            'link' => ['url' => '/linh-vuc', 'is_external' => false, 'nofollow' => false],
          ],
          [
            'label' => 'Dự án',
            'link' => ['url' => '/du-an', 'is_external' => false, 'nofollow' => false],
          ],
          [
            'label' => 'Tin tức',
            'link' => ['url' => '/tin-tuc', 'is_external' => false, 'nofollow' => false],
          ],
          [
            'label' => 'Hợp tác',
            'link' => ['url' => '/hop-tac', 'is_external' => false, 'nofollow' => false],
          ],
          [
            'label' => 'Tuyển dụng',
            'link' => ['url' => '/tuyen-dung', 'is_external' => false, 'nofollow' => false],
          ],
          [
            'label' => 'Liên hệ',
            'link' => ['url' => '/lien-he', 'is_external' => false, 'nofollow' => false],
          ],
        ],
        'title_field' => '{{{ label }}}',
      ]
    );

    $this->end_controls_section();

    $this->start_controls_section(
      'section_company',
      [
        'label' => esc_html__('Company & social', 'eai'),
        'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_control(
      'company_name',
      [
        'label' => esc_html__('Company name', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXTAREA,
        'default' => 'Công ty cổ phần Tư vấn Kiến trúc, Kỹ thuật và Xây dựng ICHOUSE',
        'rows' => 2,
      ]
    );

    $social_repeater = new \Elementor\Repeater();

    $social_repeater->add_control(
      'aria_label',
      [
        'label' => esc_html__('Aria label', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => 'Facebook',
        'label_block' => true,
      ]
    );

    $social_repeater->add_control(
      'icon',
      [
        'label' => esc_html__('Icon', 'eai'),
        'type' => \Elementor\Controls_Manager::MEDIA,
        'default' => [
          'url' => 'https://placehold.co/20x20/png?text=f',
        ],
      ]
    );

    $social_repeater->add_control(
      'icon_resolution',
      [
        'label' => esc_html__('Icon Resolution', 'eai'),
        'type' => \Elementor\Controls_Manager::SELECT,
        'default' => 'thumbnail',
        'options' => eai_get_image_size_options(),
      ]
    );

    $social_repeater->add_control(
      'link',
      [
        'label' => esc_html__('Link', 'eai'),
        'type' => \Elementor\Controls_Manager::URL,
        'options' => ['url', 'is_external', 'nofollow'],
        'default' => [
          'url' => 'https://facebook.com',
          'is_external' => true,
          'nofollow' => false,
        ],
        'label_block' => true,
      ]
    );

    $this->add_control(
      'social_links',
      [
        'label' => esc_html__('Social links', 'eai'),
        'type' => \Elementor\Controls_Manager::REPEATER,
        'fields' => $social_repeater->get_controls(),
        'default' => [
          [
            'aria_label' => 'Facebook',
            'icon' => ['url' => 'https://placehold.co/20x20/png?text=f'],
            'link' => ['url' => 'https://facebook.com', 'is_external' => true, 'nofollow' => false],
          ],
          [
            'aria_label' => 'TikTok',
            'icon' => ['url' => 'https://placehold.co/20x20/png?text=tt'],
            'link' => ['url' => 'https://tiktok.com', 'is_external' => true, 'nofollow' => false],
          ],
          [
            'aria_label' => 'YouTube',
            'icon' => ['url' => 'https://placehold.co/20x20/png?text=yt'],
            'link' => ['url' => 'https://youtube.com', 'is_external' => true, 'nofollow' => false],
          ],
          [
            'aria_label' => 'Instagram',
            'icon' => ['url' => 'https://placehold.co/20x20/png?text=ig'],
            'link' => ['url' => 'https://instagram.com', 'is_external' => true, 'nofollow' => false],
          ],
        ],
        'title_field' => '{{{ aria_label }}}',
      ]
    );

    $this->end_controls_section();

    $this->start_controls_section(
      'section_contact',
      [
        'label' => esc_html__('Contact', 'eai'),
        'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_control(
      'phone_text',
      [
        'label' => esc_html__('Phone text', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => '0899 984 988',
        'label_block' => true,
      ]
    );

    $this->add_control(
      'phone_link',
      [
        'label' => esc_html__('Phone link', 'eai'),
        'type' => \Elementor\Controls_Manager::URL,
        'options' => ['url', 'is_external', 'nofollow'],
        'default' => [
          'url' => 'tel:0899984988',
          'is_external' => false,
          'nofollow' => false,
        ],
        'label_block' => true,
      ]
    );

    $address_repeater = new \Elementor\Repeater();

    $address_repeater->add_control(
      'line',
      [
        'label' => esc_html__('Address line', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXTAREA,
        'default' => '',
        'rows' => 2,
      ]
    );

    $this->add_control(
      'addresses',
      [
        'label' => esc_html__('Addresses', 'eai'),
        'type' => \Elementor\Controls_Manager::REPEATER,
        'fields' => $address_repeater->get_controls(),
        'default' => [
          [
            'line' => 'Số 07 ngõ 71, phố Hoàng Văn Thái, phường Phương Liệt, Hà Nội',
          ],
          [
            'line' => 'Số 506/15/24, đường 3/2, phường Diễn Hồng, TP. Hồ Chí Minh',
          ],
        ],
        'title_field' => '{{{ line }}}',
      ]
    );

    $this->add_control(
      'email_text',
      [
        'label' => esc_html__('Email text', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => 'contact@ICHOUSE.vn',
        'label_block' => true,
      ]
    );

    $this->add_control(
      'email_link',
      [
        'label' => esc_html__('Email link', 'eai'),
        'type' => \Elementor\Controls_Manager::URL,
        'options' => ['url', 'is_external', 'nofollow'],
        'default' => [
          'url' => 'mailto:contact@ICHOUSE.vn',
          'is_external' => false,
          'nofollow' => false,
        ],
        'label_block' => true,
      ]
    );

    $this->end_controls_section();

    $this->start_controls_section(
      'section_bottom',
      [
        'label' => esc_html__('Bottom', 'eai'),
        'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_control(
      'copyright',
      [
        'label' => esc_html__('Copyright', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => 'Copyright © 2026 ICHOUSE Vietnam. All rights reserved.',
        'label_block' => true,
      ]
    );

    $this->add_control(
      'badge',
      [
        'label' => esc_html__('Badge (DMCA)', 'eai'),
        'type' => \Elementor\Controls_Manager::MEDIA,
        'default' => [
          'url' => 'https://placehold.co/120x32/png?text=DMCA',
        ],
      ]
    );

    $this->add_control(
      'badge_resolution',
      [
        'label' => esc_html__('Badge Resolution', 'eai'),
        'type' => \Elementor\Controls_Manager::SELECT,
        'default' => 'medium',
        'options' => eai_get_image_size_options(),
      ]
    );

    $this->end_controls_section();
  }

  protected function get_rc_props(): array
  {
    return eai_construction_footer_get_rc_props($this->get_settings_for_display());
  }

  protected function render(): void
  {
    $props = $this->get_rc_props();
    $result = eai_rc_render_html('ConstructionFooter', $props);

    eai_render_template('templates/EAI-construction-footer.php', [
      'html' => is_wp_error($result) ? '' : $result['html'],
      'error' => is_wp_error($result) ? $result : null,
    ]);
  }
}
