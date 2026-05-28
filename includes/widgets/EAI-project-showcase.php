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

    $post_type_options = eai_get_public_post_type_options();

    $this->add_control(
      'post_types',
      [
        'label' => esc_html__('Post types', 'eai'),
        'type' => \Elementor\Controls_Manager::SELECT2,
        'options' => $post_type_options,
        'multiple' => true,
        'default' => ['post'],
      ]
    );

    $taxonomy_options = array_merge(
      ['' => esc_html__('— Chọn taxonomy —', 'eai')],
      eai_get_public_taxonomy_options()
    );

    $include_term_options = [];
    $public_taxonomies = get_taxonomies(['public' => true], 'objects');
    foreach ($public_taxonomies as $taxonomy_obj) {
      if (! $taxonomy_obj || empty($taxonomy_obj->name)) {
        continue;
      }
      $taxonomy_name = (string) $taxonomy_obj->name;
      $taxonomy_label = (string) ($taxonomy_obj->labels->singular_name ?? $taxonomy_name);

      $terms = get_terms([
        'taxonomy' => $taxonomy_name,
        'hide_empty' => false,
      ]);
      if (is_wp_error($terms) || empty($terms)) {
        continue;
      }

      foreach ($terms as $term) {
        if (! $term instanceof \WP_Term) {
          continue;
        }
        $value = $taxonomy_name . ':' . $term->slug;
        $label = $taxonomy_label . ' — ' . $term->name . ' (' . $term->slug . ')';
        $include_term_options[$value] = $label;
      }
    }

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

    $taxonomies->add_control(
      'include_terms',
      [
        'label' => esc_html__('Chỉ hiển thị terms', 'eai'),
        'type' => \Elementor\Controls_Manager::SELECT2,
        'options' => $include_term_options,
        'multiple' => true,
        'default' => [],
        'description' => esc_html__('Để trống = hiển thị tất cả terms của taxonomy đã chọn.', 'eai'),
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
  }

  /**
   * @return array<string, mixed>
   */
  protected function get_rc_props(): array
  {
    $settings = $this->get_settings_for_display();
    $config = eai_project_showcase_config_from_settings($settings);
    $filters = eai_project_showcase_filters_from_url($config);

    $taxonomies = is_array($config['taxonomies'] ?? null) ? $config['taxonomies'] : [];
    $taxonomies_for_rc = array_values(array_filter(array_map(
      static function ($row) {
        if (! is_array($row)) {
          return null;
        }
        $key = sanitize_key((string) ($row['key'] ?? ''));
        if ($key === '') {
          return null;
        }
        return [
          'key' => $key,
          'label' => sanitize_text_field((string) ($row['label'] ?? $key)),
        ];
      },
      $taxonomies
    )));

    return [
      'filterEndpoint' => eai_project_showcase_filter_endpoint($config),
      'taxonomies' => $taxonomies_for_rc,
      'filters' => $filters,
      'filterOptions' => eai_project_showcase_get_filter_options($config),
      'projects' => eai_project_showcase_query_and_map($config, $filters),
    ];
  }

  protected function render(): void
  {
    $settings = $this->get_settings_for_display();
    $config = eai_project_showcase_config_from_settings($settings);

    if (empty($config['post_types'])) {
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
