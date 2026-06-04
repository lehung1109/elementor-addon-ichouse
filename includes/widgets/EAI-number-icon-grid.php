<?php

class EAI_Number_Icon_Grid_Widget extends \Elementor\Widget_Base
{
  public function get_name(): string
  {
    return 'eai_number_icon_grid_widget';
  }

  public function get_title(): string
  {
    return esc_html__('ICHouse — Bước quy trình (số + icon)', 'eai');
  }

  public function get_icon(): string
  {
    return 'eicon-gallery-grid';
  }

  public function get_categories(): array
  {
    return eai_get_widget_categories();
  }

  public function get_keywords(): array
  {
    return ['number', 'icon', 'grid', 'process', 'quy trinh', 'buoc', 'eai', 'ichouse'];
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

    $repeater = new \Elementor\Repeater();

    $repeater->add_control(
      'number',
      [
        'label' => esc_html__('Number', 'eai'),
        'type' => \Elementor\Controls_Manager::NUMBER,
        'min' => 1,
        'step' => 1,
        'default' => 1,
      ]
    );

    $repeater->add_control(
      'image',
      [
        'label' => esc_html__('Icon', 'eai'),
        'type' => \Elementor\Controls_Manager::MEDIA,
        'label_block' => true,
      ]
    );

    $repeater->add_control(
      'image_resolution',
      [
        'label' => esc_html__('Image Resolution', 'eai'),
        'type' => \Elementor\Controls_Manager::SELECT,
        'default' => 'medium',
        'options' => eai_get_image_size_options(),
      ]
    );

    $repeater->add_control(
      'alt',
      [
        'label' => esc_html__('Alt text (optional)', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => '',
        'label_block' => true,
        'description' => esc_html__('Overrides attachment alt when set.', 'eai'),
      ]
    );

    $repeater->add_control(
      'title',
      [
        'label' => esc_html__('Title', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => '',
        'label_block' => true,
      ]
    );

    $this->add_control(
      'items',
      [
        'label' => esc_html__('Items', 'eai'),
        'type' => \Elementor\Controls_Manager::REPEATER,
        'fields' => $repeater->get_controls(),
        'default' => [
          [
            'number' => 1,
            'image' => ['url' => 'https://placehold.co/80x80/D9A441/ffffff/png?text=1'],
            'alt' => 'Khảo sát và tư vấn',
            'title' => 'Khảo sát và tư vấn về từng hạng mục',
          ],
          [
            'number' => 2,
            'image' => ['url' => 'https://placehold.co/80x80/D9A441/ffffff/png?text=2'],
            'alt' => 'Báo giá thiết kế và thi công',
            'title' => 'Báo giá thiết kế & thi công các hạng mục',
          ],
          [
            'number' => 3,
            'image' => ['url' => 'https://placehold.co/80x80/D9A441/ffffff/png?text=3'],
            'alt' => 'Thảo luận và ký hợp đồng',
            'title' => 'Thảo luận và ký hợp đồng thi công',
          ],
          [
            'number' => 4,
            'image' => ['url' => 'https://placehold.co/80x80/D9A441/ffffff/png?text=4'],
            'alt' => 'Triển khai thi công dự án',
            'title' => 'Triển khai thi công dự án nội thất',
          ],
          [
            'number' => 5,
            'image' => ['url' => 'https://placehold.co/80x80/D9A441/ffffff/png?text=5'],
            'alt' => 'Nghiệm thu và bàn giao',
            'title' => 'Nghiệm thu và bàn giao sản phẩm',
          ],
          [
            'number' => 6,
            'image' => ['url' => 'https://placehold.co/80x80/D9A441/ffffff/png?text=6'],
            'alt' => 'Bảo hành sản phẩm',
            'title' => 'Bảo hành sản phẩm sau khi bàn giao',
          ],
        ],
        'title_field' => '{{{ number }}} — {{{ title }}}',
      ]
    );

    $this->end_controls_section();
  }

  protected function get_rc_props(): array
  {
    $settings = $this->get_settings_for_display();
    $items = is_array($settings['items'] ?? null) ? $settings['items'] : [];

    return [
      'className' => (string) ($settings['class_name'] ?? ''),
      'items' => eai_rc_map_number_icon_grid_items($items),
    ];
  }

  protected function render(): void
  {
    $props = $this->get_rc_props();

    if (empty($props['items'])) {
      eai_render_template('templates/EAI-number-icon-grid.php', [
        'empty' => true,
      ]);
      return;
    }

    $result = eai_rc_render_html('NumberIconGrid', $props);

    eai_render_template('templates/EAI-number-icon-grid.php', [
      'html' => is_wp_error($result) ? '' : $result['html'],
      'error' => is_wp_error($result) ? $result : null,
    ]);
  }
}
