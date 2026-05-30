<?php

class EAI_Product_Gallery_Widget extends \Elementor\Widget_Base
{

  public function get_name(): string
  {
    return 'eai_product_gallery_widget';
  }

  public function get_title(): string
  {
    return esc_html__('ICHouse — Product Gallery', 'eai');
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
    return ['product', 'gallery', 'acf', 'galerie', 'swiper', 'photoswipe', 'eai', 'ichouse'];
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
      'acf_field_name',
      [
        'label' => esc_html__('ACF field name', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => 'gallery',
        'label_block' => true,
        'description' => esc_html__(
          'Tên field ACF Galerie 4 trên bài đang xem (không phải tên field group).',
          'eai'
        ),
      ]
    );

    $this->add_control(
      'image_size',
      [
        'label' => esc_html__('Image size', 'eai'),
        'type' => \Elementor\Controls_Manager::SELECT,
        'default' => 'full',
        'options' => eai_get_image_size_options(),
        'description' => esc_html__(
          'Dùng full cho lightbox PhotoSwipe (kích thước gốc).',
          'eai'
        ),
      ]
    );

    $this->add_control(
      'class_name',
      [
        'label' => esc_html__('CSS class (optional)', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => '',
        'label_block' => true,
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

  protected function get_rc_props(): array
  {
    $settings = $this->get_settings_for_display();
    $post_id = $this->get_current_post_id();
    $field_name = sanitize_key((string) ($settings['acf_field_name'] ?? 'gallery'));
    if ($field_name === '') {
      $field_name = 'gallery';
    }

    $image_size = (string) ($settings['image_size'] ?? 'full');
    if ($image_size === '') {
      $image_size = 'full';
    }

    if ($post_id <= 0 || ! function_exists('get_field')) {
      return ['items' => []];
    }

    $gallery = get_field($field_name, $post_id);

    if (! is_array($gallery) || $gallery === []) {
      return ['items' => []];
    }

    $props = [
      'items' => eai_rc_map_product_gallery_items_from_acf_galerie_4($gallery, $image_size),
    ];

    $class_name = trim((string) ($settings['class_name'] ?? ''));
    if ($class_name !== '') {
      $props['className'] = $class_name;
    }

    return $props;
  }

  protected function render(): void
  {
    $props = $this->get_rc_props();

    if (empty($props['items'])) {
      eai_render_template('templates/EAI-product-gallery.php', [
        'html' => '',
        'error' => null,
        'empty' => true,
      ]);
      return;
    }

    $result = eai_rc_render_html('ProductGalleryWrapper', $props);

    eai_render_template('templates/EAI-product-gallery.php', [
      'html' => is_wp_error($result) ? '' : $result['html'],
      'error' => is_wp_error($result) ? $result : null,
    ]);
  }
}
