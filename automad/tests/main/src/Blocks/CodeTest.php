<?php

namespace Automad\Blocks\Utils;

use Automad\Test\Block;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class CodeTest extends TestCase {
	public static function dataForTestSearchAndReplaceIsSame() {
		return array(
			array(
				'Test',
				'Some',
				false,
				true,
				<<< JSON
				{
					"id": "1",
					"type": "code",
					"data": {
						"code": "<?php new Instance('Test string');",
						"language": "php"
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
					"type": "code",
					"data": {
						"code": "<?php new Instance('Some string');",
						"language": "php"
					},
					"tunes": {
						"layout": {
							"stretched": false,
							"width": null
						}
					}
				}
				JSON,
				"<?php new Instance('Test string');"
			)
		);
	}

	public static function dataForTestToAgentIsSame() {
		return array(
			array(
				<<< JSON
				{
					"id": "1",
					"type": "code",
					"data": {
						"code": "Some code",
						"language": "php",
						"lineNumbers": true
					},
					"tunes": []
				}
				JSON,
				<<< JSON
				{
					"id": "1",
					"type": "code",
					"data.code": "Some code",
					"data.language": "php",
					"data.lineNumbers": true
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
			'Code',
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
		Block::testToAgent($this, 'Code', $blockJson, $expectedAgentJson);
	}
}
