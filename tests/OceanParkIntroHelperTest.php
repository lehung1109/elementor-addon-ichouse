<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class OceanParkIntroHelperTest extends TestCase
{
  public function testMapsSettingsToOceanParkIntroModelProps(): void
  {
    $props = eai_ocean_park_intro_get_rc_props([
      'class_name' => ' custom-intro ',
      'eyebrow' => ' IC HOUSE LUXURY ',
      'title' => ' Chuyên Thiết Kế & Thi Công Nội Thất Cao Cấp ',
      'lead_html' => '<p>Đoạn mở đầu</p>',
      'highlights' => [
        ['label' => ' Thiết kế 3D Bespoke ', 'value' => ' 250.000đ – 350.000đ/m² ', 'note' => ' KTS trưởng chủ trì '],
        ['label' => '', 'value' => 'Bỏ qua dòng thiếu label'],
      ],
      'subdivision_badges' => "The London (Tòa LD1 • LD2 • LD3)\n\n The Beverly (Tòa BE1 • BE2 • BE3 • BE4) ",
      'steps' => [
        [
          'number' => ' 01 ',
          'title' => ' Khảo sát thực tế ',
          'description' => ' Đo đạc trực tiếp căn hộ ',
        ],
        [
          'number' => '02',
          'title' => '',
          'description' => '',
        ],
      ],
      'primary_cta_label' => ' Gọi tư vấn: 0869 855 885 ',
      'primary_cta_link' => ['url' => 'tel:0869855885', 'is_external' => false, 'nofollow' => false],
      'secondary_cta_label' => ' Nhận báo giá theo mã căn ',
      'secondary_cta_link' => ['url' => '#dang-ky-tu-van', 'is_external' => false, 'nofollow' => false],
    ]);

    self::assertSame('custom-intro', $props['className']);
    self::assertSame('IC HOUSE LUXURY', $props['eyebrow']);
    self::assertSame('Chuyên Thiết Kế & Thi Công Nội Thất Cao Cấp', $props['title']);
    self::assertSame('<p>Đoạn mở đầu</p>', $props['leadHtml']);
    self::assertCount(1, $props['highlights']);
    self::assertSame('Thiết kế 3D Bespoke', $props['highlights'][0]['label']);
    self::assertSame('250.000đ – 350.000đ/m²', $props['highlights'][0]['value']);
    self::assertSame('KTS trưởng chủ trì', $props['highlights'][0]['note']);
    self::assertSame(
      ['The London (Tòa LD1 • LD2 • LD3)', 'The Beverly (Tòa BE1 • BE2 • BE3 • BE4)'],
      $props['subdivisionBadges']
    );
    self::assertCount(1, $props['steps']);
    self::assertSame('01', $props['steps'][0]['number']);
    self::assertSame('Khảo sát thực tế', $props['steps'][0]['title']);
    self::assertSame('Gọi tư vấn: 0978 305 740', $props['primaryCta']['label']);
    self::assertSame('tel:0978305740', $props['primaryCta']['link']['url']);
    self::assertSame('Nhận báo giá theo mã căn', $props['secondaryCta']['label']);
    self::assertSame('#dang-ky-tu-van', $props['secondaryCta']['link']['url']);
  }

  public function testPreservesUserEditedValuesWithoutHardcodedOverrides(): void
  {
    $props = eai_ocean_park_intro_get_rc_props([
      'eyebrow' => 'IC HOUSE LUXURY — NỘI THẤT MAY ĐO ĐẠI ĐÔ THỊ OCEAN PARK 1 • 2 • 3',
      'title' => 'Chuyên Thiết Kế & Thi Công Nội Thất Cao Cấp Tại Vinhomes Ocean Park',
      'lead_html' => '<p>Đoạn định vị giải pháp tư vấn mặt bằng chuyên sâu tùy biến riêng.</p>',
      'highlights' => [
        ['label' => 'Thiết kế 3D Bespoke', 'value' => '250k', 'note' => 'KTS trưởng 5–15 năm kinh nghiệm chủ trì'],
        ['label' => 'H2', 'value' => 'V2'],
        ['label' => 'H3', 'value' => 'V3'],
        ['label' => 'H4', 'value' => 'V4'],
      ],
      'subdivision_badges' => "The London (Tòa LD1 • LD2 • LD3)\nB2\nB3\nB4\nB5\nCăn hộ cao tầng Ocean Park 3",
      'steps' => [
        ['number' => '01', 'title' => 'Khảo sát thực tế tại tòa trong 15 phút', 'description' => 'Mô tả bước 1 do admin sửa'],
        ['number' => '02', 'title' => 'Bước 2 tùy biến', 'description' => 'Mô tả bước 2'],
        ['number' => '03', 'title' => 'Bước 3 tùy biến', 'description' => 'Mô tả bước 3'],
      ],
    ]);

    self::assertSame('IC HOUSE LUXURY — NỘI THẤT MAY ĐO ĐẠI ĐÔ THỊ OCEAN PARK 1 • 2 • 3', $props['eyebrow']);
    self::assertSame('Chuyên Thiết Kế & Thi Công Nội Thất Cao Cấp Tại Vinhomes Ocean Park', $props['title']);
    self::assertSame('<p>Đoạn định vị giải pháp tư vấn mặt bằng chuyên sâu tùy biến riêng.</p>', $props['leadHtml']);
    self::assertSame('250k', $props['highlights'][0]['value']);
    self::assertSame('B2', $props['subdivisionBadges'][1]);
    self::assertSame('Mô tả bước 1 do admin sửa', $props['steps'][0]['description']);
  }

  public function testDetectsEmptyPropsAndBuildsEditorSample(): void
  {
    $empty_props = eai_ocean_park_intro_get_rc_props([]);
    self::assertTrue(eai_ocean_park_intro_props_are_empty($empty_props));

    $sample = eai_ocean_park_intro_get_editor_sample_props(['class_name' => ' editor-intro ']);
    self::assertFalse(eai_ocean_park_intro_props_are_empty($sample));
    self::assertSame('editor-intro', $sample['className']);
    self::assertNotEmpty($sample['highlights']);
    self::assertNotEmpty($sample['subdivisionBadges']);
    self::assertNotEmpty($sample['steps']);
  }

  public function testSourceWiringRegistersWidgetHelperAndTemplate(): void
  {
    $root = dirname(__DIR__);
    $widget = file_get_contents($root . '/includes/widgets/EAI-ocean-park-intro.php');
    $template = file_get_contents($root . '/includes/templates/EAI-ocean-park-intro.php');
    $helpers = file_get_contents($root . '/includes/helpers/bootstrap.php');
    $plugin = file_get_contents($root . '/includes/plugin.php');

    self::assertIsString($widget);
    self::assertStringContainsString("eai_rc_render_html('OceanParkIntro', \$props)", $widget);
    self::assertStringContainsString('eai_is_elementor_edit_mode()', $widget);
    self::assertIsString($template);
    self::assertStringContainsString('echo $html;', $template);
    self::assertStringNotContainsString('wp_kses_post', $template);
    self::assertStringContainsString("require_once __DIR__ . '/ocean-park-intro.php';", (string) $helpers);
    self::assertStringContainsString("widgets/EAI-ocean-park-intro.php", (string) $plugin);
    self::assertStringContainsString('new \\EAI_Ocean_Park_Intro_Widget()', (string) $plugin);
  }
}
