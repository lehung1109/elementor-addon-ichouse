<?php

class EAI_Feature_Cards_Grid_Widget extends \Elementor\Widget_Base
{

  public function get_name(): string
  {
    return 'eai_feature_cards_grid_widget';
  }

  public function get_title(): string
  {
    return esc_html__('ICHouse — Lưới thẻ tính năng', 'eai');
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
    return ['feature', 'cards', 'grid', 'the tinh nang', 'eai', 'ichouse'];
  }

  /**
   * @return array<string, mixed>
   */
  private function get_select2_ajax_config(string $action): array
  {
    return [
      'url' => admin_url('admin-ajax.php'),
      'data_type' => 'json',
      'delay' => 250,
      'data' => [
        'action' => $action,
        'nonce' => wp_create_nonce('eai_feature_cards_editor'),
      ],
    ];
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
      'content_source',
      [
        'label' => esc_html__('Nguồn thẻ', 'eai'),
        'type' => \Elementor\Controls_Manager::SELECT,
        'default' => 'manual',
        'options' => [
          'manual' => esc_html__('Chọn từng bài viết', 'eai'),
          'taxonomy' => esc_html__('Theo taxonomy', 'eai'),
          'related' => esc_html__('Bài liên quan (bài hiện tại)', 'eai'),
        ],
      ]
    );

    $this->add_control(
      'post_type',
      [
        'label' => esc_html__('Post type', 'eai'),
        'type' => \Elementor\Controls_Manager::SELECT,
        'default' => 'post',
        'options' => eai_get_public_post_type_options(),
        'condition' => [
          'content_source!' => 'related',
        ],
      ]
    );

    $this->add_control(
      'selected_posts',
      [
        'label' => esc_html__('Bài viết', 'eai'),
        'type' => \Elementor\Controls_Manager::SELECT2,
        'multiple' => true,
        'label_block' => true,
        'options' => [],
        'description' => esc_html__(
          'Tự lấy ảnh đại diện, tiêu đề và đoạn mô tả ngắn từ bài viết. Chỉ hiển thị bài đã publish và có featured image.',
          'eai'
        ),
        'select2options' => [
          'ajax' => $this->get_select2_ajax_config('eai_feature_cards_search_posts'),
        ],
        'condition' => [
          'content_source' => 'manual',
        ],
      ]
    );

    $this->add_control(
      'taxonomy',
      [
        'label' => esc_html__('Taxonomy', 'eai'),
        'type' => \Elementor\Controls_Manager::SELECT,
        'default' => '',
        'options' => eai_get_public_taxonomy_options(),
        'description' => esc_html__(
          'Lấy N bài publish mới nhất thuộc post type đã chọn. Có thể giới hạn term bên dưới; để trống term = mọi bài có gán ít nhất một term trong taxonomy. Trên trang single post, bài đang xem sẽ bị loại khỏi danh sách.',
          'eai'
        ),
        'condition' => [
          'content_source' => 'taxonomy',
        ],
      ]
    );

    foreach (get_taxonomies(['public' => true], 'objects') as $taxonomy_obj) {
      if (! $taxonomy_obj || empty($taxonomy_obj->name)) {
        continue;
      }

      $taxonomy_name = (string) $taxonomy_obj->name;
      $taxonomy_label = $taxonomy_obj->labels->singular_name ?? $taxonomy_name;

      $this->add_control(
        'taxonomy_terms_' . $taxonomy_name,
        [
          'label' => sprintf(
            /* translators: %s: taxonomy singular label */
            esc_html__('Term (%s)', 'eai'),
            esc_html($taxonomy_label)
          ),
          'type' => \Elementor\Controls_Manager::SELECT2,
          'multiple' => true,
          'label_block' => true,
          'options' => eai_get_taxonomy_terms_as_options($taxonomy_name),
          'description' => esc_html__(
            'Chọn một hoặc nhiều term để giới hạn bài hiển thị. Để trống để lấy mọi bài có gán term trong taxonomy.',
            'eai'
          ),
          'condition' => [
            'content_source' => 'taxonomy',
            'taxonomy' => $taxonomy_name,
          ],
        ]
      );
    }

    $this->add_control(
      'taxonomy_posts_per_page',
      [
        'label' => esc_html__('Số bài tối đa', 'eai'),
        'type' => \Elementor\Controls_Manager::NUMBER,
        'min' => 1,
        'max' => 50,
        'step' => 1,
        'default' => 6,
        'condition' => [
          'content_source' => 'taxonomy',
        ],
      ]
    );

    $this->add_control(
      'related_taxonomies',
      [
        'label' => esc_html__('Taxonomies', 'eai'),
        'type' => \Elementor\Controls_Manager::SELECT2,
        'multiple' => true,
        'label_block' => true,
        'options' => eai_get_public_taxonomy_options(),
        'description' => esc_html__(
          'Để trống để dùng tất cả taxonomy public của post type bài hiện tại. Tự lấy term gán bài đang xem rồi tìm bài liên quan cùng term.',
          'eai'
        ),
        'condition' => [
          'content_source' => 'related',
        ],
      ]
    );

    $this->add_control(
      'related_posts_max',
      [
        'label' => esc_html__('Số bài tối đa', 'eai'),
        'type' => \Elementor\Controls_Manager::NUMBER,
        'min' => 1,
        'max' => 50,
        'step' => 1,
        'default' => 6,
        'description' => esc_html__(
          'Chỉ hoạt động trên trang single có bài publish. Bài đang xem bị loại. Chỉ hiển thị bài có featured image — số thẻ thực tế có thể ít hơn.',
          'eai'
        ),
        'condition' => [
          'content_source' => 'related',
        ],
      ]
    );

    $this->add_control(
      'posts_offset',
      [
        'label' => esc_html__('Bỏ qua N bài đầu', 'eai'),
        'type' => \Elementor\Controls_Manager::NUMBER,
        'min' => 0,
        'max' => 50,
        'step' => 1,
        'default' => 0,
        'description' => esc_html__(
          'Taxonomy: bỏ qua N bài mới nhất (sau khi loại bài đang xem), rồi lấy tối đa số bài ở «Số bài tối đa». Related: bỏ qua N bài đầu theo thứ tự bài liên quan (taxonomy → term → ngày), rồi lấy tối đa «Số bài tối đa». Dùng khi trang có nhiều block cùng nguồn.',
          'eai'
        ),
        'condition' => [
          'content_source' => ['taxonomy', 'related'],
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
      'show_description',
      [
        'label' => esc_html__('Hiển thị mô tả', 'eai'),
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label_on' => esc_html__('Yes', 'eai'),
        'label_off' => esc_html__('No', 'eai'),
        'return_value' => 'yes',
        'default' => 'yes',
      ]
    );

    $this->add_control(
      'excerpt_length',
      [
        'label' => esc_html__('Độ dài mô tả (ký tự)', 'eai'),
        'type' => \Elementor\Controls_Manager::NUMBER,
        'min' => 40,
        'max' => 500,
        'step' => 10,
        'default' => 120,
        'condition' => [
          'show_description' => 'yes',
        ],
      ]
    );

    $this->end_controls_section();

    $this->start_controls_section(
      'section_layout',
      [
        'label' => esc_html__('Layout', 'eai'),
        'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_control(
      'item_layout',
      [
        'label' => esc_html__('Layout thẻ', 'eai'),
        'type' => \Elementor\Controls_Manager::SELECT,
        'default' => 'stack',
        'options' => [
          'stack' => esc_html__('Ảnh trên, nội dung dưới', 'eai'),
          'media-left' => esc_html__('Ảnh trái (từ tablet)', 'eai'),
        ],
        'description' => esc_html__(
          'Áp dụng cùng một kiểu bố cục cho mọi thẻ trong lưới.',
          'eai'
        ),
      ]
    );

    $this->add_control(
      'columns_tablet',
      [
        'label' => esc_html__('Cột (tablet)', 'eai'),
        'type' => \Elementor\Controls_Manager::NUMBER,
        'min' => 1,
        'max' => 6,
        'step' => 1,
        'default' => 2,
      ]
    );

    $this->add_control(
      'columns_desktop',
      [
        'label' => esc_html__('Cột (desktop)', 'eai'),
        'type' => \Elementor\Controls_Manager::NUMBER,
        'min' => 1,
        'max' => 6,
        'step' => 1,
        'default' => 3,
      ]
    );

    $this->add_control(
      'gap',
      [
        'label' => esc_html__('Khoảng cách (px)', 'eai'),
        'type' => \Elementor\Controls_Manager::NUMBER,
        'min' => 0,
        'max' => 64,
        'step' => 1,
        'default' => 16,
      ]
    );

    $this->end_controls_section();
  }

  protected function render(): void
  {
    $settings = $this->get_settings_for_display();
    $props = eai_feature_cards_grid_get_rc_props($settings);

    if (eai_is_elementor_edit_mode() && empty($props['items'])) {
      $props = eai_feature_cards_grid_get_editor_sample_props($settings);
      $result = eai_rc_render_html('FeatureCardsGrid', $props);

      eai_render_template('templates/EAI-feature-cards-grid.php', [
        'html' => is_wp_error($result) ? '' : $result['html'],
        'error' => is_wp_error($result) ? $result : null,
      ]);
      return;
    }

    if (empty($props['items'])) {
      eai_render_template('templates/EAI-feature-cards-grid.php', [
        'empty' => true,
      ]);
      return;
    }

    $result = eai_rc_render_html('FeatureCardsGrid', $props);

    eai_render_template('templates/EAI-feature-cards-grid.php', [
      'html' => is_wp_error($result) ? '' : $result['html'],
      'error' => is_wp_error($result) ? $result : null,
    ]);
  }
}
