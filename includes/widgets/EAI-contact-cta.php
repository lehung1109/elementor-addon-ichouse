<?php

class EAI_Contact_Cta_Widget extends \Elementor\Widget_Base
{
  public function get_name(): string
  {
    return 'eai_contact_cta_widget';
  }

  public function get_title(): string
  {
    return esc_html__('ICHouse — Contact CTA', 'eai');
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
    return ['contact', 'cta', 'lien he', 'tu van', 'popup', 'eai', 'ichouse'];
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
      'subtitle',
      [
        'label' => esc_html__('Subtitle', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => 'LIÊN HỆ NGAY VỚI CHÚNG TÔI',
        'label_block' => true,
      ]
    );

    $this->add_control(
      'title',
      [
        'label' => esc_html__('Title', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => 'Liên hệ với ICHouse để nhận tư vấn miễn phí',
        'label_block' => true,
      ]
    );

    $this->add_control(
      'button_label',
      [
        'label' => esc_html__('Button label', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => 'ĐẶT LỊCH NGAY',
        'label_block' => true,
      ]
    );

    $this->add_control(
      'popup_target',
      [
        'label' => esc_html__('Popup target', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => '',
        'label_block' => true,
        'description' => esc_html__(
          'Phải khớp Popup key của widget Contact Popup (vd. tu-van).',
          'eai'
        ),
      ]
    );

    $this->add_control(
      'image',
      [
        'label' => esc_html__('Image (right)', 'eai'),
        'type' => \Elementor\Controls_Manager::MEDIA,
        'default' => [
          'url' => 'https://placehold.co/960x640/png?text=Contact+CTA',
        ],
      ]
    );

    $this->add_control(
      'image_resolution',
      [
        'label' => esc_html__('Image Resolution', 'eai'),
        'type' => \Elementor\Controls_Manager::SELECT,
        'default' => 'large',
        'options' => eai_get_image_size_options(),
      ]
    );

    $this->add_control(
      'content_background_image',
      [
        'label' => esc_html__('Content background image (optional)', 'eai'),
        'type' => \Elementor\Controls_Manager::MEDIA,
        'description' => esc_html__('Nền khối content trái. Để trống → nền primary (navy).', 'eai'),
        'default' => [
          'url' => '',
        ],
      ]
    );

    $this->add_control(
      'content_background_image_resolution',
      [
        'label' => esc_html__('Content background resolution', 'eai'),
        'type' => \Elementor\Controls_Manager::SELECT,
        'default' => 'large',
        'options' => eai_get_image_size_options(),
      ]
    );

    $this->end_controls_section();
  }

  protected function get_rc_props(): array
  {
    return eai_contact_cta_get_rc_props($this->get_settings_for_display());
  }

  protected function render(): void
  {
    $props = $this->get_rc_props();
    $result = eai_rc_render_html('ContactCta', $props);

    eai_render_template('templates/EAI-contact-cta.php', [
      'html' => is_wp_error($result) ? '' : $result['html'],
      'error' => is_wp_error($result) ? $result : null,
    ]);
  }
}
