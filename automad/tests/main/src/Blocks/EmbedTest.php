<?php

namespace Automad\Blocks\Utils;

use Automad\Test\Block;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class EmbedTest extends TestCase {
	public static function dataForTestSearchAndReplaceIsSame() {
		return array(
			array(
				'caption test',
				'test caption',
				false,
				true,
				<<< JSON
				{
					"id": "1",
					"type": "embed",
					"data": {
						"service": "youtube",
						"source": "https://www.youtube.com/watch?v=jMyfnN_gu5w",
						"embed": "https://www.youtube.com/embed/jMyfnN_gu5w",
						"width": 16,
						"height": 9,
						"caption": "Video caption test"
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
					"type": "embed",
					"data": {
						"service": "youtube",
						"source": "https://www.youtube.com/watch?v=jMyfnN_gu5w",
						"embed": "https://www.youtube.com/embed/jMyfnN_gu5w",
						"width": 16,
						"height": 9,
						"caption": "Video test caption"
					},
					"tunes": {
						"layout": {
							"stretched": false,
							"width": null
						}
					}
				}
				JSON,
				'Video caption test'
			),
			array(
				'caption test',
				'test caption',
				false,
				true,
				<<< JSON
				{
					"id": "2",
					"type": "embed",
					"data": {
						"source": "https://www.youtube.com/watch?v=jMyfnN_gu5w",
						"caption": "Video caption test"
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
					"id": "2",
					"type": "embed",
					"data": {
						"source": "https://www.youtube.com/watch?v=jMyfnN_gu5w",
						"caption": "Video test caption"
					},
					"tunes": {
						"layout": {
							"stretched": false,
							"width": null
						}
					}
				}
				JSON,
				'Video caption test'
			)
		);
	}

	public static function dataForTestToAgentIsSame() {
		return array(
			array(
				<<< JSON
				{
					"id": "1",
					"type": "embed",
					"data": {
						"source": "https://www.youtube.com/watch?v=abcdef",
						"caption": "Some caption text"
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
					"type": "embed",
					"stretched": true,
					"data.source": "https://www.youtube.com/watch?v=abcdef",
					"data.caption": "Some caption text"
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
			'Embed',
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
		Block::testToAgent($this, 'Embed', $blockJson, $expectedAgentJson);
	}
}
