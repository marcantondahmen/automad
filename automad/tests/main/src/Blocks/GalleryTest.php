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

	public static function dataForTestToAgentIsSame() {
		return array(
			array(
				<<< JSON
				{
					"id": "1",
					"type": "gallery",
					"data": {
						"columnWidthPx": 250,
						"files": [
							"image-1.png",
							"image-2.png"
						],
						"fillRectangle": false,
						"gapPx": 5,
						"layout": "rows",
						"rowHeightPx": 250
					},
					"tunes": {
						"layout": {
							"stretched": false
						}
					}
				}
				JSON,
				<<< JSON
				{
					"id": "1",
					"type": "gallery",
					"data.columnWidthPx": 250,
					"data.files": [
						"image-1.png",
						"image-2.png"
					],
					"data.layout": "rows",
					"data.rowHeightPx": 250
				}
				JSON
			),
			array(
				<<< JSON
				{
					"id": "2",
					"type": "gallery",
					"data": {
						"columnWidthPx": 250,
						"files": [
							"image-1.png",
							"image-2.png"
						],
						"fillRectangle": false,
						"gapPx": 5,
						"layout": "columns",
						"rowHeightPx": 250
					},
					"tunes": {
						"layout": {
							"stretched": true
						}
					}
				}
				JSON,
				<<< JSON
				{
					"id": "2",
					"type": "gallery",
					"stretched": true,
					"data.columnWidthPx": 250,
					"data.files": [
						"image-1.png",
						"image-2.png"
					],
					"data.layout": "columns",
					"data.rowHeightPx": 250
				}
				JSON
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
		Block::testSearchReplace(
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

	#[DataProvider('dataForTestToAgentIsSame')]
	public function testToAgentIsSame(
		string $blockJson,
		string $expectedAgentJson
	) {
		Block::testToAgent($this, 'Gallery', $blockJson, $expectedAgentJson);
	}
}
