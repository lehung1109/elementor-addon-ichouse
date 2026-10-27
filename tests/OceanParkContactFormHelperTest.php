<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class OceanParkContactFormHelperTest extends TestCase
{
  public function testMapsContactFormSettingsToOceanParkContactFormModel(): void
  {
    $props = eai_ocean_park_contact_form_get_rc_props([
      'class_name' => ' custom-contact ',
      'section_id' => ' dang-ky-tu-van ',
      'form_key' => ' Ocean Park Lead 01 ',
      'eyebrow' => ' ĐĂNG KÝ TƯ VẤN ',
      'title' => ' Nhận Bản Vẽ Mặt Bằng 2D ',
      'description' => ' KTS trưởng trực tiếp khảo sát ',
      'hotline' => ' 0869 855 885 ',
      'zalo_phone' => ' 0978 305 740 ',
      'email' => ' noithatichouse@gmail.com ',
      'website' => ' noithat.ichouse.vn ',
      'locations' => [
        ['label' => ' Nhà mẫu ', 'address' => ' Sao Biển 23, Vinhomes Ocean Park '],
        ['label' => '', 'address' => ''],
      ],
      'reference_links' => [
        ['label' => ' Nội thất The London ', 'url' => ' #du-an-mau-ocean-park '],
        ['label' => '', 'url' => ''],
      ],
      'form_title' => ' Đặt Lịch Tư Vấn 1:1 ',
      'form_subtitle' => ' Miễn phí 100% khảo sát ',
      'zone_options_text' => "The London Ocean Park 1\n\n The Beverly Ocean Park 1 ",
      'submit_label' => ' Nhận Bản Vẽ 3D & Báo Giá Chi Tiết ',
      'success_message' => ' Cảm ơn Quý cư dân! ',
    ], 'widget-99');

    self::assertSame('custom-contact', $props['className']);
    self::assertSame('dang-ky-tu-van', $props['sectionId']);
    self::assertSame('ocean-park-lead-01', $props['formKey']);
    self::assertSame('ĐĂNG KÝ TƯ VẤN', $props['eyebrow']);
    self::assertSame('0978 305 740', $props['hotline']);
    self::assertSame('0978 305 740', $props['zaloPhone']);
    self::assertSame('noithatichouse@gmail.com', $props['email']);
    self::assertSame('noithat.ichouse.vn', $props['website']);
    self::assertCount(1, $props['locations']);
    self::assertSame('Nhà mẫu', $props['locations'][0]['label']);
    self::assertCount(1, $props['referenceLinks']);
    self::assertSame('#du-an-mau-ocean-park', $props['referenceLinks'][0]['url']);
    self::assertSame('Đặt Lịch Tư Vấn 1:1', $props['formTitle']);
    self::assertSame(['The London Ocean Park 1', 'The Beverly Ocean Park 1'], $props['zoneOptions']);
    self::assertSame('Nhận Bản Vẽ 3D & Báo Giá Chi Tiết', $props['submitLabel']);
    self::assertSame('Cảm ơn Quý cư dân!', $props['successMessage']);
  }

  public function testPreservesUserEditedValuesWithoutHardcodedOverrides(): void
  {
    $props = eai_ocean_park_contact_form_get_rc_props([
      'eyebrow' => 'ĐĂNG KÝ TƯ VẤN & THAM QUAN NHÀ MẪU THỰC TẾ',
      'title' => 'Nhận Bản Vẽ Mặt Bằng 2D, Phối Cảnh 3D & Dự Toán Chi Tiết Theo Mã Căn Hộ Ocean Park',
      'description' => 'Đội ngũ Kiến trúc sư trưởng IC HOUSE trực tiếp khảo sát tại các tòa do admin chỉnh sửa',
      'locations' => [
        [
          'label' => 'Nhà mẫu nội thất trong khu đô thị Vinhomes Ocean Park',
          'address' => 'Địa chỉ nhà mẫu',
        ],
      ],
    ]);

    self::assertSame('ĐĂNG KÝ TƯ VẤN & THAM QUAN NHÀ MẪU THỰC TẾ', $props['eyebrow']);
    self::assertSame('Nhận Bản Vẽ Mặt Bằng 2D, Phối Cảnh 3D & Dự Toán Chi Tiết Theo Mã Căn Hộ Ocean Park', $props['title']);
    self::assertSame('Đội ngũ Kiến trúc sư trưởng IC HOUSE trực tiếp khảo sát tại các tòa do admin chỉnh sửa', $props['description']);
    self::assertSame('Nhà mẫu nội thất trong khu đô thị Vinhomes Ocean Park', $props['locations'][0]['label']);
  }

  public function testGeneratesUniqueFormKeyPerWidgetWhenDefaultOrBlank(): void
  {
    $blank = eai_ocean_park_contact_form_get_rc_props(['form_key' => ''], 'abc_12');
    self::assertSame('ocean-park-contact-abc-12', $blank['formKey']);

    $default_key = eai_ocean_park_contact_form_get_rc_props(['form_key' => 'ocean-park-consultation'], 'w2');
    self::assertSame('ocean-park-consultation-w2', $default_key['formKey']);
  }

  public function testSourceWiringUsesWrapperComponentAndCf7HiddenSourceContainer(): void
  {
    $root = dirname(__DIR__);
    $widget = file_get_contents($root . '/includes/widgets/EAI-ocean-park-contact-form.php');
    $template = file_get_contents($root . '/includes/templates/EAI-ocean-park-contact-form.php');
    $helpers = file_get_contents($root . '/includes/helpers/bootstrap.php');
    $plugin = file_get_contents($root . '/includes/plugin.php');

    self::assertIsString($widget);
    self::assertStringContainsString("eai_rc_render_html('OceanParkContactFormWrapper', \$props)", $widget);
    self::assertStringContainsString("'cf7_form_id'", $widget);
    self::assertIsString($template);
    self::assertStringContainsString('eai-ocean-park-cf7-source-', $template);
    self::assertStringContainsString('echo $html;', $template);
    self::assertStringNotContainsString('wp_kses_post', $template);
    self::assertStringContainsString("require_once __DIR__ . '/ocean-park-contact-form.php';", (string) $helpers);
    self::assertStringContainsString("widgets/EAI-ocean-park-contact-form.php", (string) $plugin);
    self::assertStringContainsString('new \\EAI_Ocean_Park_Contact_Form_Widget()', (string) $plugin);
  }
}
