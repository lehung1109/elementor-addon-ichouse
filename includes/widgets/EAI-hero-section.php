<?php

class EAI_Hero_Section_Widget extends \Elementor\Widget_Base
{

  public function get_name(): string
  {
    return 'eai_hero_section_widget';
  }

  public function get_title(): string
  {
    return esc_html__('ICHouse — Hero Section', 'eai');
  }

  public function get_icon(): string
  {
    return 'eicon-banner';
  }

  public function get_categories(): array
  {
    return eai_get_widget_categories();
  }

  public function get_keywords(): array
  {
    return ['hero', 'hero section', 'banner', 'eai', 'ichouse'];
  }

  protected function register_controls()
  {
    $this->start_controls_section(
      'section_content',
      [
        'label' => esc_html__('Content', 'eai'),
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
      'background_image',
      [
        'label' => esc_html__('Background Image', 'eai'),
        'type' => \Elementor\Controls_Manager::MEDIA,
        'default' => [
          'url' => 'https://placehold.co/1600x700/jpg',
        ],
      ]
    );

    $this->add_control(
      'background_image_resolution',
      [
        'label' => esc_html__('Background Image Resolution', 'eai'),
        'type' => \Elementor\Controls_Manager::SELECT,
        'default' => 'large',
        'options' => \eai_get_image_size_options(),
      ]
    );

    $this->add_control(
      'subtitle',
      [
        'label' => esc_html__('Subtitle', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => esc_html__('Thi công nội thất chung cư đẹp', 'eai'),
        'label_block' => true,
      ]
    );

    $this->add_control(
      'title',
      [
        'label' => esc_html__('Title', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => esc_html__('Cùng Kiến trúc sư kinh nghiệm', 'eai'),
        'label_block' => true,
      ]
    );

    $this->add_control(
      'title_heading',
      [
        'label' => esc_html__('Title heading level', 'eai'),
        'type' => \Elementor\Controls_Manager::SELECT,
        'default' => 'h1',
        'options' => [
          'h1' => esc_html__('H1', 'eai'),
          'h2' => esc_html__('H2', 'eai'),
        ],
        'description' => esc_html__(
          'Dùng H2 nếu trang đã có Page Title Bar (một H1 trên trang).',
          'eai'
        ),
      ]
    );

    $this->add_control(
      'content_centered',
      [
        'label' => esc_html__('Căn giữa nội dung', 'eai'),
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label_on' => esc_html__('Yes', 'eai'),
        'label_off' => esc_html__('No', 'eai'),
        'return_value' => 'yes',
        'default' => '',
      ]
    );

    $this->add_control(
      'content_full_width',
      [
        'label' => esc_html__('Nội dung full width', 'eai'),
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label_on' => esc_html__('Yes', 'eai'),
        'label_off' => esc_html__('No', 'eai'),
        'return_value' => 'yes',
        'default' => '',
        'description' => esc_html__(
          'Bỏ giới hạn max-width của khối nội dung (mặc định ~max-w-xl).',
          'eai'
        ),
      ]
    );

    $this->add_control(
      'html_text',
      [
        'label' => esc_html__('HTML Text', 'eai'),
        'type' => \Elementor\Controls_Manager::WYSIWYG,
        'default' => '<ul><li>Đa dạng phong cách bởi nhân lực 30+ KTS</li><li>Kỹ năng trong nghề từ 2 - 10 năm kinh nghiệm</li><li>Trải qua 5000+ Thiết kế chung cư từ phân khúc cao cấp tới giá rẻ</li></ul>',
        'label_block' => true,
      ]
    );

    $this->add_control(
      'button_label',
      [
        'label' => esc_html__('Button Label', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => esc_html__('Tư vấn miễn phí', 'eai'),
        'label_block' => true,
      ]
    );

    $this->add_control(
      'button_link',
      [
        'label' => esc_html__('Button Link', 'eai'),
        'type' => \Elementor\Controls_Manager::URL,
        'options' => ['url', 'is_external', 'nofollow'],
        'default' => [
          'url' => '#tu-van',
          'is_external' => false,
          'nofollow' => false,
        ],
        'label_block' => true,
      ]
    );

    $this->add_control(
      'button_variant',
      [
        'label' => esc_html__('Màu nút', 'eai'),
        'type' => \Elementor\Controls_Manager::SELECT,
        'default' => 'default',
        'options' => [
          'default' => esc_html__('Mặc định (cam)', 'eai'),
          'yellow' => esc_html__('Vàng', 'eai'),
        ],
      ]
    );

    $this->end_controls_section();
  }

  protected function render(): void
  {
    $settings = $this->get_settings_for_display();
    $background_image = is_array($settings['background_image'] ?? null)
      ? $settings['background_image']
      : [];
    $resolution = (string) ($settings['background_image_resolution'] ?? 'large');

    $title_heading = (string) ($settings['title_heading'] ?? 'h1');
    $button_variant = (string) ($settings['button_variant'] ?? 'default');

    $props = [
      'className' => (string) ($settings['class_name'] ?? ''),
      'backgroundImage' => eai_rc_map_media_model($background_image, [], null, $resolution),
      'subtitle' => (string) ($settings['subtitle'] ?? ''),
      'title' => (string) ($settings['title'] ?? ''),
      'titleHeading' => in_array($title_heading, ['h1', 'h2'], true) ? $title_heading : 'h1',
      'contentCentered' => ($settings['content_centered'] ?? '') === 'yes',
      'contentFullWidth' => ($settings['content_full_width'] ?? '') === 'yes',
      'htmlText' => (string) ($settings['html_text'] ?? ''),
      'buttonLabel' => (string) ($settings['button_label'] ?? ''),
      'buttonLink' => eai_rc_map_link(
        is_array($settings['button_link'] ?? null) ? $settings['button_link'] : []
      ),
      'buttonVariant' => in_array($button_variant, ['default', 'yellow'], true)
        ? $button_variant
        : 'default',
    ];

    $result = eai_rc_render_html('HeroSection', $props);

    eai_render_template('templates/EAI-hero-section.php', [
      'html' => is_wp_error($result) ? '' : $result['html'],
      'error' => is_wp_error($result) ? $result : null,
    ]);
  }
}

