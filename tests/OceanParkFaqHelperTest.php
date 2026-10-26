<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class OceanParkFaqHelperTest extends TestCase
{
  public function testMapsFaqItemsKeywordPillsAndUniqueCheckboxIdPrefixToOceanParkFaqModel(): void
  {
    $props = eai_ocean_park_faq_get_rc_props([
      'class_name' => ' custom-faq ',
      'section_id' => ' ocean-park-faq-section ',
      'eyebrow' => ' GIẢI ĐÁP CHUYÊN SÂU ',
      'title' => ' Câu Hỏi Thường Gặp ',
      'description' => ' Tổng hợp kinh nghiệm thực chiến ',
      'items' => [
        [
          'question' => ' Chi phí thiết kế là bao nhiêu? ',
          'answer_html' => '<p>Từ 250.000đ – 350.000đ/m².</p>',
          'default_open' => 'yes',
        ],
        [
          'question' => '',
          'answer_html' => '',
        ],
      ],
      'keyword_group_title' => ' Từ khóa tìm kiếm phổ biến ',
      'keyword_pills' => [
        ['label' => ' Ocean Park 1 ', 'url' => ' #du-an-mau-ocean-park '],
        ['label' => '', 'url' => ''],
      ],
    ], 'faq 77');

    self::assertSame('custom-faq', $props['className']);
    self::assertSame('ocean-park-faq-section', $props['sectionId']);
    self::assertSame('eai-ocean-park-faq-faq77', $props['checkboxIdPrefix']);
    self::assertSame('GIẢI ĐÁP CHUYÊN SÂU', $props['eyebrow']);
    self::assertCount(1, $props['items']);
    self::assertSame('Chi phí thiết kế là bao nhiêu?', $props['items'][0]['question']);
    self::assertTrue($props['items'][0]['defaultOpen']);
    self::assertSame('Từ khóa tìm kiếm phổ biến', $props['keywordGroupTitle']);
    self::assertCount(1, $props['keywordPills']);
    self::assertSame('Ocean Park 1', $props['keywordPills'][0]['label']);
    self::assertSame('#du-an-mau-ocean-park', $props['keywordPills'][0]['url']);
  }

  public function testPreservesUserEditedValuesWithoutHardcodedOverrides(): void
  {
    $props = eai_ocean_park_faq_get_rc_props([
      'eyebrow' => 'GIẢI ĐÁP CHUYÊN SÂU TỪ KIẾN TRÚC SƯ TRƯỞNG IC HOUSE',
      'title' => 'Câu Hỏi Thường Gặp Khi Thiết Kế & Thi Công Nội Thất Cao Cấp Vinhomes Ocean Park 1 • 2 • 3',
      'items' => [
        [
          'question' => 'Cư dân có thể xem trực tiếp nhà mẫu nội thất và xưởng sản xuất của IC HOUSE tại Vinhomes Ocean Park không?',
          'answer_html' => '<p>Câu trả lời do admin biên tập riêng.</p>',
        ],
      ],
    ]);

    self::assertSame('GIẢI ĐÁP CHUYÊN SÂU TỪ KIẾN TRÚC SƯ TRƯỞNG IC HOUSE', $props['eyebrow']);
    self::assertSame('Câu Hỏi Thường Gặp Khi Thiết Kế & Thi Công Nội Thất Cao Cấp Vinhomes Ocean Park 1 • 2 • 3', $props['title']);
    self::assertSame('Cư dân có thể xem trực tiếp nhà mẫu nội thất và xưởng sản xuất của IC HOUSE tại Vinhomes Ocean Park không?', $props['items'][0]['question']);
    self::assertSame('<p>Câu trả lời do admin biên tập riêng.</p>', $props['items'][0]['answerHtml']);
  }

  public function testBuildsEditorSampleWhenFaqItemsAreEmpty(): void
  {
    $empty_props = eai_ocean_park_faq_get_rc_props([], 'w1');
    self::assertTrue(eai_ocean_park_faq_props_are_empty($empty_props));

    $sample = eai_ocean_park_faq_get_editor_sample_props(['class_name' => ' faq-sample '], 'w1');
    self::assertFalse(eai_ocean_park_faq_props_are_empty($sample));
    self::assertSame('faq-sample', $sample['className']);
    self::assertSame('eai-ocean-park-faq-w1', $sample['checkboxIdPrefix']);
    self::assertCount(8, $sample['items']);
    self::assertNotEmpty($sample['keywordPills']);
  }

  public function testSourceWiringRegistersWidgetHelperAndTemplate(): void
  {
    $root = dirname(__DIR__);
    $widget = file_get_contents($root . '/includes/widgets/EAI-ocean-park-faq.php');
    $template = file_get_contents($root . '/includes/templates/EAI-ocean-park-faq.php');
    $helpers = file_get_contents($root . '/includes/helpers/bootstrap.php');
    $plugin = file_get_contents($root . '/includes/plugin.php');

    self::assertIsString($widget);
    self::assertStringContainsString("eai_rc_render_html('OceanParkFaq', \$props)", $widget);
    self::assertIsString($template);
    self::assertStringContainsString('echo $html;', $template);
    self::assertStringNotContainsString('wp_kses_post', $template);
    self::assertStringContainsString("require_once __DIR__ . '/ocean-park-faq.php';", (string) $helpers);
    self::assertStringContainsString("widgets/EAI-ocean-park-faq.php", (string) $plugin);
    self::assertStringContainsString('new \\EAI_Ocean_Park_Faq_Widget()', (string) $plugin);
  }
}
