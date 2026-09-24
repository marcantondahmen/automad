<?php

namespace Automad\Blocks;

use Automad\Test\Block;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ImageTest extends TestCase {
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
					"type": "image",
					"data": {
						"url": "test-image.png",
						"alt": "test alt",
						"link": "",
						"openInNewTab": false,
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
					"type": "image",
					"data": {
						"url": "replaced-image.png",
						"alt": "replaced alt",
						"link": "",
						"openInNewTab": false,
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
				'test-image.png test alt test caption'
			)
		);
	}

	public static function dataForTestToAgentIsSame() {
		return array(
			array(
				<<< JSON
				{
					"id": "1",
					"type": "image",
					"data": {
						"alt": "Alt text",
						"url": "image.webp"
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
					"type": "image",
					"stretched": true,
					"data.alt": "Alt text",
					"data.url": "image.webp"
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
			'Image',
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
		Block::testToAgent($this, 'Image', $blockJson, $expectedAgentJson);
	}
}
