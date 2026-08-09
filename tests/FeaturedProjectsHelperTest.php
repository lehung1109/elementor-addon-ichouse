<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class FeaturedProjectsHelperTest extends TestCase
{
  public function testResolvesAtMostThreeUniquePublishedPostsFromSelectedPostTypeInSelectionOrder(): void
  {
    self::assertSame(
      [13, 7, 11],
      eai_featured_projects_resolve_post_ids([
        'post_type' => 'project',
        'selected_posts' => [13, 9, 7, 13, 10, 11, 12],
      ])
    );
  }

  public function testMapsPostFieldsResponsiveFeaturedImageAndTaxonomyDescription(): void
  {
    $item = eai_rc_map_featured_project_from_post(7, [
      'image_resolution' => 'large',
      'investor_taxonomy' => 'project-investor',
      'model_taxonomy' => 'project-model',
    ]);

    self::assertNotNull($item);
    self::assertSame('https://example.com/logo-large.png', $item['image']['url']);
    self::assertSame('Attachment alt', $item['image']['alt']);
    self::assertSame(['width' => 320, 'height' => 128], $item['image']['display_dimensions']);
    self::assertSame('logo-large.png 320w', $item['image']['srcSet']);
    self::assertSame('(max-width: 320px) 100vw, 320px', $item['image']['sizes']);
    self::assertSame('Biệt thự mẫu', $item['title']);
    self::assertSame("Chủ đầu tư: ICHouse, Nhà đầu tư mẹ\nMô hình: Nghỉ dưỡng, Nhà ở", $item['description']);
    self::assertStringNotContainsString('Excerpt không được dùng', $item['description']);
    self::assertSame([
      'url' => 'https://example.com/project-7',
      'is_external' => false,
      'nofollow' => false,
    ], $item['link']);
  }

  public function testUsesFullImageSizeWhenEditorSelectsFullResolution(): void
  {
    $item = eai_rc_map_featured_project_from_post(7, ['image_resolution' => '']);

    self::assertNotNull($item);
    self::assertSame('https://example.com/logo-full.png', $item['image']['url']);
    self::assertSame('logo-full.png 320w', $item['image']['srcSet']);
  }

  public function testResolvesEditorCapabilityFromSelectedPostType(): void
  {
    self::assertSame('edit_projects', eai_featured_projects_editor_capability('project'));
    self::assertSame('edit_posts', eai_featured_projects_editor_capability('missing'));
  }

  public function testSkipsPostWithoutFeaturedImageOrPermalink(): void
  {
    self::assertNull(eai_rc_map_featured_project_from_post(8, ['image_resolution' => 'large']));
    self::assertNull(eai_rc_map_featured_project_from_post(14, ['image_resolution' => 'large']));
  }

  public function testMapsLegacyRepeaterItemsWhenNoPostsHaveBeenSelected(): void
  {
    $props = eai_featured_projects_get_rc_props([
      'post_type' => 'project',
      'items' => [[
        'image' => ['id' => 42],
        'image_resolution' => 'large',
        'title' => 'Dự án cũ',
        'description' => 'Mô tả cũ',
        'link' => ['url' => '/du-an-cu'],
      ]],
    ], 'legacy');

    self::assertCount(1, $props['items']);
    self::assertSame('Dự án cũ', $props['items'][0]['title']);
    self::assertSame('/du-an-cu', $props['items'][0]['link']['url']);
  }

  public function testBuildsPropsFromSelectedPostsWithUniqueTargetAndOptionalClassName(): void
  {
    $props = eai_featured_projects_get_rc_props([
      'post_type' => 'project',
      'selected_posts' => [7, 11],
      'image_resolution' => 'large',
      'investor_taxonomy' => 'project-investor',
      'model_taxonomy' => 'project-model',
      'class_name' => ' custom-projects ',
      'subtitle' => ' DỰ ÁN ',
      'title' => ' Dự án nổi bật ',
      'button_label' => ' XEM TẤT CẢ ',
      'button_link' => ['url' => '/du-an'],
    ], 'abc 123');

    self::assertSame('custom-projects', $props['className']);
    self::assertSame('DỰ ÁN', $props['subtitle']);
    self::assertSame('Dự án nổi bật', $props['title']);
    self::assertCount(2, $props['items']);
    self::assertSame('XEM TẤT CẢ', $props['buttonLabel']);
    self::assertSame('/du-an', $props['buttonLink']['url']);
    self::assertSame('featured-projects-abc123', $props['scrollReveal']['targetId']);
  }

  public function testEditorSampleContainsThreeCardsAndPreservesDisplaySettings(): void
  {
    $props = eai_featured_projects_get_editor_sample_props([
      'class_name' => ' sample-projects ',
      'subtitle' => 'DỰ ÁN MẪU',
      'title' => 'Các dự án mẫu',
      'button_label' => 'XEM THÊM',
      'button_link' => ['url' => '/du-an'],
    ], 'sample 42');

    self::assertSame('sample-projects', $props['className']);
    self::assertSame('DỰ ÁN MẪU', $props['subtitle']);
    self::assertSame('Các dự án mẫu', $props['title']);
    self::assertCount(3, $props['items']);
    self::assertSame("Chủ đầu tư: ICHouse\nMô hình: Nhà ở", $props['items'][0]['description']);
    self::assertSame('featured-projects-sample42', $props['scrollReveal']['targetId']);
  }

  public function testWidgetUsesPostControlsEditorSampleEmptyBranchAndFeaturedProjectsComponent(): void
  {
    $source = file_get_contents(dirname(__DIR__) . '/includes/widgets/EAI-featured-projects.php');

    self::assertIsString($source);
    self::assertStringContainsString("'post_type'", $source);
    self::assertStringContainsString("'selected_posts_' . \$post_type", $source);
    self::assertStringContainsString("'image_resolution'", $source);
    self::assertStringContainsString("'investor_taxonomy'", $source);
    self::assertStringContainsString("'model_taxonomy'", $source);
    self::assertStringContainsString("'maximumSelectionLength' => 3", $source);
    self::assertStringContainsString("add_query_arg([", $source);
    self::assertStringNotContainsString("'data' => [", $source);
    self::assertStringContainsString('eai_featured_projects_get_rc_props($settings, (string) $this->get_id())', $source);
    self::assertStringContainsString('eai_is_elementor_edit_mode()', $source);
    self::assertStringContainsString('eai_featured_projects_get_editor_sample_props($settings, (string) $this->get_id())', $source);
    self::assertStringContainsString("if (empty(\$props['items']))", $source);
    self::assertStringContainsString("'empty' => true", $source);
    self::assertStringContainsString("eai_rc_render_html('FeaturedProjects', \$props)", $source);
    self::assertStringNotContainsString('new \\Elementor\\Repeater()', $source);
    self::assertStringNotContainsString("'scroll_reveal_target_id'", $source);
  }

  public function testWidgetDoesNotRequireDedicatedEditorJavaScript(): void
  {
    $plugin_source = file_get_contents(dirname(__DIR__) . '/includes/plugin.php');

    self::assertIsString($plugin_source);
    self::assertFileDoesNotExist(dirname(__DIR__) . '/assets/js/eai-featured-projects-editor.js');
    self::assertStringNotContainsString('eai-featured-projects-editor', $plugin_source);
  }

  public function testTemplateHandlesEmptyErrorAndRawSsrHtml(): void
  {
    $source = file_get_contents(dirname(__DIR__) . '/includes/templates/EAI-featured-projects.php');

    self::assertIsString($source);
    self::assertStringContainsString("if (! empty(\$args['empty']))", $source);
    self::assertStringContainsString('eai_rc_render_error_message($error)', $source);
    self::assertStringContainsString('echo $html;', $source);
    self::assertStringNotContainsString('wp_kses_post', $source);
  }
}
