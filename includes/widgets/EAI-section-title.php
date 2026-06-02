<?php

class EAI_Section_Title_Widget extends \Elementor\Widget_Base
{
  public function get_name(): string
  {
    return 'eai_section_title_widget';
  }

  public function get_title(): string
  {
    return esc_html__('ICHouse — Section Title', 'eai');
  }

  public function get_icon(): string
  {
    return 'eicon-t-letter';
  }

  public function get_categories(): array
  {
    return eai_get_widget_categories();
  }

  public function get_keywords(): array
  {
    return ['section', 'title', 'heading', 'eai', 'ichouse'];
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
      'title',
      [
        'label' => esc_html__('Title', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => esc_html__('DỊCH VỤ NỔI BẬT', 'eai'),
        'label_block' => true,
      ]
    );

    $this->add_control(
      'background_image',
      [
        'label' => esc_html__('Underline Image', 'eai'),
        'type' => \Elementor\Controls_Manager::MEDIA,
        'default' => [
          'url' => 'https://placehold.co/243x10/png',
        ],
        'label_block' => true,
      ]
    );

    $this->add_control(
      'background_image_dimensions',
      [
        'label' => esc_html__('Underline Image Dimensions', 'eai'),
        'type' => \Elementor\Controls_Manager::IMAGE_DIMENSIONS,
        'default' => [
          'width' => '243',
          'height' => '10',
        ],
        'label_block' => true,
      ]
    );

    $this->add_control(
      'background_image_resolution',
      [
        'label' => esc_html__('Underline Image Resolution', 'eai'),
        'type' => \Elementor\Controls_Manager::SELECT,
        'default' => 'full',
        'options' => \eai_get_image_size_options(),
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
    $background_image_dimensions = is_array($settings['background_image_dimensions'] ?? null)
      ? $settings['background_image_dimensions']
      : [];
    $background_image_resolution = (string) ($settings['background_image_resolution'] ?? 'full');

    $props = [
      'title' => (string) ($settings['title'] ?? ''),
      'backgroundImage' => eai_rc_map_media_model(
        $background_image,
        $background_image_dimensions,
        null,
        $background_image_resolution
      ),
    ];

    $result = eai_rc_render_html('SectionTitle', $props);

    eai_render_template('templates/EAI-section-title.php', [
      'html' => is_wp_error($result) ? '' : $result['html'],
      'error' => is_wp_error($result) ? $result : null,
    ]);
  }
}

