<?php

class EAI_Contact_Popup_Button_Widget extends \Elementor\Widget_Base
{
  public function get_name(): string
  {
    return 'eai_contact_popup_button_widget';
  }

  public function get_title(): string
  {
    return esc_html__('ICHouse — Nút mở popup liên hệ', 'eai');
  }

  public function get_icon(): string
  {
    return 'eicon-button';
  }

  public function get_categories(): array
  {
    return eai_get_widget_categories();
  }

  public function get_keywords(): array
  {
    return ['button', 'popup', 'contact', 'lien he', 'eai', 'ichouse'];
  }

  protected function register_controls()
  {
    $this->start_controls_section('section_content', [
      'label' => esc_html__('Content', 'eai'),
      'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
    ]);

    $this->add_control('button_label', [
      'label' => esc_html__('Button label', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => '',
      'label_block' => true,
    ]);

    $this->add_control('popup_target', [
      'label' => esc_html__('Popup target', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => '',
      'label_block' => true,
      'description' => esc_html__(
        'Phải khớp Popup key của widget Contact Popup (vd. tu-van). Để trống → không hiển thị nút.',
        'eai'
      ),
    ]);

    $this->add_control('button_variant', [
      'label' => esc_html__('Button variant', 'eai'),
      'type' => \Elementor\Controls_Manager::SELECT,
      'default' => 'white',
      'options' => [
        'white' => esc_html__('Trắng (dùng trên nền tối)', 'eai'),
        'navy' => esc_html__('Navy (dùng trên nền sáng)', 'eai'),
      ],
    ]);

    $this->add_control('class_name', [
      'label' => esc_html__('Class name', 'eai'),
      'type' => \Elementor\Controls_Manager::TEXT,
      'default' => '',
      'label_block' => true,
    ]);

    $this->end_controls_section();
  }

  protected function render(): void
  {
    $settings = $this->get_settings_for_display();
    $props = eai_contact_popup_button_get_rc_props($settings);

    if (eai_is_elementor_edit_mode() && eai_contact_popup_button_props_are_empty($props)) {
      $props = eai_contact_popup_button_get_editor_sample_props($settings);
      $this->render_props($props);
      return;
    }

    if (eai_contact_popup_button_props_are_empty($props)) {
      eai_render_template('templates/EAI-contact-popup-button.php', ['empty' => true]);
      return;
    }

    $this->render_props($props);
  }

  /** @param array<string, mixed> $props */
  private function render_props(array $props): void
  {
    $result = eai_rc_render_html('ContactPopupButton', $props);
    eai_render_template('templates/EAI-contact-popup-button.php', [
      'html' => is_wp_error($result) ? '' : $result['html'],
      'error' => is_wp_error($result) ? $result : null,
    ]);
  }
}
