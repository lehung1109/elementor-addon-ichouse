<?php

class EAI_Construction_Highlights_Widget extends \Elementor\Widget_Base
{
  public function get_name(): string
  {
    return 'eai_construction_highlights_widget';
  }

  public function get_title(): string
  {
    return esc_html__('ICHouse — Điểm nổi bật', 'eai');
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
    return ['diem noi bat', 'highlights', 'accordion', 'construction', 'eai', 'ichouse'];
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
      'subtitle',
      [
        'label' => esc_html__('Subtitle', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => 'ĐIỂM NỔI BẬT CỦA ICHOUSE',
        'label_block' => true,
      ]
    );

    $this->add_control(
      'title_html',
      [
        'label' => esc_html__('Title (HTML)', 'eai'),
        'type' => \Elementor\Controls_Manager::WYSIWYG,
        'default' => 'Với hơn 10 năm kinh nghiệm trong lĩnh vực xây dựng, <span class="text-brand-gold">ICHOUSE</span> tự hào là đơn vị <span class="text-brand-gold">tiên phong</span> trong việc cung cấp các giải pháp xây dựng toàn diện và đồng bộ.',
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
      'icon_image',
      [
        'label' => esc_html__('Icon image', 'eai'),
        'type' => \Elementor\Controls_Manager::MEDIA,
        'label_block' => true,
      ]
    );

    $repeater->add_control(
      'icon_image_resolution',
      [
        'label' => esc_html__('Icon resolution', 'eai'),
        'type' => \Elementor\Controls_Manager::SELECT,
        'default' => 'thumbnail',
        'options' => eai_get_image_size_options(),
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
            'title' => 'Đối tác thi công số 1 của các công ty thiết kế hàng đầu Việt Nam',
            'content_html' => '<p>ICHOUSE đồng hành cùng các công ty thiết kế hàng đầu để hiện thực hóa ý tưởng kiến trúc thành công trình chất lượng, đúng tiến độ và thẩm mỹ.</p>',
            'icon_image' => [
              'url' => 'https://placehold.co/80x80/ffffff/022B63/png?text=01',
            ],
            'default_open' => 'yes',
          ],
          [
            'title' => 'Đội ngũ chuyên gia giàu kinh nghiệm',
            'content_html' => '<p>Đội ngũ kiến trúc sư và quản lý dự án dày dạn kinh nghiệm, kiểm soát chặt chẽ từng giai đoạn từ thiết kế đến bàn giao.</p>',
            'icon_image' => [
              'url' => 'https://placehold.co/80x80/ffffff/022B63/png?text=02',
            ],
            'default_open' => '',
          ],
          [
            'title' => 'Đội ngũ Kỹ sư chuyên môn cao',
            'content_html' => '<p>Kỹ sư chuyên môn cao giám sát kỹ thuật tại công trường, đảm bảo tiêu chuẩn an toàn và chất lượng thi công.</p>',
            'icon_image' => [
              'url' => 'https://placehold.co/80x80/ffffff/022B63/png?text=03',
            ],
            'default_open' => '',
          ],
          [
            'title' => 'Dịch vụ xây dựng toàn diện và đồng bộ',
            'content_html' => '<p>Cung cấp giải pháp xây dựng đồng bộ từ phần thô đến hoàn thiện, tối ưu chi phí và tiến độ cho chủ đầu tư.</p>',
            'icon_image' => [
              'url' => 'https://placehold.co/80x80/ffffff/022B63/png?text=04',
            ],
            'default_open' => '',
          ],
        ],
        'title_field' => '{{{ title }}}',
      ]
    );

    $this->add_control(
      'scroll_reveal_target_id',
      [
        'label' => esc_html__('Scroll reveal target ID', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => 'construction-highlights',
        'description' => esc_html__('DOM id trên section (IntersectionObserver).', 'eai'),
      ]
    );

    $this->end_controls_section();

    $this->start_controls_section(
      'section_image',
      [
        'label' => esc_html__('Image', 'eai'),
        'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_control(
      'image',
      [
        'label' => esc_html__('Image', 'eai'),
        'type' => \Elementor\Controls_Manager::MEDIA,
        'default' => [
          'url' => 'https://placehold.co/960x720/888888/ffffff?text=Construction+Highlights',
        ],
      ]
    );

    $this->add_control(
      'image_resolution',
      [
        'label' => esc_html__('Image resolution', 'eai'),
        'type' => \Elementor\Controls_Manager::SELECT,
        'default' => 'large',
        'options' => eai_get_image_size_options(),
      ]
    );

    $this->end_controls_section();
  }

  protected function get_rc_props(): array
  {
    return eai_construction_highlights_get_rc_props($this->get_settings_for_display());
  }

  protected function render(): void
  {
    $props = $this->get_rc_props();
    $subtitle = trim((string) ($props['subtitle'] ?? ''));
    $title_html = trim(wp_strip_all_tags((string) ($props['titleHtml'] ?? '')));
    $items = is_array($props['items'] ?? null) ? $props['items'] : [];
    $image_url = trim((string) (($props['image']['url'] ?? '') ?: ''));

    if ($subtitle === '' && $title_html === '' && $items === [] && $image_url === '') {
      eai_render_template('templates/EAI-construction-highlights.php', [
        'empty' => true,
      ]);
      return;
    }

    $result = eai_rc_render_html('ConstructionHighlights', $props);

    eai_render_template('templates/EAI-construction-highlights.php', [
      'html' => is_wp_error($result) ? '' : $result['html'],
      'error' => is_wp_error($result) ? $result : null,
    ]);
  }
}
