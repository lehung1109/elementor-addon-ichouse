<?php

class EAI_Process_Section_Widget extends \Elementor\Widget_Base
{

  public function get_name(): string
  {
    return 'eai_process_section_widget';
  }

  public function get_title(): string
  {
    return esc_html__('EAI Process Section Widget', 'eai');
  }

  public function get_icon(): string
  {
    return 'eicon-flow';
  }

  public function get_categories(): array
  {
    return ['basic'];
  }

  public function get_keywords(): array
  {
    return ['process', 'steps', 'quy trinh', 'eai'];
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
      'background_image',
      [
        'label' => esc_html__('Background Image', 'eai'),
        'type' => \Elementor\Controls_Manager::MEDIA,
        'default' => [
          'url' => 'https://placehold.co/2600x800/png',
        ],
      ]
    );

    $this->add_control(
      'background_image_resolution',
      [
        'label' => esc_html__('Background Image Resolution', 'eai'),
        'type' => \Elementor\Controls_Manager::SELECT,
        'default' => 'large',
        'options' => \eai_get_image_size_options(),
      ]
    );

    $this->add_control(
      'intro_content',
      [
        'label' => esc_html__('Intro Content', 'eai'),
        'type' => \Elementor\Controls_Manager::WYSIWYG,
        'default' => '<h2 class="uppercase text-[1.6rem] font-bold">QUY TRÌNH THI CÔNG CỦA HOÀN MỸ <span class="text-orange-500">DECOR</span></h2><p>Để Quý khách hàng không mất quá nhiều thời gian trong việc lựa chọn đơn vị Tư vấn – Thiết kế nội thất uy tín, Hoàn Mỹ Decor giới thiệu tới Quý khách hàng Quy trình Tư vấn – Thiết kế nội thất chuyên nghiệp, trọn gói.</p>',
        'label_block' => true,
      ]
    );

    $repeater = new \Elementor\Repeater();

    $repeater->add_control(
      'title',
      [
        'label' => esc_html__('Title', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => esc_html__('Step', 'eai'),
        'label_block' => true,
      ]
    );

    $repeater->add_control(
      'description',
      [
        'label' => esc_html__('Description', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXTAREA,
        'default' => '',
        'rows' => 3,
      ]
    );

    $repeater->add_control(
      'icon',
      [
        'label' => esc_html__('Icon', 'eai'),
        'type' => \Elementor\Controls_Manager::SELECT,
        'default' => 'user-round',
        'options' => eai_get_process_section_icon_options(),
      ]
    );

    $this->add_control(
      'steps',
      [
        'label' => esc_html__('Steps', 'eai'),
        'type' => \Elementor\Controls_Manager::REPEATER,
        'fields' => $repeater->get_controls(),
        'default' => [
          [
            'title' => 'Tư vấn',
            'description' => 'Liên hệ tư vấn và ước lượng quy mô sản phẩm',
            'icon' => 'user-round',
          ],
          [
            'title' => 'Thiết kế',
            'description' => 'Phác họa và tính toán chi tiết cho dự án sắp thực hiện',
            'icon' => 'pencil-ruler',
          ],
          [
            'title' => 'Thi công lắp đặt',
            'description' => 'Đội ngũ thi công giàu kinh nghiệm, chuyên nghiệp',
            'icon' => 'cog',
          ],
          [
            'title' => 'Thanh toán bảo trì',
            'description' => 'Bảo trì theo kỳ hạn tiêu chuẩn của nhà sản xuất',
            'icon' => 'banknote',
          ],
        ],
        'title_field' => '{{{ title }}}',
      ]
    );

    $this->end_controls_section();
  }

  protected function render(): void
  {
    $settings = $this->get_settings_for_display();

    eai_render_template('templates/EAI-process-section.php', [
      'background_image' => $settings['background_image'] ?? [],
      'background_image_resolution' => $settings['background_image_resolution'] ?? 'large',
      'intro_content' => $settings['intro_content'] ?? '',
      'steps' => $settings['steps'] ?? [],
    ]);
  }
}
