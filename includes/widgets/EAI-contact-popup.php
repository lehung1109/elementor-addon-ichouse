<?php

class EAI_Contact_Popup_Widget extends \Elementor\Widget_Base
{
  public function get_name(): string
  {
    return 'eai_contact_popup_widget';
  }

  public function get_title(): string
  {
    return esc_html__('ICHouse — Contact Popup', 'eai');
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
    return ['contact', 'popup', 'cf7', 'form', 'lien he', 'eai', 'ichouse'];
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
      'popup_key',
      [
        'label' => esc_html__('Popup key', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => '',
        'label_block' => true,
        'description' => esc_html__(
          'Slug unique (vd. tu-van). Contact CTA phải dùng cùng giá trị ở Popup target.',
          'eai'
        ),
      ]
    );

    $this->add_control(
      'cf7_form_id',
      [
        'label' => esc_html__('Contact Form 7', 'eai'),
        'type' => \Elementor\Controls_Manager::SELECT,
        'options' => eai_contact_popup_get_cf7_options(),
        'default' => '',
        'description' => esc_html__(
          'Có thể đặt nhiều popup trên trang. CTA mở đúng popup qua Popup key.',
          'eai'
        ),
      ]
    );

    $this->end_controls_section();
  }

  protected function get_rc_props(): array
  {
    return eai_contact_popup_get_rc_props($this->get_settings_for_display());
  }

  protected function render(): void
  {
    $settings = $this->get_settings_for_display();
    $props = $this->get_rc_props();
    $popup_key = (string) ($props['popupKey'] ?? '');
    $form_source_id = eai_contact_popup_cf7_source_id($popup_key);

    if ($popup_key === '') {
      eai_render_template('templates/EAI-contact-popup.php', [
        'html' => '',
        'error' => null,
        'empty' => true,
        'form_html' => '',
        'form_source_id' => '',
      ]);
      return;
    }

    $result = eai_rc_render_html('ContactPopupWrapper', $props);

    $form_id = (int) ($settings['cf7_form_id'] ?? 0);
    $form_html = eai_contact_popup_render_form_html($form_id);

    eai_render_template('templates/EAI-contact-popup.php', [
      'html' => is_wp_error($result) ? '' : $result['html'],
      'error' => is_wp_error($result) ? $result : null,
      'form_html' => $form_html,
      'form_source_id' => $form_source_id,
    ]);
  }
}
