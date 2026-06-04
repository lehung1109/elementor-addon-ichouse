<?php

class EAI_Image_Overlay_Cards_Grid_Widget extends \Elementor\Widget_Base
{
  public function get_name(): string
  {
    return 'eai_image_overlay_cards_grid_widget';
  }

  public function get_title(): string
  {
    return esc_html__('ICHouse — Lưới thẻ ảnh overlay', 'eai');
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
    return ['image', 'overlay', 'cards', 'grid', 'the anh', 'luoi', 'eai', 'ichouse'];
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
      'image',
      [
        'label' => esc_html__('Image', 'eai'),
        'type' => \Elementor\Controls_Manager::MEDIA,
        'label_block' => true,
      ]
    );

    $repeater->add_control(
      'image_resolution',
      [
        'label' => esc_html__('Image Resolution', 'eai'),
        'type' => \Elementor\Controls_Manager::SELECT,
        'default' => 'large',
        'options' => eai_get_image_size_options(),
      ]
    );

    $repeater->add_control(
      'alt',
      [
        'label' => esc_html__('Alt text (optional)', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => '',
        'label_block' => true,
        'description' => esc_html__('Overrides attachment alt when set.', 'eai'),
      ]
    );

    $repeater->add_control(
      'title',
      [
        'label' => esc_html__('Title', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => esc_html__('Thiết kế nội thất', 'eai'),
        'label_block' => true,
      ]
    );

    $repeater->add_control(
      'link',
      [
        'label' => esc_html__('Link (optional)', 'eai'),
        'type' => \Elementor\Controls_Manager::URL,
        'options' => ['url', 'is_external', 'nofollow'],
        'default' => [
          'url' => '',
          'is_external' => false,
          'nofollow' => false,
        ],
        'label_block' => true,
      ]
    );

    $this->add_control(
      'items',
      [
        'label' => esc_html__('Items', 'eai'),
        'type' => \Elementor\Controls_Manager::REPEATER,
        'fields' => $repeater->get_controls(),
        'default' => [
          [
            'image' => ['url' => 'https://placehold.co/400x400/png'],
            'alt' => 'Thiết kế nội thất nhà phố',
            'title' => 'Thiết kế nội thất nhà phố',
            'link' => [
              'url' => '#',
              'is_external' => false,
              'nofollow' => false,
            ],
          ],
          [
            'image' => ['url' => 'https://placehold.co/400x400/png?text=2'],
            'alt' => 'Thiết kế nội thất chung cư',
            'title' => 'Thiết kế nội thất chung cư',
            'link' => [
              'url' => '#',
              'is_external' => false,
              'nofollow' => false,
            ],
          ],
          [
            'image' => ['url' => 'https://placehold.co/400x400/png?text=3'],
            'alt' => 'Thiết kế nội thất biệt thự',
            'title' => 'Thiết kế nội thất biệt thự',
            'link' => [
              'url' => '',
              'is_external' => false,
              'nofollow' => false,
            ],
          ],
        ],
        'title_field' => '{{{ title }}}',
      ]
    );

    $this->end_controls_section();

    $this->start_controls_section(
      'section_layout',
      [
        'label' => esc_html__('Layout', 'eai'),
        'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_control(
      'gap',
      [
        'label' => esc_html__('Khoảng cách (px)', 'eai'),
        'type' => \Elementor\Controls_Manager::NUMBER,
        'min' => 0,
        'max' => 64,
        'step' => 1,
        'default' => 24,
      ]
    );

    $this->end_controls_section();
  }

  protected function render(): void
  {
    $settings = $this->get_settings_for_display();
    $props = eai_image_overlay_cards_grid_get_rc_props($settings);

    if (eai_is_elementor_edit_mode() && empty($props['items'])) {
      $props = eai_image_overlay_cards_grid_get_editor_sample_props($settings);
      $result = eai_rc_render_html('ImageOverlayCardsGrid', $props);

      eai_render_template('templates/EAI-image-overlay-cards-grid.php', [
        'html' => is_wp_error($result) ? '' : $result['html'],
        'error' => is_wp_error($result) ? $result : null,
      ]);
      return;
    }

    if (empty($props['items'])) {
      eai_render_template('templates/EAI-image-overlay-cards-grid.php', [
        'empty' => true,
      ]);
      return;
    }

    $result = eai_rc_render_html('ImageOverlayCardsGrid', $props);

    eai_render_template('templates/EAI-image-overlay-cards-grid.php', [
      'html' => is_wp_error($result) ? '' : $result['html'],
      'error' => is_wp_error($result) ? $result : null,
    ]);
  }
}
