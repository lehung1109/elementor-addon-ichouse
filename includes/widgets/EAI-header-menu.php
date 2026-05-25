<?php
class EAI_Header_Menu_Widget extends \Elementor\Widget_Base
{

  public function get_name(): string
  {
    return 'eai_header_menu_widget';
  }

  public function get_title(): string
  {
    return esc_html__('EAI Header Menu Widget', 'eai');
  }

  public function get_icon(): string
  {
    return 'eicon-header';
  }

  public function get_categories(): array
  {
    return ['basic'];
  }

  public function get_keywords(): array
  {
    return ['header', 'eai'];
  }

  private function get_wp_menus_options()
  {
    $menus = wp_get_nav_menus();
    $options = [];

    if (! empty($menus) && ! is_wp_error($menus)) {
      foreach ($menus as $menu) {
        $options[$menu->term_id] = $menu->name;
      }
    }

    return $options;
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
      'menu_id',
      [
        'label'   => esc_html__('Choose Menu', 'custom-elementor-menu'),
        'type'    => \Elementor\Controls_Manager::SELECT,
        'options' => $this->get_wp_menus_options(),
        'default' => '',
      ]
    );

    $this->end_controls_section();
  }

  protected function render(): void
  {
    $settings = $this->get_settings_for_display();
    $menu_id = (int) ($settings['menu_id'] ?? 0);

    if ($menu_id <= 0) {
      eai_render_template('templates/EAI-header-menu.php', [
        'html' => '',
        'error' => null,
        'empty' => true,
      ]);
      return;
    }

    $props = [
      'items' => eai_rc_map_header_menu_items(
        eai_get_menu_tree_with_active($menu_id)
      ),
    ];

    $result = eai_rc_render_html('HeaderMenu', $props);

    eai_render_template('templates/EAI-header-menu.php', [
      'html' => is_wp_error($result) ? '' : $result['html'],
      'error' => is_wp_error($result) ? $result : null,
      'empty' => false,
    ]);
  }
}
