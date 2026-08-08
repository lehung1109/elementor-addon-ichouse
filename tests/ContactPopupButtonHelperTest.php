<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__) . '/includes/helpers/contact-popup-button.php';

final class ContactPopupButtonHelperTest extends TestCase
{
  public function testMapsCompleteSettingsToReactProps(): void
  {
    $props = eai_contact_popup_button_get_rc_props([
      'class_name' => 'custom-button',
      'button_label' => 'Trở thành đối tác',
      'popup_target' => ' Tu Van ',
    ]);

    self::assertSame('custom-button', $props['className']);
    self::assertSame('Trở thành đối tác', $props['buttonLabel']);
    self::assertSame('tu-van', $props['popupTarget']);
  }

  public function testOmitsBlankClassNameAndPopupTargetWhenBlank(): void
  {
    $props = eai_contact_popup_button_get_rc_props([
      'class_name' => '   ',
      'button_label' => 'Trở thành đối tác',
      'popup_target' => '  ',
    ]);

    self::assertArrayNotHasKey('className', $props);
    self::assertArrayNotHasKey('popupTarget', $props);
  }

  public function testDetectsEmptyProps(): void
  {
    $empty = eai_contact_popup_button_get_rc_props([]);
    $no_target = eai_contact_popup_button_get_rc_props(['button_label' => 'Nút']);
    $complete = eai_contact_popup_button_get_rc_props([
      'button_label' => 'Nút',
      'popup_target' => 'tu-van',
    ]);

    self::assertTrue(eai_contact_popup_button_props_are_empty($empty));
    self::assertTrue(eai_contact_popup_button_props_are_empty($no_target));
    self::assertFalse(eai_contact_popup_button_props_are_empty($complete));
  }

  public function testEditorSampleMirrorsCanonicalFixtureAndPreservesSettings(): void
  {
    $props = eai_contact_popup_button_get_editor_sample_props([
      'class_name' => 'editor-class',
    ]);

    self::assertSame('editor-class', $props['className']);
    self::assertSame('TRỞ THÀNH ĐỐI TÁC ICHOUSE!', $props['buttonLabel']);
    self::assertSame('tu-van', $props['popupTarget']);
  }
}
