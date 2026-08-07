<?php

class EAI_Outstanding_Advantages_Widget extends \Elementor\Widget_Base
{
  private const TEMPLATE = 'templates/EAI-outstanding-advantages.php';

  public function get_name(): string
  {
    return 'eai_outstanding_advantages_widget';
  }

  public function get_title(): string
  {
    return esc_html__('ICHouse — Ưu điểm vượt trội', 'eai');
  }

  public function get_icon(): string
  {
    return 'eicon-gallery-grid';
  }

  public function get_categories(): array
  {
    return eai_get_widget_categories();
  }

  public function get_keywords(): array
  {
    return ['outstanding', 'advantages', 'benefits', 'uu diem', 'loi the', 'eai', 'ichouse'];
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

    $this->add_media_controls(
      $repeater,
      'background_mobile_image',
      esc_html__('Mobile background', 'eai'),
      'large'
    );
    $this->add_media_controls(
      $repeater,
      'background_desktop_image',
      esc_html__('Desktop background', 'eai'),
      'large'
    );
    $this->add_media_controls(
      $this,
      'image',
      esc_html__('Top image', 'eai'),
      'medium'
    );

    $repeater->add_control(
      'subtitle',
      [
        'label' => esc_html__('Subtitle', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => 'Ưu điểm vượt trội',
        'label_block' => true,
      ]
    );

    $repeater->add_control(
      'title',
      [
        'label' => esc_html__('Title', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => '',
        'label_block' => true,
      ]
    );

    $repeater->add_control(
      'description',
      [
        'label' => esc_html__('Description', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXTAREA,
        'default' => '',
        'rows' => 5,
      ]
    );

    $this->add_control(
      'items',
      [
        'label' => esc_html__('Advantages', 'eai'),
        'type' => \Elementor\Controls_Manager::REPEATER,
        'fields' => $repeater->get_controls(),
        'default' => [],
        'title_field' => '{{{ title }}}',
      ]
    );

    $this->end_controls_section();
  }

  private function add_media_controls(
    object $control_container,
    string $field,
    string $label,
    string $default_resolution
  ): void {
    $control_container->add_control(
      $field,
      [
        'label' => $label,
        'type' => \Elementor\Controls_Manager::MEDIA,
        'label_block' => true,
      ]
    );

    $control_container->add_control(
      $field . '_resolution',
      [
        'label' => esc_html__('Image Resolution', 'eai'),
        'type' => \Elementor\Controls_Manager::SELECT,
        'default' => $default_resolution,
        'options' => eai_get_image_size_options(),
      ]
    );

    $alt_field = str_ends_with($field, '_image')
      ? substr($field, 0, -6) . '_alt'
      : $field . '_alt';

    $control_container->add_control(
      $alt_field,
      [
        'label' => esc_html__('Alt text (optional)', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => '',
        'label_block' => true,
        'description' => esc_html__('Overrides attachment alt when set.', 'eai'),
      ]
    );
  }

  protected function render(): void
  {
    $settings = $this->get_settings_for_display();
    $widget_id = $this->get_id();
    $props = eai_outstanding_advantages_get_rc_props($settings, $widget_id);

    if (eai_is_elementor_edit_mode() && empty($props['items'])) {
      $props = eai_outstanding_advantages_get_editor_sample_props($settings, $widget_id);
      $this->render_props($props);
      return;
    }

    if (empty($props['items'])) {
      eai_render_template(self::TEMPLATE, ['empty' => true]);
      return;
    }

    $this->render_props($props);
  }

  /** @param array<string, mixed> $props */
  private function render_props(array $props): void
  {
    $result = eai_rc_render_html('OutstandingAdvantages', $props);

    eai_render_template(self::TEMPLATE, [
      'html' => is_wp_error($result) ? '' : $result['html'],
      'error' => is_wp_error($result) ? $result : null,
    ]);
  }
}
