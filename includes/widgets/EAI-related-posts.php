<?php

class EAI_Related_Posts_Widget extends \Elementor\Widget_Base
{

  public function get_name(): string
  {
    return 'eai_related_posts_widget';
  }

  public function get_title(): string
  {
    return esc_html__('ICHouse — Bài viết liên quan', 'eai');
  }

  public function get_icon(): string
  {
    return 'eicon-post-list';
  }

  public function get_categories(): array
  {
    return eai_get_widget_categories();
  }

  public function get_keywords(): array
  {
    return ['related', 'posts', 'bai viet', 'lien quan', 'taxonomy', 'eai', 'ichouse'];
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
      'title',
      [
        'label' => esc_html__('Tiêu đề', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => '',
      ]
    );

    $this->add_control(
      'posts_count',
      [
        'label' => esc_html__('Số bài hiển thị', 'eai'),
        'type' => \Elementor\Controls_Manager::NUMBER,
        'min' => 1,
        'max' => 3,
        'step' => 1,
        'default' => 3,
      ]
    );

    $this->add_control(
      'taxonomies',
      [
        'label' => esc_html__('Taxonomies', 'eai'),
        'type' => \Elementor\Controls_Manager::SELECT2,
        'multiple' => true,
        'label_block' => true,
        'options' => eai_get_taxonomy_options_for_post_type('post'),
        'description' => esc_html__(
          'Để trống để dùng tất cả taxonomy public của post type bài hiện tại.',
          'eai'
        ),
      ]
    );

    $this->end_controls_section();
  }

  protected function get_current_post_id(): int
  {
    $post_id = (int) get_queried_object_id();
    if ($post_id <= 0) {
      $post_id = (int) get_the_ID();
    }

    return $post_id;
  }

  /**
   * @return array<int, string>
   */
  protected function get_selected_taxonomy_slugs(): array
  {
    $settings = $this->get_settings_for_display();
    $taxonomies = $settings['taxonomies'] ?? [];

    if (! is_array($taxonomies)) {
      return [];
    }

    $slugs = [];
    foreach ($taxonomies as $slug) {
      $slug = sanitize_key((string) $slug);
      if ($slug !== '') {
        $slugs[] = $slug;
      }
    }

    return $slugs;
  }

  protected function get_rc_props(): array
  {
    $settings = $this->get_settings_for_display();
    $post_id = $this->get_current_post_id();
    $posts_count = (int) ($settings['posts_count'] ?? 3);
    $taxonomy_slugs = $this->get_selected_taxonomy_slugs();

    if ($post_id <= 0) {
      return [
        'postId' => 0,
        'links' => [],
      ];
    }

    $post_ids = eai_related_posts_resolve($post_id, $posts_count, $taxonomy_slugs);

    return [
      'postId' => $post_id,
      'title' => $settings['title'] ?? '',
      'links' => eai_rc_map_related_post_links($post_ids),
    ];
  }

  protected function render(): void
  {
    $props = $this->get_rc_props();

    if (empty($props['links'])) {
      eai_render_template('templates/EAI-related-posts.php', [
        'html' => '',
        'error' => null,
        'empty' => true,
      ]);
      return;
    }

    $result = eai_rc_render_html('RelatedPostList', $props);

    eai_render_template('templates/EAI-related-posts.php', [
      'html' => is_wp_error($result) ? '' : $result['html'],
      'error' => is_wp_error($result) ? $result : null,
    ]);
  }
}
