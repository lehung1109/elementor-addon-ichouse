<?php

class EAI_Director_Intro_Widget extends \Elementor\Widget_Base
{

  public function get_name(): string
  {
    return 'eai_director_intro_widget';
  }

  public function get_title(): string
  {
    return esc_html__('ICHouse — Director Intro', 'eai');
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
    return ['director', 'intro', 'giam doc', 'lanh dao', 'eai', 'ichouse'];
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
      'image',
      [
        'label' => esc_html__('Image', 'eai'),
        'type' => \Elementor\Controls_Manager::MEDIA,
        'default' => [
          'url' => 'https://placehold.co/640x800/png?text=Director',
        ],
      ]
    );

    $this->add_control(
      'image_resolution',
      [
        'label' => esc_html__('Image Resolution', 'eai'),
        'type' => \Elementor\Controls_Manager::SELECT,
        'default' => 'large',
        'options' => eai_get_image_size_options(),
      ]
    );

    $this->add_control(
      'subtitle',
      [
        'label' => esc_html__('Subtitle', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => 'GIÁM ĐỐC – TS. NGUYỄN ĐĂNG HẠNH',
        'label_block' => true,
      ]
    );

    $this->add_control(
      'description_html',
      [
        'label' => esc_html__('Description', 'eai'),
        'type' => \Elementor\Controls_Manager::WYSIWYG,
        'default' => '<p>ICHOUSE ra đời với mong muốn thay đổi cách thức xây dựng và quy chuẩn công trình chất lượng của người Việt. Tôn chỉ làm việc của ICHOUSE là “tri thức trong xây dựng”.</p>',
      ]
    );

    $this->add_control(
      'scroll_reveal_target_id',
      [
        'label' => esc_html__('Scroll reveal target ID', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => 'director-intro',
        'description' => esc_html__('DOM id trên section (IntersectionObserver).', 'eai'),
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
          'url' => '/gioi-thieu',
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
    return eai_director_intro_get_rc_props($this->get_settings_for_display());
  }

  protected function render(): void
  {
    $props = $this->get_rc_props();
    $result = eai_rc_render_html('DirectorIntro', $props);

    eai_render_template('templates/EAI-director-intro.php', [
      'html' => is_wp_error($result) ? '' : $result['html'],
      'error' => is_wp_error($result) ? $result : null,
    ]);
  }
}
