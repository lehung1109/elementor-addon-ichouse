<?php

class EAI_Key_Personnel_Widget extends \Elementor\Widget_Base
{
  public function get_name(): string
  {
    return 'eai_key_personnel_widget';
  }

  public function get_title(): string
  {
    return esc_html__('ICHouse — Key Personnel', 'eai');
  }

  public function get_icon(): string
  {
    return 'eicon-person';
  }

  public function get_categories(): array
  {
    return eai_get_widget_categories();
  }

  public function get_keywords(): array
  {
    return ['key', 'personnel', 'team', 'nhan su', 'doi ngu', 'swiper', 'eai', 'ichouse'];
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
        'default' => 'ĐỘI NGŨ NHÂN SỰ CHỦ CHỐT',
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
          'url' => 'https://placehold.co/480x600/png?text=Personnel',
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
        'label' => esc_html__('Name / title', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => esc_html__('KS. Lưu Hoàng Nga', 'eai'),
        'label_block' => true,
      ]
    );

    $repeater->add_control(
      'description_html',
      [
        'label' => esc_html__('Description', 'eai'),
        'type' => \Elementor\Controls_Manager::WYSIWYG,
        'default' => '<ul><li>Tốt nghiệp Đại học Xây dựng Hà Nội</li><li>Chứng chỉ hành nghề giám sát thi công</li></ul>',
      ]
    );

    $repeater->add_control(
      'link',
      [
        'label' => esc_html__('Link', 'eai'),
        'type' => \Elementor\Controls_Manager::URL,
        'options' => ['url', 'is_external', 'nofollow'],
        'default' => [
          'url' => '',
          'is_external' => false,
          'nofollow' => false,
        ],
        'label_block' => true,
      ]
    );

    $repeater->add_control(
      'link_label',
      [
        'label' => esc_html__('Link label', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => 'Xem chi tiết',
        'label_block' => true,
      ]
    );

    $this->add_control(
      'items',
      [
        'label' => esc_html__('Personnel', 'eai'),
        'type' => \Elementor\Controls_Manager::REPEATER,
        'fields' => $repeater->get_controls(),
        'default' => [],
        'title_field' => '{{{ title }}}',
      ]
    );

    $this->end_controls_section();
  }

  protected function get_rc_props(): array
  {
    return eai_key_personnel_get_rc_props($this->get_settings_for_display());
  }

  protected function render(): void
  {
    $settings = $this->get_settings_for_display();
    $props = eai_key_personnel_get_rc_props($settings);

    if (eai_is_elementor_edit_mode() && empty($props['items'])) {
      $props = eai_key_personnel_get_editor_sample_props($settings);
      $result = eai_rc_render_html('KeyPersonnelWrapper', $props);

      eai_render_template('templates/EAI-key-personnel.php', [
        'html' => is_wp_error($result) ? '' : $result['html'],
        'error' => is_wp_error($result) ? $result : null,
      ]);
      return;
    }

    if (empty($props['items'])) {
      eai_render_template('templates/EAI-key-personnel.php', [
        'empty' => true,
      ]);
      return;
    }

    $result = eai_rc_render_html('KeyPersonnelWrapper', $props);

    eai_render_template('templates/EAI-key-personnel.php', [
      'html' => is_wp_error($result) ? '' : $result['html'],
      'error' => is_wp_error($result) ? $result : null,
    ]);
  }
}
