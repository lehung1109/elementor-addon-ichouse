<?php

class EAI_Floating_Contact_Widget extends \Elementor\Widget_Base
{
  public function get_name(): string
  {
    return 'eai_floating_contact_widget';
  }

  public function get_title(): string
  {
    return esc_html__('ICHouse — Floating Contact', 'eai');
  }

  public function get_icon(): string
  {
    return 'eicon-tel-field';
  }

  public function get_categories(): array
  {
    return eai_get_widget_categories();
  }

  public function get_keywords(): array
  {
    return ['floating', 'contact', 'zalo', 'messenger', 'phone', 'hotline', 'eai', 'ichouse'];
  }

  protected function register_controls()
  {
    $this->start_controls_section(
      'section_general',
      [
        'label' => esc_html__('General', 'eai'),
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
      'icon_resolution',
      [
        'label' => esc_html__('Icon Resolution', 'eai'),
        'type' => \Elementor\Controls_Manager::SELECT,
        'default' => 'thumbnail',
        'options' => eai_get_image_size_options(),
      ]
    );

    $this->end_controls_section();

    $this->start_controls_section(
      'section_messenger',
      [
        'label' => esc_html__('Messenger', 'eai'),
        'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_control(
      'messenger_label',
      [
        'label' => esc_html__('Label', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => 'ĐĂNG KÝ TƯ VẤN',
        'label_block' => true,
      ]
    );

    $this->add_control(
      'messenger_icon',
      [
        'label' => esc_html__('Icon', 'eai'),
        'type' => \Elementor\Controls_Manager::MEDIA,
        'default' => [
          'url' => 'https://placehold.co/20x20/ffffff/0084FF/png?text=m',
        ],
      ]
    );

    $this->add_control(
      'messenger_link',
      [
        'label' => esc_html__('Link', 'eai'),
        'type' => \Elementor\Controls_Manager::URL,
        'options' => ['url', 'is_external', 'nofollow'],
        'default' => [
          'url' => 'https://m.me/',
          'is_external' => true,
          'nofollow' => false,
        ],
        'label_block' => true,
      ]
    );

    $this->end_controls_section();

    $this->start_controls_section(
      'section_zalo',
      [
        'label' => esc_html__('Zalo', 'eai'),
        'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_control(
      'zalo_label',
      [
        'label' => esc_html__('Label', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => 'CHAT ZALO',
        'label_block' => true,
      ]
    );

    $this->add_control(
      'zalo_icon',
      [
        'label' => esc_html__('Icon', 'eai'),
        'type' => \Elementor\Controls_Manager::MEDIA,
        'default' => [
          'url' => 'https://placehold.co/20x20/ffffff/0068FF/png?text=z',
        ],
      ]
    );

    $this->add_control(
      'zalo_link',
      [
        'label' => esc_html__('Link', 'eai'),
        'type' => \Elementor\Controls_Manager::URL,
        'options' => ['url', 'is_external', 'nofollow'],
        'default' => [
          'url' => 'https://zalo.me/',
          'is_external' => true,
          'nofollow' => false,
        ],
        'label_block' => true,
      ]
    );

    $this->end_controls_section();

    $this->start_controls_section(
      'section_phone',
      [
        'label' => esc_html__('Phone', 'eai'),
        'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_control(
      'phone_label',
      [
        'label' => esc_html__('Label', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => '0000 000 000',
        'label_block' => true,
      ]
    );

    $this->add_control(
      'phone_link',
      [
        'label' => esc_html__('Link', 'eai'),
        'type' => \Elementor\Controls_Manager::URL,
        'options' => ['url', 'is_external', 'nofollow'],
        'default' => [
          'url' => 'tel:0000000000',
          'is_external' => false,
          'nofollow' => false,
        ],
        'label_block' => true,
      ]
    );

    $this->end_controls_section();
  }

  protected function get_rc_props(): array
  {
    return eai_floating_contact_get_rc_props($this->get_settings_for_display());
  }

  protected function render(): void
  {
    $props = $this->get_rc_props();
    $result = eai_rc_render_html('FloatingContact', $props);

    eai_render_template('templates/EAI-floating-contact.php', [
      'html' => is_wp_error($result) ? '' : $result['html'],
      'error' => is_wp_error($result) ? $result : null,
    ]);
  }
}
