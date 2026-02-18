<?php

namespace Automad\Blocks\Utils;

use Automad\Test\Block;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class GalleryTest extends TestCase {
	public static function dataForTestSearchAndReplaceIsSame() {
		return array(
			array(
				'[a-z]+\-1',
				'picture-1',
				true,
				false,
				<<< JSON
				{
					"id": "1",
					"type": "gallery",
					"data": {
						"files": [
							"test-image-1.png",
							"test-image-2.png"
						],
						"layout": "columns",
						"columnWidthPx": 250,
						"rowHeightPx": 250,
						"gapPx": 5,
						"fillRectangle": false
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
					"type": "gallery",
					"data": {
						"files": [
							"test-picture-1.png",
							"test-image-2.png"
						],
						"layout": "columns",
						"columnWidthPx": 250,
						"rowHeightPx": 250,
						"gapPx": 5,
						"fillRectangle": false
					},
					"tunes": {
						"layout": {
							"stretched": false,
							"width": null
						}
					}
				}
				JSON,
				'test-image-1.png test-image-2.png'
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
			'Gallery',
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
