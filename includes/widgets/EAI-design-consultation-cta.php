<?php

class EAI_Design_Consultation_Cta_Widget extends \Elementor\Widget_Base
{

  public function get_name(): string
  {
    return 'eai_design_consultation_cta_widget';
  }

  public function get_title(): string
  {
    return esc_html__('ICHouse — CTA tư vấn thiết kế', 'eai');
  }

  public function get_icon(): string
  {
    return 'eicon-call-to-action';
  }

  public function get_categories(): array
  {
    return eai_get_widget_categories();
  }

  public function get_keywords(): array
  {
    return ['cta', 'consultation', 'design', 'tu van', 'lien he', 'eai', 'ichouse'];
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
      'background_image',
      [
        'label' => esc_html__('Background Image', 'eai'),
        'type' => \Elementor\Controls_Manager::MEDIA,
        'default' => [
          'url' => 'https://placehold.co/1920x400/022B63/ffffff?text=Interior',
        ],
      ]
    );

    $this->add_control(
      'background_image_resolution',
      [
        'label' => esc_html__('Background Image Resolution', 'eai'),
        'type' => \Elementor\Controls_Manager::SELECT,
        'default' => 'large',
        'options' => eai_get_image_size_options(),
      ]
    );

    $this->add_control(
      'heading',
      [
        'label' => esc_html__('Heading', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => 'ĐĂNG KÝ TƯ VẤN THIẾT KẾ NỘI THẤT',
        'label_block' => true,
      ]
    );

    $this->add_control(
      'subheading',
      [
        'label' => esc_html__('Subheading', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXTAREA,
        'default' => 'Chúng tôi sẽ giúp bạn tạo ra không gian hoàn hảo cho tổ ấm của bạn!',
        'rows' => 3,
      ]
    );

    $this->end_controls_section();

    $this->start_controls_section(
      'section_cta',
      [
        'label' => esc_html__('Call to action', 'eai'),
        'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_control(
      'cta_label',
      [
        'label' => esc_html__('Button label', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => 'LIÊN HỆ NGAY',
        'label_block' => true,
      ]
    );

    $this->add_control(
      'cta_link',
      [
        'label' => esc_html__('Button link', 'eai'),
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

    $this->end_controls_section();
  }

  protected function get_rc_props(): array
  {
    $settings = $this->get_settings_for_display();
    $background_image = is_array($settings['background_image'] ?? null)
      ? $settings['background_image']
      : [];
    $resolution = (string) ($settings['background_image_resolution'] ?? 'large');
    $cta_link = is_array($settings['cta_link'] ?? null) ? $settings['cta_link'] : [];

    return [
      'backgroundImage' => eai_rc_map_media_model($background_image, [], null, $resolution),
      'heading' => (string) ($settings['heading'] ?? ''),
      'subheading' => (string) ($settings['subheading'] ?? ''),
      'ctaLabel' => (string) ($settings['cta_label'] ?? ''),
      'cta' => eai_rc_map_link($cta_link),
    ];
  }

  protected function render(): void
  {
    $props = $this->get_rc_props();
    $result = eai_rc_render_html('DesignConsultationCta', $props);

    eai_render_template('templates/EAI-design-consultation-cta.php', [
      'html' => is_wp_error($result) ? '' : $result['html'],
      'error' => is_wp_error($result) ? $result : null,
    ]);
  }
}
