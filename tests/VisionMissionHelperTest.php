<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__) . '/includes/helpers/vision-mission.php';

final class VisionMissionHelperTest extends TestCase
{
  public function testMapsValidRowsAndSkipsInvalidRows(): void
  {
    self::assertSame([
      ['title' => 'Tầm nhìn', 'description' => 'Mô tả'],
      ['title' => 'Chỉ tiêu đề', 'description' => ''],
      ['title' => '', 'description' => 'Chỉ mô tả'],
    ], eai_rc_map_vision_mission_items([
      ['title' => ' Tầm nhìn ', 'description' => ' Mô tả '],
      ['title' => ' Chỉ tiêu đề ', 'description' => ' '],
      ['title' => ' ', 'description' => ' Chỉ mô tả '],
      ['title' => ' ', 'description' => ' '],
      'invalid-row',
    ]));
  }

  public function testBuildsPropsWithUniqueTargetIdAndOptionalClassName(): void
  {
    $props = eai_vision_mission_get_rc_props([
      'class_name' => ' custom-vision ',
      'columns' => [],
    ], 'widget @ 42');

    self::assertSame('custom-vision', $props['className']);
    self::assertSame('vision-mission-widget42', $props['scrollReveal']['targetId']);
  }

  public function testUsesFallbackTargetIdAndOmitsBlankClassName(): void
  {
    $props = eai_vision_mission_get_rc_props([
      'class_name' => ' ',
      'columns' => [],
    ], '***');

    self::assertArrayNotHasKey('className', $props);
    self::assertSame('vision-mission-widget', $props['scrollReveal']['targetId']);
  }

  public function testEditorSampleMirrorsVisionMissionFixture(): void
  {
    $props = eai_vision_mission_get_editor_sample_props([
      'class_name' => ' editor-vision ',
    ], 'editor-1');

    self::assertSame('editor-vision', $props['className']);
    self::assertSame('vision-mission-editor-1', $props['scrollReveal']['targetId']);
    self::assertCount(2, $props['columns']);
    self::assertSame('TẦM NHÌN', $props['columns'][0]['title']);
    self::assertCount(2, $props['columns'][0]['items']);
    self::assertSame('SỨ MỆNH', $props['columns'][1]['title']);
  }

  public function testWidgetUsesEditorSampleAndUniqueProps(): void
  {
    $source = file_get_contents(dirname(__DIR__) . '/includes/widgets/EAI-vision-mission.php');

    self::assertIsString($source);
    self::assertStringContainsString('eai_vision_mission_get_rc_props($settings, $this->get_id())', $source);
    self::assertStringContainsString('eai_is_elementor_edit_mode()', $source);
    self::assertStringContainsString('eai_vision_mission_get_editor_sample_props($settings, $this->get_id())', $source);
    self::assertStringNotContainsString('scroll_reveal_target_id', $source);
    self::assertSame(2, substr_count($source, "eai_rc_render_html('VisionMission', " . '$props' . ")"));
  }

  public function testEditorCssDisablesVisionMissionAnimation(): void
  {
    $source = file_get_contents(dirname(__DIR__) . '/assets/css/eai-elementor-editor.css');

    self::assertIsString($source);
    self::assertStringContainsString('div .vision-mission-column', $source);
    self::assertStringContainsString('opacity: 1 !important;', $source);
    self::assertStringContainsString('transform: none !important;', $source);
    self::assertStringContainsString('translate: none !important;', $source);
    self::assertStringContainsString('transition: none !important;', $source);
    self::assertStringContainsString('transition-delay: 0s !important;', $source);
  }
}
