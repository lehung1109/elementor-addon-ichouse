<?php

class EAI_Inline_List_Widget extends \Elementor\Widget_Base
{

  public function get_name(): string
  {
    return 'eai_inline_list_widget';
  }

  public function get_title(): string
  {
    return esc_html__('ICHouse — Inline List', 'eai');
  }

  public function get_icon(): string
  {
    return 'eicon-bullet-list';
  }

  public function get_categories(): array
  {
    return eai_get_widget_categories();
  }

  public function get_keywords(): array
  {
    return ['inline', 'list', 'taxonomy', 'term', 'eai', 'ichouse'];
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
      'taxonomy',
      [
        'label' => esc_html__('Taxonomy', 'eai'),
        'type' => \Elementor\Controls_Manager::SELECT,
        'default' => 'category',
        'options' => eai_get_public_taxonomy_options(),
        'label_block' => true,
        'description' => esc_html__(
          'Danh sách mọi term của taxonomy (kể cả term chưa có bài).',
          'eai'
        ),
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

  protected function render(): void
  {
    $settings = $this->get_settings_for_display();
    $props = eai_inline_list_get_rc_props($settings);

    if (eai_is_elementor_edit_mode() && empty($props['items'])) {
      $props = eai_inline_list_get_editor_sample_props($settings);
      $result = eai_rc_render_html('InlineList', $props);

      eai_render_template('templates/EAI-inline-list.php', [
        'html' => is_wp_error($result) ? '' : $result['html'],
        'error' => is_wp_error($result) ? $result : null,
      ]);

      return;
    }

    if (empty($props['items'])) {
      eai_render_template('templates/EAI-inline-list.php', [
        'html' => '',
        'error' => null,
        'empty' => true,
      ]);
      return;
    }

    $result = eai_rc_render_html('InlineList', $props);

    eai_render_template('templates/EAI-inline-list.php', [
      'html' => is_wp_error($result) ? '' : $result['html'],
      'error' => is_wp_error($result) ? $result : null,
    ]);
  }
}
