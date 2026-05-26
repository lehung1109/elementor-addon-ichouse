<?php
class EAI_Header_Widget extends \Elementor\Widget_Base
{

  public function get_name(): string
  {
    return 'eai_header_widget';
  }

  public function get_title(): string
  {
    return esc_html__('EAI Header Widget', 'eai');
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

  protected function register_controls()
  {
    // Top
    $this->start_controls_section(
      'section_top',
      [
        'label' => esc_html__('Top', 'eai'),
        'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_control(
      'text',
      [
        'label' => esc_html__('Text', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => '',
        'label_block' => true,
      ]
    );

    $this->add_control(
      'phone',
      [
        'label' => esc_html__('Phone', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => '',
        'label_block' => true,
      ]
    );

    $this->add_control(
      'link_phone',
      [
        'label' => esc_html__('Link Phone', 'eai'),
        'type' => \Elementor\Controls_Manager::URL,
        'options' => ['url', 'is_external', 'nofollow'],
        'default' => [
          'url' => '',
          'is_external' => true,
          'nofollow' => true,
        ],
        'label_block' => true,
      ]
    );

    $this->end_controls_section();

    // Search
    $this->start_controls_section(
      'section_search',
      [
        'label' => esc_html__('Search', 'eai'),
        'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_control(
      'search_placeholder',
      [
        'label' => esc_html__('Search Placeholder', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => '',
        'label_block' => true,
      ]
    );

    $this->end_controls_section();

    // Inner
    $this->start_controls_section(
      'section_inner',
      [
        'label' => esc_html__('Inner', 'eai'),
        'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_control(
      'logo',
      [
        'label' => esc_html__('Logo', 'eai'),
        'type' => \Elementor\Controls_Manager::MEDIA,
        'label_block' => true,
      ]
    );

    $this->add_control(
      'logo_dimensions',
      [
        'label' => esc_html__('Logo Dimensions', 'eai'),
        'type' => \Elementor\Controls_Manager::IMAGE_DIMENSIONS,
        'label_block' => true,
      ]
    );

    $this->add_control(
      'logo_link',
      [
        'label' => esc_html__('Logo Link', 'eai'),
        'type' => \Elementor\Controls_Manager::URL,
        'label_block' => true,
      ]
    );

    $repeater = new \Elementor\Repeater();

    $repeater->add_control(
      'icon',
      [
        'label' => esc_html__('Icon', 'eai'),
        'type' => \Elementor\Controls_Manager::MEDIA,
        'label_block' => true,
      ]
    );

    $repeater->add_control(
      'icon_dimensions',
      [
        'label' => esc_html__('Icon Dimensions', 'eai'),
        'type' => \Elementor\Controls_Manager::IMAGE_DIMENSIONS,
        'label_block' => true,
      ]
    );

    $repeater->add_control(
      'text',
      [
        'label' => esc_html__('Text', 'eai'),
        'type' => \Elementor\Controls_Manager::WYSIWYG,
        'label_block' => true,
      ]
    );

    $this->add_control(
      'info_list',
      [
        'label' => esc_html__('Information List', 'eai'),
        'type' => \Elementor\Controls_Manager::REPEATER,
        'title_field' => '{{{ text }}}',
        'fields' => $repeater->get_controls(),
      ]
    );

    $this->end_controls_section();

    // Menu
    $this->start_controls_section(
      'section_menu',
      [
        'label' => esc_html__('Menu', 'eai'),
        'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_control(
      'menu_id',
      [
        'label' => esc_html__('Choose Menu', 'custom-elementor-menu'),
        'type' => \Elementor\Controls_Manager::SELECT,
        'options' => $this->get_wp_menus_options(),
        'default' => '',
      ]
    );

    $this->end_controls_section();
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

  protected function render(): void
  {
    $settings = $this->get_settings_for_display();
    $menu_id = (int) ($settings['menu_id'] ?? 0);

    if ($menu_id <= 0) {
      eai_render_template('templates/EAI-header.php', [
        'html' => '',
        'error' => null,
        'empty' => true,
      ]);
      return;
    }

    $logo = is_array($settings['logo'] ?? null) ? $settings['logo'] : [];
    $logo_dimensions = is_array($settings['logo_dimensions'] ?? null)
      ? $settings['logo_dimensions']
      : [];
    $logo_link = is_array($settings['logo_link'] ?? null) ? $settings['logo_link'] : [];
    $logo_link_props = ! empty($logo_link['url']) ? $logo_link : null;

    $info_list = is_array($settings['info_list'] ?? null) ? $settings['info_list'] : [];
    $link = is_array($settings['link_phone'] ?? null) ? $settings['link_phone'] : [];

    $props = [
      'headerTop' => [
        'text' => (string) ($settings['text'] ?? ''),
        'phone' => (string) ($settings['phone'] ?? ''),
        'link_phone' => eai_rc_map_link($link),
      ],
      'headerInner' => [
        'logo' => eai_rc_map_media_model($logo, $logo_dimensions, $logo_link_props),
        'info_list' => eai_rc_map_header_inner_info_list($info_list),
      ],
      'headerMenu' => [
        'items' => eai_rc_map_header_menu_items(
          eai_get_menu_tree_with_active($menu_id)
        ),
      ],
      'autocomplete_search' => [
        'placeholder' => (string) ($settings['search_placeholder'] ?? ''),
        'api_url' => rest_url('wp/v2/posts'),
      ],
    ];

    $result = eai_rc_render_html('Header', $props);

    eai_render_template('templates/EAI-header.php', [
      'html' => is_wp_error($result) ? '' : $result['html'],
      'error' => is_wp_error($result) ? $result : null,
      'empty' => false,
    ]);
  }
}

