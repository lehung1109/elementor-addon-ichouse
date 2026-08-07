<?php

class EAI_Vision_Mission_Widget extends \Elementor\Widget_Base
{
  public function get_name(): string
  {
    return 'eai_vision_mission_widget';
  }

  public function get_title(): string
  {
    return esc_html__('ICHouse — Vision Mission', 'eai');
  }

  public function get_icon(): string
  {
    return 'eicon-text-area';
  }

  public function get_categories(): array
  {
    return eai_get_widget_categories();
  }

  public function get_keywords(): array
  {
    return ['vision', 'mission', 'tam nhin', 'su menh', 'eai', 'ichouse'];
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

    $columns_repeater = new \Elementor\Repeater();

    $columns_repeater->add_control(
      'title',
      [
        'label' => esc_html__('Column title', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => 'TẦM NHÌN',
        'label_block' => true,
      ]
    );

    $columns_repeater->add_control(
      'items',
      [
        'label' => esc_html__('Items', 'eai'),
        'type' => \Elementor\Controls_Manager::REPEATER,
        'fields' => $items_repeater->get_controls(),
        'default' => [],
        'title_field' => '{{{ title }}}',
      ]
    );

    $this->add_control(
      'columns',
      [
        'label' => esc_html__('Columns', 'eai'),
        'type' => \Elementor\Controls_Manager::REPEATER,
        'fields' => $columns_repeater->get_controls(),
        'default' => [
          [
            'title' => 'TẦM NHÌN',
            'items' => [
              [
                'title' => '2027 - Doanh nghiệp xây dựng dân dụng có vị thế tại Việt Nam',
                'description' => 'Trở thành doanh nghiệp xây dựng dân dụng có vị thế tại Việt Nam, kiến tạo những công trình nhà ở kiểu mẫu, góp phần thay đổi diện mạo đô thị và nâng cao chất lượng sống của khách hàng.',
              ],
              [
                'title' => '2030 - Doanh nghiệp xây dựng dân dụng kiểu mẫu tại Việt Nam',
                'description' => 'Trở thành hình mẫu trong lĩnh vực xây dựng nhà ở dân dụng cao cấp tại Việt Nam - nơi hội tụ đội ngũ chuyên nghiệp, quy trình chuẩn hóa và công trình đạt chuẩn chất lượng cao, góp phần phát triển bền vững ngành xây dựng.',
              ],
            ],
          ],
          [
            'title' => 'SỨ MỆNH',
            'items' => [
              [
                'title' => 'Tạo nên những công trình giàu sức sáng tạo',
                'description' => 'ICHOUSE tạo nên những công trình giàu sức sáng tạo mang phong cách kiến trúc đặc sắc, áp dụng các giải pháp kết cấu và công nghệ xây dựng Châu Âu, đảm bảo tính bền vững với thời gian.',
              ],
              [
                'title' => 'Góp phần phát triển xã hội bền vững',
                'description' => 'Ngoài ra, ICHOUSE còn góp phần phát triển xã hội trên cơ sở là tác nhân quan trọng trong xu thế phát triển bền vững ở lĩnh vực này.',
              ],
            ],
          ],
        ],
        'title_field' => '{{{ title }}}',
      ]
    );

    $this->end_controls_section();
  }

  protected function get_rc_props(): array
  {
    return eai_vision_mission_get_rc_props($this->get_settings_for_display(), $this->get_id());
  }

  protected function render(): void
  {
    $settings = $this->get_settings_for_display();
    $props = eai_vision_mission_get_rc_props($settings, $this->get_id());

    if (empty($props['columns']) && eai_is_elementor_edit_mode()) {
      $props = eai_vision_mission_get_editor_sample_props($settings, $this->get_id());
      $result = eai_rc_render_html('VisionMission', $props);

      eai_render_template('templates/EAI-vision-mission.php', [
        'html' => is_wp_error($result) ? '' : $result['html'],
        'error' => is_wp_error($result) ? $result : null,
      ]);
      return;
    }

    if (empty($props['columns'])) {
      eai_render_template('templates/EAI-vision-mission.php', [
        'html' => '',
        'error' => null,
      ]);
      return;
    }

    $result = eai_rc_render_html('VisionMission', $props);

    eai_render_template('templates/EAI-vision-mission.php', [
      'html' => is_wp_error($result) ? '' : $result['html'],
      'error' => is_wp_error($result) ? $result : null,
    ]);
  }
}
