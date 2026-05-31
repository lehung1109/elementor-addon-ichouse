<?php

class EAI_Feature_Cards_Carousel_Widget extends \Elementor\Widget_Base
{

  public function get_name(): string
  {
    return 'eai_feature_cards_carousel_widget';
  }

  public function get_title(): string
  {
    return esc_html__('ICHouse — Carousel thẻ tính năng', 'eai');
  }

  public function get_icon(): string
  {
    return 'eicon-posts-carousel';
  }

  public function get_categories(): array
  {
    return eai_get_widget_categories();
  }

  public function get_keywords(): array
  {
    return ['feature', 'cards', 'carousel', 'slider', 'the tinh nang', 'eai', 'ichouse'];
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
      'posts_offset',
      [
        'label' => esc_html__('Bỏ qua N bài đầu', 'eai'),
        'type' => \Elementor\Controls_Manager::NUMBER,
        'min' => 0,
        'max' => 50,
        'step' => 1,
        'default' => 0,
        'description' => esc_html__(
          'Bỏ qua N bài mới nhất trong kết quả taxonomy (sau khi loại bài đang xem), rồi mới lấy tối đa số bài ở trên. Dùng khi trang có nhiều block cùng taxonomy.',
          'eai'
        ),
        'condition' => [
          'content_source' => 'taxonomy',
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
      'excerpt_length',
      [
        'label' => esc_html__('Độ dài mô tả (ký tự)', 'eai'),
        'type' => \Elementor\Controls_Manager::NUMBER,
        'min' => 40,
        'max' => 500,
        'step' => 10,
        'default' => 120,
      ]
    );

    $this->end_controls_section();

    $this->start_controls_section(
      'section_carousel',
      [
        'label' => esc_html__('Carousel', 'eai'),
        'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
      ]
    );

    $this->add_control(
      'slides_per_view',
      [
        'label' => esc_html__('Slides per view', 'eai'),
        'type' => \Elementor\Controls_Manager::NUMBER,
        'min' => 1,
        'max' => 6,
        'step' => 1,
        'default' => 3,
      ]
    );

    $this->add_control(
      'space_between',
      [
        'label' => esc_html__('Space between (px)', 'eai'),
        'type' => \Elementor\Controls_Manager::NUMBER,
        'min' => 0,
        'max' => 64,
        'step' => 1,
        'default' => 16,
      ]
    );

    $this->add_control(
      'loop',
      [
        'label' => esc_html__('Loop', 'eai'),
        'type' => \Elementor\Controls_Manager::SWITCHER,
        'label_on' => esc_html__('Yes', 'eai'),
        'label_off' => esc_html__('No', 'eai'),
        'return_value' => 'yes',
        'default' => 'yes',
      ]
    );

    $this->end_controls_section();
  }

  protected function get_rc_props(): array
  {
    $settings = $this->get_settings_for_display();
    $slides_per_view = (int) ($settings['slides_per_view'] ?? 3);

    if ($slides_per_view < 1) {
      $slides_per_view = 1;
    }

    $space_between = (int) ($settings['space_between'] ?? 16);
    if ($space_between < 0) {
      $space_between = 0;
    }

    return [
      'items' => eai_rc_map_feature_cards_carousel_items($settings),
      'slidesPerView' => $slides_per_view,
      'spaceBetween' => $space_between,
      'loop' => ($settings['loop'] ?? 'yes') === 'yes',
    ];
  }

  protected function render(): void
  {
    $props = $this->get_rc_props();

    if (empty($props['items'])) {
      eai_render_template('templates/EAI-feature-cards-carousel.php', [
        'empty' => true,
      ]);
      return;
    }

    $result = eai_rc_render_html('FeatureCardsCarouselWrapper', $props);

    eai_render_template('templates/EAI-feature-cards-carousel.php', [
      'html' => is_wp_error($result) ? '' : $result['html'],
      'error' => is_wp_error($result) ? $result : null,
    ]);
  }
}
