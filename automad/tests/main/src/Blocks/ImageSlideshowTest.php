<?php

namespace Automad\Blocks\Utils;

use Automad\Test\Block;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ImageSlideshowTest extends TestCase {
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
					"type": "imageSlideshow",
					"data": {
						"files": [
							"test-image-1.png",
							"test-image-2.png"
						],
						"imageWidthPx": 1200,
						"imageHeightPx": 780,
						"gapPx": 0,
						"slidesPerView": 1,
						"loop": true,
						"autoplay": false,
						"effect": "slide",
						"hideControls": false,
						"delay": 3000,
						"breakpoints": {
							"600": {
								"slidesPerView": 2
							},
							"900": {
								"slidesPerView": 3
							}
						}
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
					"type": "imageSlideshow",
					"data": {
						"files": [
							"test-picture-1.png",
							"test-image-2.png"
						],
						"imageWidthPx": 1200,
						"imageHeightPx": 780,
						"gapPx": 0,
						"slidesPerView": 1,
						"loop": true,
						"autoplay": false,
						"effect": "slide",
						"hideControls": false,
						"delay": 3000,
						"breakpoints": {
							"600": {
								"slidesPerView": 2
							},
							"900": {
								"slidesPerView": 3
							}
						}
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
					"type": "imageSlideshow",
					"data": {
						"autoplay": false,
						"breakpoints": {
							"600": {
								"slidesPerView": 2
							},
							"900": {
								"slidesPerView": 3
							}
						},
						"delay": 3000,
						"effect": "slide",
						"files": [
							"image-1.png",
							"image-2.png",
							"image-3.png"
						],
						"gapPx": 0,
						"hideControls": false,
						"imageHeightPx": 780,
						"imageWidthPx": 1200,
						"loop": true,
						"slidesPerView": 1
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
					"id": "1",
					"type": "imageSlideshow",
					"stretched": true,
					"data.files": [
						"image-1.png",
						"image-2.png",
						"image-3.png"
					],
					"data.imageHeightPx": 780,
					"data.imageWidthPx": 1200
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
			'ImageSlideshow',
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
		Block::testToAgent($this, 'ImageSlideshow', $blockJson, $expectedAgentJson);
	}
}
