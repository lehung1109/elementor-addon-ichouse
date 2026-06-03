<?php

class EAI_Customer_Testimonials_Widget extends \Elementor\Widget_Base
{
  public function get_name(): string
  {
    return 'eai_customer_testimonials_widget';
  }

  public function get_title(): string
  {
    return esc_html__('ICHouse — Cảm nhận khách hàng', 'eai');
  }

  public function get_icon(): string
  {
    return 'eicon-video-playlist';
  }

  public function get_categories(): array
  {
    return eai_get_widget_categories();
  }

  public function get_keywords(): array
  {
    return [
      'testimonial',
      'testimonials',
      'youtube',
      'video',
      'cam nhan',
      'khach hang',
      'eai',
      'ichouse',
    ];
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
        'label' => esc_html__('Thumbnail', 'eai'),
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
      'youtube_video',
      [
        'label' => esc_html__('YouTube ID or URL', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => '',
        'label_block' => true,
        'description' => esc_html__(
          'Video ID (11 ký tự) hoặc link YouTube (watch, youtu.be, embed).',
          'eai'
        ),
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
            'image' => [
              'url' => 'https://placehold.co/640x360/333/fff?text=Nha+pho+Ninh+Binh',
            ],
            'alt' => esc_html__('Nhà phố 450m² Ninh Bình', 'eai'),
            'youtube_video' => 'dQw4w9WgXcQ',
          ],
          [
            'image' => [
              'url' => 'https://placehold.co/640x360/444/fff?text=Riverside+150m2',
            ],
            'alt' => esc_html__('Riverside 150m² nội thất chung cư', 'eai'),
            'youtube_video' => 'dQw4w9WgXcQ',
          ],
          [
            'image' => [
              'url' => 'https://placehold.co/640x360/555/fff?text=Nha+pho+Bac+Giang',
            ],
            'alt' => esc_html__('Nhà phố Bắc Giang hoàn thiện', 'eai'),
            'youtube_video' => 'dQw4w9WgXcQ',
          ],
        ],
        'title_field' => '{{{ alt }}}',
      ]
    );

    $this->end_controls_section();
  }

  protected function get_rc_props(): array
  {
    return eai_customer_testimonials_get_rc_props($this->get_settings_for_display());
  }

  protected function render(): void
  {
    $props = $this->get_rc_props();

    if (empty($props['items'])) {
      eai_render_template('templates/EAI-customer-testimonials.php', [
        'empty' => true,
      ]);
      return;
    }

    $result = eai_rc_render_html('CustomerTestimonialsWrapper', $props);

    eai_render_template('templates/EAI-customer-testimonials.php', [
      'html' => is_wp_error($result) ? '' : $result['html'],
      'error' => is_wp_error($result) ? $result : null,
    ]);
  }
}
