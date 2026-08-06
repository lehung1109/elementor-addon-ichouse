<?php

class EAI_Featured_Projects_Widget extends \Elementor\Widget_Base
{
  public function get_name(): string
  {
    return 'eai_featured_projects_widget';
  }

  public function get_title(): string
  {
    return esc_html__('ICHouse — Featured Projects', 'eai');
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
    return ['featured', 'projects', 'du an', 'noi bat', 'gallery', 'eai', 'ichouse'];
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
        'default' => 'DỰ ÁN',
        'label_block' => true,
      ]
    );

    $this->add_control(
      'title',
      [
        'label' => esc_html__('Title', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => 'Dự án nổi bật tại ICHOUSE',
        'label_block' => true,
      ]
    );

    $repeater = new \Elementor\Repeater();

    $repeater->add_control(
      'image',
      [
        'label' => esc_html__('Image', 'eai'),
        'type' => \Elementor\Controls_Manager::MEDIA,
        'label_block' => true,
        'default' => [
          'url' => 'https://placehold.co/600x800/png?text=Project',
        ],
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
      'title',
      [
        'label' => esc_html__('Item title', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => esc_html__('Biệt thự Nghĩa Đô', 'eai'),
        'label_block' => true,
      ]
    );

    $repeater->add_control(
      'description',
      [
        'label' => esc_html__('Item description', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXTAREA,
        'default' => "Chủ đầu tư: Mr. TAI\nMô hình: Biệt thự - Villa",
        'rows' => 3,
      ]
    );

    $repeater->add_control(
      'link',
      [
        'label' => esc_html__('Link', 'eai'),
        'type' => \Elementor\Controls_Manager::URL,
        'options' => ['url', 'is_external', 'nofollow'],
        'default' => [
          'url' => '#',
          'is_external' => false,
          'nofollow' => false,
        ],
        'label_block' => true,
      ]
    );

    $this->add_control(
      'items',
      [
        'label' => esc_html__('Projects', 'eai'),
        'type' => \Elementor\Controls_Manager::REPEATER,
        'fields' => $repeater->get_controls(),
        'default' => [
          [
            'image' => ['url' => 'https://placehold.co/600x800/png?text=Villa+1'],
            'title' => 'Biệt thự Nghĩa Đô',
            'description' => "Chủ đầu tư: Mr. TAI\nMô hình: Biệt thự - Villa",
            'link' => [
              'url' => '/du-an/biet-thu-nghia-do',
              'is_external' => false,
              'nofollow' => false,
            ],
          ],
          [
            'image' => ['url' => 'https://placehold.co/600x800/png?text=THT+Tower'],
            'title' => 'THT TOWER',
            'description' => 'VĂN PHÒNG',
            'link' => [
              'url' => '/du-an/tht-tower',
              'is_external' => false,
              'nofollow' => false,
            ],
          ],
          [
            'image' => ['url' => 'https://placehold.co/600x800/png?text=Residence'],
            'title' => 'Nhà phố hiện đại',
            'description' => "Chủ đầu tư: Mr. Hùng\nMô hình: Nhà phố",
            'link' => [
              'url' => '/du-an/nha-pho-hien-dai',
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
      'section_cta',
      [
        'label' => esc_html__('Call to action', 'eai'),
        'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_control(
      'button_label',
      [
        'label' => esc_html__('Button label', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => 'XEM TẤT CẢ DỰ ÁN',
        'label_block' => true,
      ]
    );

    $this->add_control(
      'button_link',
      [
        'label' => esc_html__('Button link', 'eai'),
        'type' => \Elementor\Controls_Manager::URL,
        'options' => ['url', 'is_external', 'nofollow'],
        'default' => [
          'url' => '/du-an',
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
    return eai_featured_projects_get_rc_props($this->get_settings_for_display());
  }

  protected function render(): void
  {
    $props = $this->get_rc_props();
    $result = eai_rc_render_html('FeaturedProjects', $props);

    eai_render_template('templates/EAI-featured-projects.php', [
      'html' => is_wp_error($result) ? '' : $result['html'],
      'error' => is_wp_error($result) ? $result : null,
    ]);
  }
}
