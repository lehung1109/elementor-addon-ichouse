<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class OceanParkProjectSamplesHelperTest extends TestCase
{
  public function testResolvesPublishedPostsInSelectionOrderAndMapsToOceanParkProjectSamplesModel(): void
  {
    $props = eai_ocean_park_project_samples_get_rc_props([
      'class_name' => ' custom-samples ',
      'eyebrow' => ' THƯ VIỆN MẪU THIẾT KẾ ',
      'title' => ' Mẫu Thiết Kế Nội Thất Cao Cấp ',
      'description' => ' Bộ sưu tập mặt bằng và phối cảnh 3D ',
      'post_type' => 'project',
      'selected_posts_project' => [11, 9, 7, 11, 8, 14],
      'image_resolution' => 'large',
      'all_zones_label' => 'Tất cả phân khu',
      'zone_taxonomy' => 'project-investor',
      'all_unit_types_label' => 'Tất cả loại căn',
      'unit_type_taxonomy' => 'project-model',
      'tower_taxonomy' => 'project-category',
      'area_field' => 'field_location_text',
      'apartment_code_field' => 'field_location',
      'style_field' => 'field_location_textarea',
      'budget_field' => 'field_location_radio',
      'cta_banner_title' => ' Bạn cần mặt bằng bố trí 2D? ',
      'cta_banner_description' => ' Đăng ký để nhận phương án miễn phí. ',
      'cta_banner_button_label' => ' Nhận bản vẽ theo mã căn ',
      'cta_banner_button_link' => ['url' => '#dang-ky-tu-van'],
    ]);

    self::assertSame('custom-samples', $props['className']);
    self::assertSame('THƯ VIỆN MẪU THIẾT KẾ', $props['eyebrow']);
    self::assertSame('Mẫu Thiết Kế Nội Thất Cao Cấp', $props['title']);
    self::assertCount(2, $props['featuredProjects']);
    self::assertSame('Dự án thứ hai', $props['featuredProjects'][0]['title']);
    self::assertSame('Biệt thự mẫu', $props['featuredProjects'][1]['title']);
    self::assertSame('Excerpt không được dùng', $props['featuredProjects'][1]['summary']);
    self::assertSame('ichouse', $props['featuredProjects'][1]['zoneId']);
    self::assertSame('ICHouse', $props['featuredProjects'][1]['zoneLabel']);
    self::assertSame('nghi-duong', $props['featuredProjects'][1]['unitTypeId']);
    self::assertSame('Nghỉ dưỡng', $props['featuredProjects'][1]['unitTypeLabel']);
    self::assertSame('Villa', $props['featuredProjects'][1]['towerLabel']);
    self::assertSame('Đà Nẵng', $props['featuredProjects'][1]['area']);
    self::assertSame('Hà Nội', $props['featuredProjects'][1]['apartmentCode']);
    self::assertSame('Văn phòng Hồ Chí Minh', $props['featuredProjects'][1]['style']);
    self::assertSame('Làm việc từ xa', $props['featuredProjects'][1]['budget']);
    self::assertSame('https://example.com/project-7', $props['featuredProjects'][1]['detailLink']['url']);
    self::assertTrue($props['featuredProjects'][1]['isRealProject']);
    self::assertSame(
      [
        ['id' => 'all', 'label' => 'Tất cả phân khu'],
        ['id' => 'ichouse', 'label' => 'ICHouse'],
      ],
      $props['zoneTabs']
    );
    self::assertSame(
      [
        ['id' => 'all', 'label' => 'Tất cả loại căn'],
        ['id' => 'nghi-duong', 'label' => 'Nghỉ dưỡng'],
      ],
      $props['unitTypeTabs']
    );
    self::assertSame('Bạn cần mặt bằng bố trí 2D?', $props['ctaBanner']['title']);
    self::assertSame('Nhận bản vẽ theo mã căn', $props['ctaBanner']['buttonLabel']);
    self::assertSame('#dang-ky-tu-van', $props['ctaBanner']['buttonLink']['url']);
  }

  public function testBuildsEditorSampleWithSevenCardsAndFilterTabs(): void
  {
    $sample = eai_ocean_park_project_samples_get_editor_sample_props([
      'class_name' => ' sample-class ',
      'eyebrow' => 'THƯ VIỆN MẪU',
    ]);

    self::assertSame('sample-class', $sample['className']);
    self::assertSame('THƯ VIỆN MẪU', $sample['eyebrow']);
    self::assertCount(9, $sample['featuredProjects']);
    self::assertFalse($sample['featuredProjects'][0]['isLocked']);
    self::assertTrue($sample['featuredProjects'][7]['isLocked']);
    self::assertTrue($sample['featuredProjects'][8]['isLocked']);
    self::assertCount(7, $sample['zoneTabs']);
    self::assertCount(7, $sample['unitTypeTabs']);

    $legacy_seven = array_fill(0, 7, ['title' => 'Legacy item']);
    $migrated = eai_ocean_park_project_samples_prepare_manual_entries($legacy_seven);
    self::assertCount(9, $migrated);
    self::assertSame('yes', $migrated[7]['is_locked']);
    self::assertSame('yes', $migrated[8]['is_locked']);

    $edited_seven = array_fill(0, 7, ['title' => 'Edited item', 'is_locked' => '']);
    self::assertCount(7, eai_ocean_park_project_samples_prepare_manual_entries($edited_seven));

    // When Elementor's Control_Repeater::get_value() pre-populates 'is_locked' => '' on $entries,
    // passing the raw un-merged repeater rows ($raw_entries without 'is_locked') still appends the 2 locked items.
    $migrated_with_raw = eai_ocean_park_project_samples_prepare_manual_entries($edited_seven, $legacy_seven);
    self::assertCount(9, $migrated_with_raw);
    self::assertSame('yes', $migrated_with_raw[7]['is_locked']);
    self::assertSame('yes', $migrated_with_raw[8]['is_locked']);
  }

  public function testSourceWiringUsesWrapperComponentAndPostSelectionControls(): void
  {
    $root = dirname(__DIR__);
    $widget = file_get_contents($root . '/includes/widgets/EAI-ocean-park-project-samples.php');
    $template = file_get_contents($root . '/includes/templates/EAI-ocean-park-project-samples.php');
    $helpers = file_get_contents($root . '/includes/helpers/bootstrap.php');
    $plugin = file_get_contents($root . '/includes/plugin.php');

    self::assertIsString($widget);
    self::assertStringContainsString("eai_rc_render_html('OceanParkProjectSamplesWrapper', \$props)", $widget);
    self::assertStringContainsString("'manual_entries'", $widget);
    self::assertStringContainsString("'is_locked'", $widget);
    self::assertStringContainsString("get_data('settings')", $widget);
    self::assertStringContainsString("'mark_as_real_project'", $widget);
    self::assertIsString($template);
    self::assertStringContainsString('echo $html;', $template);
    self::assertStringNotContainsString('wp_kses_post', $template);
    self::assertStringContainsString("require_once __DIR__ . '/ocean-park-project-samples.php';", (string) $helpers);
    self::assertStringContainsString("widgets/EAI-ocean-park-project-samples.php", (string) $plugin);
    self::assertStringContainsString('new \\EAI_Ocean_Park_Project_Samples_Widget()', (string) $plugin);
  }
}
