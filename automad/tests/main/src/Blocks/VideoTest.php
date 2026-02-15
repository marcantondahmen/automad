<?php

namespace Automad\Blocks;

use Automad\Test\Block;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class VideoTest extends TestCase {
	public static function dataForTestSearchAndReplaceIsSame() {
		return array(
			array(
				'test',
				'replaced',
				false,
				true,
				<<< JSON
				{
					"id": "1",
					"type": "video",
					"data": {
						"url": "test-video.mp4",
						"autoplay": false,
						"loop": false,
						"muted": false,
						"controls": true,
						"caption": "test caption"
					},
					"tunes": {
						"layout": {
							"stretched": false,
							"width": null
						}
					}
				}
				JSON,
				<<< JSON
				{
					"id": "1",
					"type": "video",
					"data": {
						"url": "replaced-video.mp4",
						"autoplay": false,
						"loop": false,
						"muted": false,
						"controls": true,
						"caption": "replaced caption"
					},
					"tunes": {
						"layout": {
							"stretched": false,
							"width": null
						}
					}
				}
				JSON,
				'test-video.mp4 test caption'
			)
		);
	}

	#[DataProvider('dataForTestSearchAndReplaceIsSame')]
	public function testSearchAndReplaceIsSame(
		string $search,
		string $replace,
		bool $isRegex,
		bool $isCaseInsensitive,
		string $blockJson,
		string $expectedReplacedJson,
		string $expectedString
	) {
		Block::test(
			$this,
			'Video',
			$search,
			$replace,
			$isRegex,
			$isCaseInsensitive,
			$blockJson,
			$expectedReplacedJson,
			$expectedString
		);
	}
}
