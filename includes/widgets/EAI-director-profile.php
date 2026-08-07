<?php

class EAI_Director_Profile_Widget extends \Elementor\Widget_Base
{
  public function get_name(): string
  {
    return 'eai_director_profile_widget';
  }

  public function get_title(): string
  {
    return esc_html__('ICHouse — Director Profile', 'eai');
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
    return ['director', 'profile', 'giam doc', 'ho so', 'eai', 'ichouse'];
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
      'background_mobile_image',
      [
        'label' => esc_html__('Background image (mobile)', 'eai'),
        'type' => \Elementor\Controls_Manager::MEDIA,
        'default' => [
          'url' => 'https://placehold.co/768x1024/152243/ffffff?text=Director+BG+Mobile',
        ],
      ]
    );

    $this->add_control(
      'background_mobile_image_resolution',
      [
        'label' => esc_html__('Background mobile resolution', 'eai'),
        'type' => \Elementor\Controls_Manager::SELECT,
        'default' => 'large',
        'options' => eai_get_image_size_options(),
      ]
    );

    $this->add_control(
      'background_desktop_image',
      [
        'label' => esc_html__('Background image (desktop)', 'eai'),
        'type' => \Elementor\Controls_Manager::MEDIA,
        'default' => [
          'url' => 'https://placehold.co/1920x1080/152243/ffffff?text=Director+BG+Desktop',
        ],
      ]
    );

    $this->add_control(
      'background_desktop_image_resolution',
      [
        'label' => esc_html__('Background desktop resolution', 'eai'),
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
        'default' => 'GIÁM ĐỐC - TS. NGUYỄN ĐĂNG HẠNH',
        'label_block' => true,
      ]
    );

    $this->add_control(
      'description_html',
      [
        'label' => esc_html__('Description', 'eai'),
        'type' => \Elementor\Controls_Manager::WYSIWYG,
        'default' => '<p>ICHOUSE ra đời với mong muốn thay đổi cách thức <span class="text-brand-gold">xây dựng</span> và quy chuẩn về một công trình chất lượng của người Việt. Với sứ mệnh kiến tạo những không gian sống bền vững, chúng tôi mang đến giải pháp thiết kế và thi công đồng bộ, chuẩn mực Châu Âu.</p>',
      ]
    );

    $items_repeater = new \Elementor\Repeater();

    $items_repeater->add_control(
      'title',
      [
        'label' => esc_html__('Item title', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => '',
        'label_block' => true,
      ]
    );

    $items_repeater->add_control(
      'description',
      [
        'label' => esc_html__('Item description', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXTAREA,
        'default' => '',
        'rows' => 4,
      ]
    );

    $this->add_control(
      'items',
      [
        'label' => esc_html__('Items', 'eai'),
        'type' => \Elementor\Controls_Manager::REPEATER,
        'fields' => $items_repeater->get_controls(),
        'default' => [
          [
            'title' => 'Giảng viên',
            'description' => 'ngành Kỹ thuật xây dựng tại trường ĐH Xây dựng Caen (Pháp) từ năm 2008 đến năm 2014',
          ],
          [
            'title' => 'Hơn 15 năm',
            'description' => 'kinh nghiệm quản lý và điều hành các dự án xây dựng dân dụng cao cấp tại Việt Nam',
          ],
          [
            'title' => 'Tốt nghiệp Tiến sỹ',
            'description' => 'ngành Kỹ thuật xây dựng tại Đại học Caen (Pháp), chuyên sâu kết cấu và vật liệu',
          ],
          [
            'title' => 'Chủ tịch HĐQT',
            'description' => 'công ty ICHOUSE — định hướng chiến lược phát triển và chuẩn hóa quy trình thi công',
          ],
          [
            'title' => 'Tác giả',
            'description' => 'nhiều công trình nghiên cứu và bài báo khoa học về kỹ thuật xây dựng bền vững',
          ],
        ],
        'title_field' => '{{{ title }}}',
      ]
    );

    $this->end_controls_section();
  }

  protected function get_rc_props(): array
  {
    return eai_director_profile_get_rc_props($this->get_settings_for_display(), $this->get_id());
  }

  protected function render(): void
  {
    $settings = $this->get_settings_for_display();
    $props = eai_director_profile_get_rc_props($settings, $this->get_id());

    if (eai_is_elementor_edit_mode() && ! eai_director_profile_has_content($props)) {
      $props = eai_director_profile_get_editor_sample_props($settings, $this->get_id());
      $result = eai_rc_render_html('DirectorProfile', $props);

      eai_render_template('templates/EAI-director-profile.php', [
        'html' => is_wp_error($result) ? '' : $result['html'],
        'error' => is_wp_error($result) ? $result : null,
      ]);
      return;
    }

    if (! eai_director_profile_has_content($props)) {
      eai_render_template('templates/EAI-director-profile.php', [
        'empty' => true,
      ]);
      return;
    }

    $result = eai_rc_render_html('DirectorProfile', $props);

    eai_render_template('templates/EAI-director-profile.php', [
      'html' => is_wp_error($result) ? '' : $result['html'],
      'error' => is_wp_error($result) ? $result : null,
    ]);
  }
}
