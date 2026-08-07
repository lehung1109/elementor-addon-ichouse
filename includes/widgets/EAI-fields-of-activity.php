<?php

class EAI_Fields_Of_Activity_Widget extends \Elementor\Widget_Base
{
  public function get_name(): string
  {
    return 'eai_fields_of_activity_widget';
  }

  public function get_title(): string
  {
    return esc_html__('ICHouse — Lĩnh vực hoạt động', 'eai');
  }

  public function get_icon(): string
  {
    return 'eicon-accordion';
  }

  public function get_categories(): array
  {
    return eai_get_widget_categories();
  }

  public function get_keywords(): array
  {
    return ['linh vuc', 'hoat dong', 'accordion', 'fields', 'activity', 'eai', 'ichouse'];
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
      'title',
      [
        'label' => esc_html__('Title', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => 'Lĩnh vực hoạt động',
        'label_block' => true,
      ]
    );

    $this->add_control(
      'icon_image',
      [
        'label' => esc_html__('Icon image (when open)', 'eai'),
        'type' => \Elementor\Controls_Manager::MEDIA,
        'default' => [
          'url' => 'https://placehold.co/80x80/D9A441/022B63/png?text=Icon',
        ],
        'label_block' => true,
      ]
    );

    $this->add_control(
      'icon_image_resolution',
      [
        'label' => esc_html__('Icon resolution', 'eai'),
        'type' => \Elementor\Controls_Manager::SELECT,
        'default' => 'thumbnail',
        'options' => eai_get_image_size_options(),
      ]
    );

    $repeater = new \Elementor\Repeater();

    $repeater->add_control(
      'title',
      [
        'label' => esc_html__('Title', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => '',
        'label_block' => true,
      ]
    );

    $repeater->add_control(
      'content_html',
      [
        'label' => esc_html__('Content', 'eai'),
        'type' => \Elementor\Controls_Manager::WYSIWYG,
        'default' => '',
      ]
    );

    $repeater->add_control(
      'default_open',
      [
        'label' => esc_html__('Open by default', 'eai'),
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label_on' => esc_html__('Yes', 'eai'),
        'label_off' => esc_html__('No', 'eai'),
        'return_value' => 'yes',
        'default' => '',
      ]
    );

    $this->add_control(
      'items',
      [
        'label' => esc_html__('Accordion items', 'eai'),
        'type' => \Elementor\Controls_Manager::REPEATER,
        'fields' => $repeater->get_controls(),
        'default' => [
          [
            'title' => 'Thiết kế kiến trúc và nội thất công trình dân dụng',
            'content_html' => '<ul><li>Thiết kế kiến trúc công trình dân dụng</li><li>Thiết kế nội thất và hồ sơ kỹ thuật thi công</li></ul>',
            'default_open' => 'yes',
          ],
          [
            'title' => 'Thi công xây dựng công trình',
            'content_html' => '<ul><li>Thi công xây dựng phần thô và hoàn thiện</li><li>Giám sát và bàn giao công trình</li></ul>',
            'default_open' => '',
          ],
        ],
        'title_field' => '{{{ title }}}',
      ]
    );

    $this->end_controls_section();

    $this->start_controls_section(
      'section_images',
      [
        'label' => esc_html__('Images', 'eai'),
        'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_control(
      'image_1',
      [
        'label' => esc_html__('Image 1', 'eai'),
        'type' => \Elementor\Controls_Manager::MEDIA,
        'default' => [
          'url' => 'https://placehold.co/480x640/888888/ffffff?text=Blueprint',
        ],
      ]
    );

    $this->add_control(
      'image_1_resolution',
      [
        'label' => esc_html__('Image 1 resolution', 'eai'),
        'type' => \Elementor\Controls_Manager::SELECT,
        'default' => 'large',
        'options' => eai_get_image_size_options(),
      ]
    );

    $this->add_control(
      'image_2',
      [
        'label' => esc_html__('Image 2', 'eai'),
        'type' => \Elementor\Controls_Manager::MEDIA,
        'default' => [
          'url' => 'https://placehold.co/480x640/666666/ffffff?text=Construction',
        ],
      ]
    );

    $this->add_control(
      'image_2_resolution',
      [
        'label' => esc_html__('Image 2 resolution', 'eai'),
        'type' => \Elementor\Controls_Manager::SELECT,
        'default' => 'large',
        'options' => eai_get_image_size_options(),
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
        'default' => 'TÌM HIỂU THÊM',
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
          'url' => '/linh-vuc-hoat-dong',
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
    return eai_fields_of_activity_get_rc_props(
      $this->get_settings_for_display(),
      $this->get_id()
    );
  }

  protected function render(): void
  {
    $props = $this->get_rc_props();
    $title = trim((string) ($props['title'] ?? ''));
    $items = is_array($props['items'] ?? null) ? $props['items'] : [];
    $images = is_array($props['images'] ?? null) ? $props['images'] : [];
    $button_label = trim((string) ($props['buttonLabel'] ?? ''));
    $button_url = trim((string) (($props['buttonLink']['url'] ?? '') ?: ''));
    $has_button = $button_label !== '' && $button_url !== '';

    if ($title === '' && $items === [] && $images === [] && ! $has_button) {
      eai_render_template('templates/EAI-fields-of-activity.php', [
        'empty' => true,
      ]);
      return;
    }

    $result = eai_rc_render_html('FieldsOfActivity', $props);

    eai_render_template('templates/EAI-fields-of-activity.php', [
      'html' => is_wp_error($result) ? '' : $result['html'],
      'error' => is_wp_error($result) ? $result : null,
    ]);
  }
}
