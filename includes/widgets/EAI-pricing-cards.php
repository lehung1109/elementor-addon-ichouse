<?php

class EAI_Pricing_Cards_Widget extends \Elementor\Widget_Base
{
  public function get_name(): string
  {
    return 'eai_pricing_cards_widget';
  }

  public function get_title(): string
  {
    return esc_html__('ICHouse — Pricing Cards', 'eai');
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
    return ['pricing', 'cards', 'bang gia', 'goi', 'eai', 'ichouse'];
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

    $repeater = new \Elementor\Repeater();

    $repeater->add_control(
      'title',
      [
        'label' => esc_html__('Title', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => esc_html__('Gói cơ bản', 'eai'),
        'label_block' => true,
      ]
    );

    $repeater->add_control(
      'price',
      [
        'label' => esc_html__('Price', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => esc_html__('180,000đ/m²', 'eai'),
        'label_block' => true,
      ]
    );

    $repeater->add_control(
      'html_text',
      [
        'label' => esc_html__('HTML Text', 'eai'),
        'type' => \Elementor\Controls_Manager::WYSIWYG,
        'default' => '<ul><li>Phong cách: Hiện đại, Tối giản</li><li>Tư vấn thiết kế miễn phí</li><li>Thời gian triển khai: 10-20 ngày</li></ul>',
        'label_block' => true,
      ]
    );

    $repeater->add_control(
      'button_label',
      [
        'label' => esc_html__('Button Label', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => esc_html__('Đăng ký thiết kế', 'eai'),
        'label_block' => true,
      ]
    );

    $repeater->add_control(
      'button_link',
      [
        'label' => esc_html__('Button Link', 'eai'),
        'type' => \Elementor\Controls_Manager::URL,
        'options' => ['url', 'is_external', 'nofollow'],
        'default' => [
          'url' => '/lien-he',
          'is_external' => false,
          'nofollow' => false,
        ],
        'label_block' => true,
      ]
    );

    $repeater->add_control(
      'active',
      [
        'label' => esc_html__('Active', 'eai'),
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label_on' => esc_html__('Yes', 'eai'),
        'label_off' => esc_html__('No', 'eai'),
        'return_value' => 'yes',
        'default' => '',
      ]
    );

    $this->add_control(
      'items',
      [
        'label' => esc_html__('Items', 'eai'),
        'type' => \Elementor\Controls_Manager::REPEATER,
        'fields' => $repeater->get_controls(),
        'default' => [],
        'title_field' => '{{{ title }}}',
      ]
    );

    $this->end_controls_section();
  }

  protected function render(): void
  {
    $settings = $this->get_settings_for_display();
    $items = is_array($settings['items'] ?? null) ? $settings['items'] : [];

    $props = [
      'className' => (string) ($settings['class_name'] ?? ''),
      'items' => array_values(array_filter(array_map(function ($item) {
        $item = is_array($item) ? $item : [];
        $button_link = is_array($item['button_link'] ?? null) ? $item['button_link'] : [];

        return [
          'title' => (string) ($item['title'] ?? ''),
          'price' => (string) ($item['price'] ?? ''),
          'htmlText' => (string) ($item['html_text'] ?? ''),
          'buttonLabel' => (string) ($item['button_label'] ?? ''),
          'buttonLink' => eai_rc_map_link($button_link),
          'active' => (($item['active'] ?? '') === 'yes'),
        ];
      }, $items), function ($item) {
        return is_array($item)
          && ((string) ($item['title'] ?? '')) !== ''
          && ((string) ($item['price'] ?? '')) !== '';
      })),
    ];

    if (empty($props['items'])) {
      eai_render_template('templates/EAI-pricing-cards.php', [
        'empty' => true,
      ]);
      return;
    }

    $result = eai_rc_render_html('PricingCards', $props);

    eai_render_template('templates/EAI-pricing-cards.php', [
      'html' => is_wp_error($result) ? '' : $result['html'],
      'error' => is_wp_error($result) ? $result : null,
    ]);
  }
}

