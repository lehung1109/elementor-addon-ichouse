<?php
class EAI_Construction_Header_Widget extends \Elementor\Widget_Base
{

  public function get_name(): string
  {
    return 'eai_construction_header_widget';
  }

  public function get_title(): string
  {
    return esc_html__('ICHouse — Construction Header', 'eai');
  }

  public function get_icon(): string
  {
    return 'eicon-header';
  }

  public function get_categories(): array
  {
    return eai_get_widget_categories();
  }

  public function get_keywords(): array
  {
    return ['header', 'construction', 'eai', 'ichouse'];
  }

  private function get_default_logo_url(): string
  {
    return 'https://noithat.ichouse.vn/wp-content/uploads/2024/02/logo.jpg';
  }

  protected function register_controls()
  {
    $default_logo_url = $this->get_default_logo_url();

    // Top
    $this->start_controls_section(
      'section_top',
      [
        'label' => esc_html__('Top', 'eai'),
        'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_control(
      'hotline_text',
      [
        'label' => esc_html__('Hotline text', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => 'Hotline: 0000 000 000',
        'label_block' => true,
      ]
    );

    $this->end_controls_section();

    // Logo
    $this->start_controls_section(
      'section_logo',
      [
        'label' => esc_html__('Logo', 'eai'),
        'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_control(
      'logo',
      [
        'label' => esc_html__('Logo', 'eai'),
        'type' => \Elementor\Controls_Manager::MEDIA,
        'default' => [
          'url' => $default_logo_url,
        ],
        'label_block' => true,
      ]
    );

    $this->add_control(
      'logo_dimensions',
      [
        'label' => esc_html__('Logo Dimensions', 'eai'),
        'type' => \Elementor\Controls_Manager::IMAGE_DIMENSIONS,
        'default' => [
          'width' => '150',
          'height' => '150',
        ],
        'label_block' => true,
      ]
    );

    $this->add_control(
      'logo_link',
      [
        'label' => esc_html__('Logo Link', 'eai'),
        'type' => \Elementor\Controls_Manager::URL,
        'options' => ['url', 'is_external', 'nofollow'],
        'default' => [
          'url' => '/',
          'is_external' => false,
          'nofollow' => false,
        ],
        'label_block' => true,
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

    $this->add_control(
      'nav_aria_label',
      [
        'label' => esc_html__('Nav aria label', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => 'Điều hướng chính',
        'label_block' => true,
      ]
    );

    $this->add_control(
      'open_submenu_label_prefix',
      [
        'label' => esc_html__('Open submenu label prefix', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => 'Mở menu',
        'label_block' => true,
      ]
    );

    $this->end_controls_section();

    // Social
    $this->start_controls_section(
      'section_social',
      [
        'label' => esc_html__('Social', 'eai'),
        'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
      ]
    );

    $social_repeater = new \Elementor\Repeater();

    $social_repeater->add_control(
      'icon',
      [
        'label' => esc_html__('Icon', 'eai'),
        'type' => \Elementor\Controls_Manager::MEDIA,
        'label_block' => true,
      ]
    );

    $social_repeater->add_control(
      'icon_resolution',
      [
        'label' => esc_html__('Image Resolution', 'eai'),
        'type' => \Elementor\Controls_Manager::SELECT,
        'default' => 'thumbnail',
        'options' => eai_get_image_size_options(),
      ]
    );

    $social_repeater->add_control(
      'icon_alt',
      [
        'label' => esc_html__('Alt text (optional)', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => '',
        'label_block' => true,
      ]
    );

    $social_repeater->add_control(
      'link',
      [
        'label' => esc_html__('Link', 'eai'),
        'type' => \Elementor\Controls_Manager::URL,
        'options' => ['url', 'is_external', 'nofollow'],
        'default' => [
          'url' => '',
          'is_external' => true,
          'nofollow' => false,
        ],
        'label_block' => true,
      ]
    );

    $this->add_control(
      'social_links',
      [
        'label' => esc_html__('Social links', 'eai'),
        'type' => \Elementor\Controls_Manager::REPEATER,
        'fields' => $social_repeater->get_controls(),
        'default' => [],
        'title_field' => '{{{ icon_alt }}}',
      ]
    );

    $this->end_controls_section();

    // Background
    $this->start_controls_section(
      'section_background',
      [
        'label' => esc_html__('Background', 'eai'),
        'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_control(
      'background_mobile',
      [
        'label' => esc_html__('Background (mobile / default)', 'eai'),
        'type' => \Elementor\Controls_Manager::MEDIA,
        'default' => [
          'url' => 'https://placehold.co/768x1024/152243/ffffff?text=Header+BG+Mobile',
        ],
        'label_block' => true,
      ]
    );

    $this->add_control(
      'background_mobile_resolution',
      [
        'label' => esc_html__('Mobile image resolution', 'eai'),
        'type' => \Elementor\Controls_Manager::SELECT,
        'default' => 'full',
        'options' => eai_get_image_size_options(),
      ]
    );

    $this->add_control(
      'background_desktop',
      [
        'label' => esc_html__('Background (desktop, optional)', 'eai'),
        'type' => \Elementor\Controls_Manager::MEDIA,
        'default' => [
          'url' => 'https://placehold.co/1920x1080/152243/ffffff?text=Header+BG+Desktop',
        ],
        'label_block' => true,
      ]
    );

    $this->add_control(
      'background_desktop_resolution',
      [
        'label' => esc_html__('Desktop image resolution', 'eai'),
        'type' => \Elementor\Controls_Manager::SELECT,
        'default' => 'full',
        'options' => eai_get_image_size_options(),
      ]
    );

    $this->add_control(
      'background_alt',
      [
        'label' => esc_html__('Background alt (optional)', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => '',
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
        'default' => 'Gõ tìm kiếm...',
        'label_block' => true,
      ]
    );

    $this->end_controls_section();

    // Scroll
    $this->start_controls_section(
      'section_scroll',
      [
        'label' => esc_html__('Scroll', 'eai'),
        'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_control(
      'sticky_after_px',
      [
        'label' => esc_html__('Sticky after (px)', 'eai'),
        'type' => \Elementor\Controls_Manager::NUMBER,
        'default' => 80,
        'min' => 0,
        'step' => 1,
      ]
    );

    $this->add_control(
      'disable_always_show_background_pages',
      [
        'label' => esc_html__('Disable always-show background on', 'eai'),
        'type' => \Elementor\Controls_Manager::SELECT2,
        'multiple' => true,
        'label_block' => true,
        'options' => function_exists('eai_get_page_options')
          ? eai_get_page_options()
          : [],
        'default' => [],
        'description' => esc_html__(
          'Background is always shown by default. Select pages where the header stays transparent until scroll.',
          'eai'
        ),
      ]
    );

    $this->end_controls_section();

    // Labels
    $this->start_controls_section(
      'section_labels',
      [
        'label' => esc_html__('Labels', 'eai'),
        'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_control(
      'open_menu_label',
      [
        'label' => esc_html__('Open menu label', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => 'Mở menu',
        'label_block' => true,
      ]
    );

    $this->add_control(
      'close_menu_label',
      [
        'label' => esc_html__('Close menu label', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => 'Đóng menu',
        'label_block' => true,
      ]
    );

    $this->add_control(
      'open_search_label',
      [
        'label' => esc_html__('Open search label', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => 'Mở tìm kiếm',
        'label_block' => true,
      ]
    );

    $this->add_control(
      'close_search_label',
      [
        'label' => esc_html__('Close search label', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => 'Đóng tìm kiếm',
        'label_block' => true,
      ]
    );

    $this->add_control(
      'search_menu_item_label',
      [
        'label' => esc_html__('Search menu item label', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => 'Tìm kiếm',
        'label_block' => true,
      ]
    );

    $this->end_controls_section();

    // Animation
    $this->start_controls_section(
      'section_animation',
      [
        'label' => esc_html__('Animation', 'eai'),
        'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_control(
      'menu_enable_fade_in',
      [
        'label' => esc_html__('Menu fade in', 'eai'),
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label_on' => esc_html__('Yes', 'eai'),
        'label_off' => esc_html__('No', 'eai'),
        'return_value' => 'yes',
        'default' => 'yes',
      ]
    );

    $this->add_control(
      'menu_enable_slide_in',
      [
        'label' => esc_html__('Menu slide in', 'eai'),
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label_on' => esc_html__('Yes', 'eai'),
        'label_off' => esc_html__('No', 'eai'),
        'return_value' => 'yes',
        'default' => 'yes',
      ]
    );

    $this->add_control(
      'search_enable_fade_in',
      [
        'label' => esc_html__('Search fade in', 'eai'),
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label_on' => esc_html__('Yes', 'eai'),
        'label_off' => esc_html__('No', 'eai'),
        'return_value' => 'yes',
        'default' => 'yes',
      ]
    );

    $this->add_control(
      'search_enable_slide_in',
      [
        'label' => esc_html__('Search slide in', 'eai'),
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label_on' => esc_html__('Yes', 'eai'),
        'label_off' => esc_html__('No', 'eai'),
        'return_value' => 'yes',
        'default' => 'yes',
      ]
    );

    $this->end_controls_section();

    // Advanced
    $this->start_controls_section(
      'section_advanced',
      [
        'label' => esc_html__('Advanced', 'eai'),
        'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_control(
      'class_name',
      [
        'label' => esc_html__('CSS class', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => '',
        'label_block' => true,
      ]
    );

    $this->end_controls_section();
  }

  /**
   * @return array<int|string, string>
   */
  private function get_wp_menus_options(): array
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
      eai_render_template('templates/EAI-construction-header.php', [
        'html' => '',
        'error' => null,
        'empty' => true,
      ]);
      return;
    }

    $props = eai_construction_header_get_rc_props($settings);
    $result = eai_rc_render_html('ConstructionHeader', $props);

    eai_render_template('templates/EAI-construction-header.php', [
      'html' => is_wp_error($result) ? '' : $result['html'],
      'error' => is_wp_error($result) ? $result : null,
      'empty' => false,
    ]);
  }
}
