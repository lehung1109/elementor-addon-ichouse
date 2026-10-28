<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class OceanParkPricingHelperTest extends TestCase
{
  public function testMapsFeaturedPackageEstimatesCommitmentsAndBottomPromptToOceanParkPricingModel(): void
  {
    $props = eai_ocean_park_pricing_get_rc_props([
      'class_name' => ' custom-pricing ',
      'eyebrow' => ' BẢNG GIÁ & DỰ TOÁN ',
      'title' => ' Báo Giá Thiết Kế & Thi Công ',
      'description' => ' Giải pháp nội thất may đo Bespoke ',
      'featured_badge' => ' GÓI BESPOKE LUXURY ',
      'featured_title' => ' Hoàn Thiện Nội Thất Trọn Gói ',
      'featured_price' => ' 180 – 260 triệu ',
      'featured_unit' => ' / căn hộ 2PN – 2PN+1 ',
      'featured_note' => ' Gói khởi điểm từ 120 triệu/căn ',
      'featured_handover_saving_note' => ' Tiết kiệm ngay 35 – 60 triệu/căn ',
      'primary_cta_label' => ' Gọi nhận dự toán: 0869 855 885 ',
      'primary_cta_link' => ['url' => 'tel:0869855885'],
      'secondary_cta_label' => ' Nhận BOQ chi tiết ',
      'secondary_cta_link' => ['url' => '#dang-ky-tu-van'],
      'unit_estimates_title' => ' Dự Toán Theo Loại Căn ',
      'unit_estimates_subtitle' => ' Áp dụng cho Ocean Park 1-2-3 ',
      'unit_estimates' => [
        [
          'unit_type' => ' 2PN / 2PN+1 ',
          'area_range' => ' 58m² – 76m² ',
          'price_range' => ' 180 – 260 triệu ',
          'highlight' => 'yes',
          'target_zones' => ' The Beverly, The London ',
        ],
        [
          'unit_type' => '',
          'price_range' => '',
        ],
      ],
      'room_estimates' => [
        [
          'room_name' => ' Không gian Phòng khách & Bếp ',
          'english_name' => ' Living & Dining Bespoke ',
          'price_range' => ' 75 – 125 triệu ',
        ],
        [
          'room_name' => '',
          'price_range' => '',
        ],
      ],
      'commitments' => [
        [
          'title' => ' Gói thi công bao gồm ',
          'items_text' => "Cải tạo trần – sàn | Trần thạch cao Vĩnh Tường\n\nHệ tủ bếp | Mặt đá Vicostone",
        ],
        [
          'title' => '',
          'items_text' => '',
        ],
      ],
      'bottom_prompt_title' => ' Nhận bảng dự toán BOQ ',
      'bottom_prompt_description' => ' KTS Trưởng gửi dự toán trong 30 phút ',
      'bottom_prompt_button_label' => ' Yêu cầu bảng dự toán ',
      'bottom_prompt_button_link' => ['url' => '#dang-ky-tu-van'],
    ]);

    self::assertSame('custom-pricing', $props['className']);
    self::assertSame('BẢNG GIÁ & DỰ TOÁN', $props['eyebrow']);
    self::assertSame('180 – 260 triệu', $props['featuredPackage']['price']);
    self::assertSame('Gọi nhận dự toán: 0869 855 885', $props['primaryCta']['label']);
    self::assertSame('tel:0869855885', $props['primaryCta']['link']['url']);
    self::assertCount(1, $props['unitEstimates']);
    self::assertSame('2PN / 2PN+1', $props['unitEstimates'][0]['unitType']);
    self::assertTrue($props['unitEstimates'][0]['highlight']);
    self::assertCount(1, $props['roomEstimates']);
    self::assertSame('Không gian Phòng khách & Bếp', $props['roomEstimates'][0]['roomName']);
    self::assertCount(1, $props['commitments']);
    self::assertSame('Gói thi công bao gồm', $props['commitments'][0]['title']);
    self::assertCount(2, $props['commitments'][0]['items']);
    self::assertSame('Cải tạo trần – sàn', $props['commitments'][0]['items'][0]['label']);
    self::assertSame('Trần thạch cao Vĩnh Tường', $props['commitments'][0]['items'][0]['detail']);
    self::assertSame('Nhận bảng dự toán BOQ', $props['bottomPrompt']['title']);
    self::assertSame('Yêu cầu bảng dự toán', $props['bottomPrompt']['buttonLabel']);
  }

  public function testPreservesUserEditedValuesWithoutHardcodedOverrides(): void
  {
    $props = eai_ocean_park_pricing_get_rc_props([
      'eyebrow' => 'BẢNG GIÁ & DỰ TOÁN ĐẦU TƯ PHÂN KHÚC CAO CẤP',
      'title' => 'Báo Giá Thiết Kế & Thi Công Nội Thất Cao Cấp Tại Vinhomes Ocean Park 1 • 2 • 3',
      'description' => 'Khác biệt hoàn toàn với các gói nội thất đại trà giá rẻ do admin ghi chú riêng',
      'featured_badge' => 'GÓI BESPOKE LUXURY ĐỒNG BỘ — LỰA CHỌN HÀNG ĐẦU TẠI OCEAN PARK',
      'featured_title' => 'Hoàn Thiện Nội Thất Trọn Gói Cao Cấp (Từ 120 triệu/căn • Phổ biến 180 – 260 triệu/căn)',
      'commitments' => [
        [
          'title' => 'Gói thi công bao gồm',
          'items' => [['label' => 'Item 1', 'detail' => 'Detail 1']],
        ],
        [
          'title' => 'Cam kết 2',
          'items' => [['label' => 'Item 2', 'detail' => 'Detail 2']],
        ],
        [
          'title' => 'Quyền lợi đặc quyền khách hàng',
          'items' => [['label' => 'Item 3', 'detail' => 'Detail 3']],
        ],
      ],
    ]);

    self::assertSame('BẢNG GIÁ & DỰ TOÁN ĐẦU TƯ PHÂN KHÚC CAO CẤP', $props['eyebrow']);
    self::assertSame('Báo Giá Thiết Kế & Thi Công Nội Thất Cao Cấp Tại Vinhomes Ocean Park 1 • 2 • 3', $props['title']);
    self::assertSame('Khác biệt hoàn toàn với các gói nội thất đại trà giá rẻ do admin ghi chú riêng', $props['description']);
    self::assertSame('GÓI BESPOKE LUXURY ĐỒNG BỘ — LỰA CHỌN HÀNG ĐẦU TẠI OCEAN PARK', $props['featuredPackage']['badge']);
    self::assertSame('Hoàn Thiện Nội Thất Trọn Gói Cao Cấp (Từ 120 triệu/căn • Phổ biến 180 – 260 triệu/căn)', $props['featuredPackage']['title']);
    self::assertSame('Gói thi công bao gồm', $props['commitments'][0]['title']);
    self::assertSame('Item 1', $props['commitments'][0]['items'][0]['label']);
  }

  public function testBuildsEditorSampleWhenEmpty(): void
  {
    $empty_props = eai_ocean_park_pricing_get_rc_props([]);
    self::assertTrue(eai_ocean_park_pricing_props_are_empty($empty_props));

    $sample = eai_ocean_park_pricing_get_editor_sample_props(['class_name' => ' pricing-sample ']);
    self::assertFalse(eai_ocean_park_pricing_props_are_empty($sample));
    self::assertSame('pricing-sample', $sample['className']);
    self::assertNotEmpty($sample['unitEstimates']);
    self::assertNotEmpty($sample['roomEstimates']);
    self::assertNotEmpty($sample['commitments']);
  }

  public function testSourceWiringRegistersWidgetHelperAndTemplate(): void
  {
    $root = dirname(__DIR__);
    $widget = file_get_contents($root . '/includes/widgets/EAI-ocean-park-pricing.php');
    $template = file_get_contents($root . '/includes/templates/EAI-ocean-park-pricing.php');
    $helpers = file_get_contents($root . '/includes/helpers/bootstrap.php');
    $plugin = file_get_contents($root . '/includes/plugin.php');

    self::assertIsString($widget);
    self::assertStringContainsString("eai_rc_render_html('OceanParkPricing', \$props)", $widget);
    self::assertIsString($template);
    self::assertStringContainsString('echo $html;', $template);
    self::assertStringNotContainsString('wp_kses_post', $template);
    self::assertStringContainsString("require_once __DIR__ . '/ocean-park-pricing.php';", (string) $helpers);
    self::assertStringContainsString("widgets/EAI-ocean-park-pricing.php", (string) $plugin);
    self::assertStringContainsString('new \\EAI_Ocean_Park_Pricing_Widget()', (string) $plugin);
  }
}
