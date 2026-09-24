<?php

namespace Automad\Blocks\Utils;

use Automad\Test\Block;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class TeXTest extends TestCase {
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
					"type": "teX",
					"data": {
						"code": "test"
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
					"type": "teX",
					"data": {
						"code": "replaced"
					},
					"tunes": {
						"layout": {
							"stretched": false,
							"width": null
						}
					}
				}
				JSON,
				'test'
			)
		);
	}

	public static function dataForTestToAgentIsSame() {
		return array(
			array(
				<<< JSON
				{
					"id": "1",
					"type": "teX",
					"data": {
						"code": "tex"
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
					"type": "teX",
					"stretched": true,
					"data.code": "tex"
				}
				JSON,
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
			'TeX',
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
		Block::testToAgent($this, 'TeX', $blockJson, $expectedAgentJson);
	}
}
