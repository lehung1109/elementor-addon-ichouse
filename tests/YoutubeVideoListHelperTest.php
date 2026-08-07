<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class YoutubeVideoListHelperTest extends TestCase
{
  public function testExtractsVideoIdFromSupportedInputs(): void
  {
    self::assertSame('dQw4w9WgXcQ', eai_youtube_video_list_extract_video_id('dQw4w9WgXcQ'));
    self::assertSame('dQw4w9WgXcQ', eai_youtube_video_list_extract_video_id('https://www.youtube.com/watch?v=dQw4w9WgXcQ&t=10'));
    self::assertSame('dQw4w9WgXcQ', eai_youtube_video_list_extract_video_id('https://youtu.be/dQw4w9WgXcQ?si=test'));
    self::assertSame('dQw4w9WgXcQ', eai_youtube_video_list_extract_video_id('https://youtube.com/shorts/dQw4w9WgXcQ'));
    self::assertSame('dQw4w9WgXcQ', eai_youtube_video_list_extract_video_id('https://www.youtube.com/embed/dQw4w9WgXcQ'));
  }

  public function testRejectsUnsupportedOrInvalidInputs(): void
  {
    self::assertSame('', eai_youtube_video_list_extract_video_id(''));
    self::assertSame('', eai_youtube_video_list_extract_video_id('too-short'));
    self::assertSame('', eai_youtube_video_list_extract_video_id('https://example.com/watch?v=dQw4w9WgXcQ'));
    self::assertSame('', eai_youtube_video_list_extract_video_id('https://youtube.com/watch?v=invalid'));
  }

  public function testMapsValidRowsAndOmitsBlankTitles(): void
  {
    $items = eai_rc_map_youtube_video_list_items([
      ['youtube_video' => 'https://youtu.be/dQw4w9WgXcQ', 'title' => ' Video giới thiệu '],
      ['youtube_video' => '9bZkp7q19f0', 'title' => '   '],
      ['youtube_video' => 'invalid', 'title' => 'Bỏ qua'],
      'invalid-row',
    ]);

    self::assertSame([
      ['youtubeVideoId' => 'dQw4w9WgXcQ', 'title' => 'Video giới thiệu'],
      ['youtubeVideoId' => '9bZkp7q19f0'],
    ], $items);
  }

  public function testBuildsPropsWithOptionalClassNameAndUniqueTargetId(): void
  {
    $settings = [
      'class_name' => ' custom-videos ',
      'items' => [['youtube_video' => 'dQw4w9WgXcQ']],
    ];

    $first = eai_youtube_video_list_get_rc_props($settings, 'abc-123');
    $second = eai_youtube_video_list_get_rc_props($settings, 'xyz 456');

    self::assertSame('custom-videos', $first['className']);
    self::assertSame('youtube-video-list-abc-123', $first['scrollReveal']['targetId']);
    self::assertSame('youtube-video-list-xyz456', $second['scrollReveal']['targetId']);
  }

  public function testOmitsBlankClassNameAndUsesStableTargetFallback(): void
  {
    $props = eai_youtube_video_list_get_rc_props([
      'class_name' => '   ',
      'items' => [],
    ], '***');

    self::assertArrayNotHasKey('className', $props);
    self::assertSame([], $props['items']);
    self::assertSame('youtube-video-list-widget', $props['scrollReveal']['targetId']);
  }

  public function testEditorSampleMirrorsCanonicalFixtureAndPreservesClassName(): void
  {
    $props = eai_youtube_video_list_get_editor_sample_props([
      'class_name' => 'editor-videos',
    ], 'editor-1');

    self::assertSame('editor-videos', $props['className']);
    self::assertSame('youtube-video-list-editor-1', $props['scrollReveal']['targetId']);
    self::assertCount(3, $props['items']);
    self::assertSame('dQw4w9WgXcQ', $props['items'][0]['youtubeVideoId']);
    self::assertSame('Báo giá chi phí thiết kế nhà', $props['items'][0]['title']);
  }
}
