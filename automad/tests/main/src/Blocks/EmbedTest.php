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
}
