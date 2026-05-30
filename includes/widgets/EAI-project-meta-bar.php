<?php

class EAI_Project_Meta_Bar_Widget extends \Elementor\Widget_Base
{

  public function get_name(): string
  {
    return 'eai_project_meta_bar_widget';
  }

  public function get_title(): string
  {
    return esc_html__('ICHouse — Project Meta Bar', 'eai');
  }

  public function get_icon(): string
  {
    return 'eicon-info-box';
  }

  public function get_categories(): array
  {
    return eai_get_widget_categories();
  }

  public function get_keywords(): array
  {
    return ['project', 'meta', 'taxonomy', 'term', 'eai', 'ichouse'];
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

    $taxonomy_options = eai_get_taxonomy_options_for_post_type(
      eai_project_meta_bar_get_post_type_for_controls()
    );

    $icon_options = eai_get_project_meta_bar_icon_options();
    $first_taxonomy = array_key_first($taxonomy_options) ?: '';

    $columns_repeater = new \Elementor\Repeater();

    $columns_repeater->add_control(
      'taxonomy',
      [
        'label' => esc_html__('Taxonomy', 'eai'),
        'type' => \Elementor\Controls_Manager::SELECT,
        'options' => $taxonomy_options,
        'default' => $first_taxonomy,
        'label_block' => true,
        'description' => esc_html__(
          'Chỉ taxonomy thuộc post type của bài đang xem (kiểm tra lúc render).',
          'eai'
        ),
      ]
    );

    $columns_repeater->add_control(
      'icon',
      [
        'label' => esc_html__('Icon', 'eai'),
        'type' => \Elementor\Controls_Manager::SELECT,
        'options' => $icon_options,
        'default' => 'user-round',
      ]
    );

    $default_columns = [];
    $default_icons = ['user-round', 'bed-double', 'palette', 'ruler'];

    for ($i = 0; $i < 4; $i++) {
      $default_columns[] = [
        'taxonomy' => $first_taxonomy,
        'icon' => $default_icons[$i] ?? 'user-round',
      ];
    }

    $this->add_control(
      'columns',
      [
        'label' => esc_html__('Columns', 'eai'),
        'type' => \Elementor\Controls_Manager::REPEATER,
        'fields' => $columns_repeater->get_controls(),
        'default' => $default_columns,
        'title_field' => '{{{ taxonomy }}}',
        'max_items' => 4,
      ]
    );

    $this->add_control(
      'class_name',
      [
        'label' => esc_html__('CSS class (optional)', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => '',
        'label_block' => true,
      ]
    );

    $this->end_controls_section();
  }

  protected function get_current_post_id(): int
  {
    $post_id = (int) get_queried_object_id();
    if ($post_id <= 0) {
      $post_id = (int) get_the_ID();
    }

    return $post_id;
  }

  protected function get_rc_props(): array
  {
    $settings = $this->get_settings_for_display();
    $post_id = $this->get_current_post_id();

    return eai_project_meta_bar_get_rc_props($post_id, $settings);
  }

  protected function render(): void
  {
    $settings = $this->get_settings_for_display();
    $post_id = $this->get_current_post_id();
    $props = eai_project_meta_bar_get_rc_props($post_id, $settings);

    if (
      eai_is_elementor_edit_mode()
      && ($post_id <= 0 || empty($props['columns']))
    ) {
      $props = eai_project_meta_bar_get_editor_sample_props($settings);
      $result = eai_rc_render_html('ProjectMetaBar', $props);

      eai_render_template('templates/EAI-project-meta-bar.php', [
        'html' => is_wp_error($result) ? '' : $result['html'],
        'error' => is_wp_error($result) ? $result : null,
      ]);

      return;
    }

    if ($post_id <= 0 || empty($props['columns'])) {
      eai_render_template('templates/EAI-project-meta-bar.php', [
        'html' => '',
        'error' => null,
        'empty' => true,
      ]);
      return;
    }

    $result = eai_rc_render_html('ProjectMetaBar', $props);

    eai_render_template('templates/EAI-project-meta-bar.php', [
      'html' => is_wp_error($result) ? '' : $result['html'],
      'error' => is_wp_error($result) ? $result : null,
    ]);
  }
}
