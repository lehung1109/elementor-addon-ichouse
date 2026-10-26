<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class OceanParkAdvantagesHelperTest extends TestCase
{
  public function testMapsLocalStrengthsCapabilityPillarsAndProofBannerToOceanParkAdvantagesModel(): void
  {
    $props = eai_ocean_park_advantages_get_rc_props([
      'class_name' => ' custom-adv ',
      'eyebrow' => ' 3 LỢI THẾ ĐỊA LÝ ĐỘC QUYỀN ',
      'title' => ' Nhà Mẫu Trong Khu Đô Thị ',
      'description' => ' Hệ sinh thái bao quanh đại đô thị ',
      'local_strengths' => [
        [
          'badge' => ' 01 • NHÀ MẪU ',
          'title' => ' Nhà Mẫu Nội Thất Thực Tế ',
          'address' => ' Sao Biển 23, Vinhomes Ocean Park ',
          'distance_note' => ' Chỉ 3 phút di chuyển ',
          'description' => ' Trải nghiệm trực tiếp ',
          'highlight_points' => "Tủ bếp cánh kính Anode\n\n Phụ kiện Blum/Hafele ",
          'image' => ['id' => 42],
          'image_resolution' => 'large',
          'image_alt' => ' Ảnh nhà mẫu ',
        ],
        [
          'title' => '',
          'address' => '',
        ],
      ],
      'capability_pillars' => [
        [
          'title' => ' Xưởng sản xuất 2.000m² ',
          'description' => ' Dây chuyền CNC & Sơn Inchem ',
          'checkpoints' => "Kiểm soát 100% cốt gỗ\n\n Độ chính xác từng milimet ",
          'image' => ['id' => 42],
          'image_resolution' => 'medium',
          'image_alt' => ' Ảnh xưởng ',
        ],
        [
          'title' => '',
        ],
      ],
      'proof_banner_title' => ' Đặt lịch tham quan Nhà mẫu ',
      'proof_banner_description' => ' Có xe đưa đón miễn phí ',
      'proof_banner_button_label' => ' Đặt lịch tham quan ',
      'proof_banner_button_link' => ['url' => '#dang-ky-tu-van'],
    ]);

    self::assertSame('custom-adv', $props['className']);
    self::assertSame('3 LỢI THẾ ĐỊA LÝ ĐỘC QUYỀN', $props['eyebrow']);
    self::assertCount(1, $props['localStrengths']);
    self::assertSame('01 • NHÀ MẪU', $props['localStrengths'][0]['badge']);
    self::assertSame('Sao Biển 23, Vinhomes Ocean Park', $props['localStrengths'][0]['address']);
    self::assertSame(['Tủ bếp cánh kính Anode', 'Phụ kiện Blum/Hafele'], $props['localStrengths'][0]['highlightPoints']);
    self::assertSame('https://example.com/logo-large.png', $props['localStrengths'][0]['image']['url']);
    self::assertSame('Ảnh nhà mẫu', $props['localStrengths'][0]['image']['alt']);
    self::assertCount(1, $props['capabilityPillars']);
    self::assertSame('Xưởng sản xuất 2.000m²', $props['capabilityPillars'][0]['title']);
    self::assertSame(['Kiểm soát 100% cốt gỗ', 'Độ chính xác từng milimet'], $props['capabilityPillars'][0]['checkpoints']);
    self::assertSame('https://example.com/logo-medium.png', $props['capabilityPillars'][0]['image']['url']);
    self::assertSame('Ảnh xưởng', $props['capabilityPillars'][0]['image']['alt']);
    self::assertSame('Đặt lịch tham quan Nhà mẫu', $props['proofBanner']['title']);
    self::assertSame('#dang-ky-tu-van', $props['proofBanner']['buttonLink']['url']);
  }

  public function testPreservesUserEditedValuesWithoutHardcodedOverrides(): void
  {
    $props = eai_ocean_park_advantages_get_rc_props([
      'eyebrow' => '3 LỢI THẾ ĐỊA LÝ ĐỘC QUYỀN CỦA IC HOUSE TẠI VINHOMES OCEAN PARK',
      'title' => 'Nhà Mẫu Trong Khu Đô Thị • Văn Phòng Kế Cận • Nhà Máy Sản Xuất Ngay Sát Ocean Park',
      'description' => 'Không phải di chuyển xa vào nội thành hay chờ đợi đội thợ từ nơi khác do admin viết riêng',
      'local_strengths' => [
        [
          'badge' => '01 • NHÀ MẪU TRONG ĐẠI ĐÔ THỊ',
          'title' => 'Nhà Mẫu Nội Thất Thực Tế Ngay Tại Vinhomes Ocean Park',
          'address' => 'Địa chỉ tùy biến',
        ],
      ],
      'capability_pillars' => [
        [
          'title' => 'Xưởng sản xuất trực tiếp 2.000m² — Dây chuyền CNC & Sơn Inchem khép kín',
          'description' => 'Mô tả xưởng tùy biến',
        ],
      ],
    ]);

    self::assertSame('3 LỢI THẾ ĐỊA LÝ ĐỘC QUYỀN CỦA IC HOUSE TẠI VINHOMES OCEAN PARK', $props['eyebrow']);
    self::assertSame('Nhà Mẫu Trong Khu Đô Thị • Văn Phòng Kế Cận • Nhà Máy Sản Xuất Ngay Sát Ocean Park', $props['title']);
    self::assertSame('Không phải di chuyển xa vào nội thành hay chờ đợi đội thợ từ nơi khác do admin viết riêng', $props['description']);
    self::assertSame('01 • NHÀ MẪU TRONG ĐẠI ĐÔ THỊ', $props['localStrengths'][0]['badge']);
    self::assertSame('Nhà Mẫu Nội Thất Thực Tế Ngay Tại Vinhomes Ocean Park', $props['localStrengths'][0]['title']);
    self::assertSame('Xưởng sản xuất trực tiếp 2.000m² — Dây chuyền CNC & Sơn Inchem khép kín', $props['capabilityPillars'][0]['title']);
  }

  public function testBuildsEditorSampleWhenEmpty(): void
  {
    $empty_props = eai_ocean_park_advantages_get_rc_props([]);
    self::assertTrue(eai_ocean_park_advantages_props_are_empty($empty_props));

    $sample = eai_ocean_park_advantages_get_editor_sample_props(['class_name' => ' adv-sample ']);
    self::assertFalse(eai_ocean_park_advantages_props_are_empty($sample));
    self::assertSame('adv-sample', $sample['className']);
    self::assertCount(3, $sample['localStrengths']);
    self::assertCount(2, $sample['capabilityPillars']);
  }

  public function testSourceWiringRegistersWidgetHelperAndTemplate(): void
  {
    $root = dirname(__DIR__);
    $widget = file_get_contents($root . '/includes/widgets/EAI-ocean-park-advantages.php');
    $template = file_get_contents($root . '/includes/templates/EAI-ocean-park-advantages.php');
    $helpers = file_get_contents($root . '/includes/helpers/bootstrap.php');
    $plugin = file_get_contents($root . '/includes/plugin.php');

    self::assertIsString($widget);
    self::assertStringContainsString("eai_rc_render_html('OceanParkAdvantages', \$props)", $widget);
    self::assertIsString($template);
    self::assertStringContainsString('echo $html;', $template);
    self::assertStringNotContainsString('wp_kses_post', $template);
    self::assertStringContainsString("require_once __DIR__ . '/ocean-park-advantages.php';", (string) $helpers);
    self::assertStringContainsString("widgets/EAI-ocean-park-advantages.php", (string) $plugin);
    self::assertStringContainsString('new \\EAI_Ocean_Park_Advantages_Widget()', (string) $plugin);
  }
}
