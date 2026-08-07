<?php

class EAI_Youtube_Video_List_Widget extends \Elementor\Widget_Base
{
  private const TEMPLATE = 'templates/EAI-youtube-video-list.php';

  public function get_name(): string
  {
    return 'eai_youtube_video_list_widget';
  }

  public function get_title(): string
  {
    return esc_html__('ICHouse — Danh sách video YouTube', 'eai');
  }

  public function get_icon(): string
  {
    return 'eicon-youtube';
  }

  public function get_categories(): array
  {
    return eai_get_widget_categories();
  }

  public function get_keywords(): array
  {
    return ['youtube', 'video', 'list', 'grid', 'eai', 'ichouse'];
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
      'youtube_video',
      [
        'label' => esc_html__('YouTube URL hoặc video ID', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => '',
        'label_block' => true,
        'placeholder' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
      ]
    );

    $repeater->add_control(
      'title',
      [
        'label' => esc_html__('Tiêu đề iframe', 'eai'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'default' => '',
        'label_block' => true,
        'description' => esc_html__('Dùng cho khả năng truy cập; để trống sẽ dùng tiêu đề mặc định.', 'eai'),
      ]
    );

    $this->add_control(
      'items',
      [
        'label' => esc_html__('Videos', 'eai'),
        'type' => \Elementor\Controls_Manager::REPEATER,
        'fields' => $repeater->get_controls(),
        'default' => [],
        'title_field' => '{{{ title || youtube_video }}}',
      ]
    );

    $this->end_controls_section();
  }

  protected function render(): void
  {
    $settings = $this->get_settings_for_display();
    $widget_id = $this->get_id();
    $props = eai_youtube_video_list_get_rc_props($settings, $widget_id);

    if (eai_is_elementor_edit_mode() && empty($props['items'])) {
      $props = eai_youtube_video_list_get_editor_sample_props($settings, $widget_id);
      $this->render_props($props);
      return;
    }

    if (empty($props['items'])) {
      eai_render_template(self::TEMPLATE, ['empty' => true]);
      return;
    }

    $this->render_props($props);
  }

  /** @param array<string, mixed> $props */
  private function render_props(array $props): void
  {
    $result = eai_rc_render_html('YoutubeVideoList', $props);

    eai_render_template(self::TEMPLATE, [
      'html' => is_wp_error($result) ? '' : $result['html'],
      'error' => is_wp_error($result) ? $result : null,
    ]);
  }
}
