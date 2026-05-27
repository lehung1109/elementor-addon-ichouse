<?php

class EAI_Project_Showcase_Widget extends \Elementor\Widget_Base
{
  public function get_name(): string
  {
    return 'eai_project_showcase_widget';
  }

  public function get_title(): string
  {
    return esc_html__('ICHouse — Dự án (filter)', 'eai');
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
    return ['project', 'showcase', 'du an', 'filter', 'eai'];
  }

  protected function register_controls()
  {
    $this->start_controls_section(
      'section_query',
      [
        'label' => esc_html__('Query', 'eai'),
        'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_control(
      'post_type',
      [
        'label' => esc_html__('Post type', 'eai'),
        'type' => \Elementor\Controls_Manager::SELECT,
        'options' => eai_get_public_post_type_options(),
        'default' => 'post',
      ]
    );

    $taxonomy_options = array_merge(
      ['' => esc_html__('— Chọn taxonomy —', 'eai')],
      eai_get_public_taxonomy_options()
    );

    $taxonomies = new \Elementor\Repeater();

    $taxonomies->add_control(
      'key',
      [
        'label' => esc_html__('Key', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => '',
        'description' => esc_html__('VD: area, beds, style, ... (dùng làm key cho filter).', 'eai'),
      ]
    );

    $taxonomies->add_control(
      'label',
      [
        'label' => esc_html__('Label', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => '',
      ]
    );

    $taxonomies->add_control(
      'taxonomy',
      [
        'label' => esc_html__('Taxonomy', 'eai'),
        'type' => \Elementor\Controls_Manager::SELECT,
        'options' => $taxonomy_options,
        'default' => '',
      ]
    );

    $this->add_control(
      'taxonomies',
      [
        'label' => esc_html__('Taxonomies (filter)', 'eai'),
        'type' => \Elementor\Controls_Manager::REPEATER,
        'fields' => $taxonomies->get_controls(),
        'title_field' => '{{{ key }}}',
        'default' => [
          ['key' => 'area', 'label' => 'Diện tích', 'taxonomy' => ''],
          ['key' => 'beds', 'label' => 'Số phòng ngủ', 'taxonomy' => ''],
          ['key' => 'style', 'label' => 'Phong cách', 'taxonomy' => ''],
        ],
      ]
    );

    $this->add_control(
      'posts_per_page',
      [
        'label' => esc_html__('Số bài tối đa', 'eai'),
        'type' => \Elementor\Controls_Manager::NUMBER,
        'default' => -1,
        'min' => -1,
        'description' => esc_html__('-1 = không giới hạn.', 'eai'),
      ]
    );

    $this->add_control(
      'image_size',
      [
        'label' => esc_html__('Kích thước ảnh', 'eai'),
        'type' => \Elementor\Controls_Manager::SELECT,
        'default' => 'large',
        'options' => eai_get_image_size_options(),
      ]
    );

    $this->end_controls_section();

    $this->start_controls_section(
      'section_defaults',
      [
        'label' => esc_html__('Bộ lọc mặc định', 'eai'),
        'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
      ]
    );

    $defaults = new \Elementor\Repeater();

    $defaults->add_control(
      'key',
      [
        'label' => esc_html__('Key', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => '',
      ]
    );

    $defaults->add_control(
      'term',
      [
        'label' => esc_html__('Term slug', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => '',
        'description' => esc_html__('Để trống = không chọn.', 'eai'),
      ]
    );

    $this->add_control(
      'default_filters',
      [
        'label' => esc_html__('Bộ lọc mặc định', 'eai'),
        'type' => \Elementor\Controls_Manager::REPEATER,
        'fields' => $defaults->get_controls(),
        'title_field' => '{{{ key }}}',
        'default' => [
          ['key' => 'area', 'term' => ''],
          ['key' => 'beds', 'term' => ''],
          ['key' => 'style', 'term' => ''],
        ],
      ]
    );

    $this->end_controls_section();
  }

  /**
   * @return array<string, mixed>
   */
  protected function get_rc_props(): array
  {
    $settings = $this->get_settings_for_display();
    $config = eai_project_showcase_config_from_settings($settings);
    $filters = eai_project_showcase_resolve_filters($settings, $config);

    return [
      'filterEndpoint' => eai_project_showcase_filter_endpoint($config),
      'taxonomies' => $config['taxonomies'] ?? [],
      'filters' => $filters,
      'filterOptions' => eai_project_showcase_get_filter_options($config),
      'projects' => eai_project_showcase_query_and_map($config, $filters),
    ];
  }

  protected function render(): void
  {
    $settings = $this->get_settings_for_display();
    $config = eai_project_showcase_config_from_settings($settings);

    if (empty($config['post_type'])) {
      eai_render_template('templates/EAI-project-showcase.php', [
        'html' => '',
        'error' => null,
        'empty' => true,
      ]);
      return;
    }

    $props = $this->get_rc_props();
    $result = eai_rc_render_html('ProjectShowcase', $props);

    eai_render_template('templates/EAI-project-showcase.php', [
      'html' => is_wp_error($result) ? '' : $result['html'],
      'error' => is_wp_error($result) ? $result : null,
      'empty' => empty($props['projects']),
    ]);
  }
}
