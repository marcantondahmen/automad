<?php

namespace Automad\Blocks;

use Automad\Test\Block;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class TableTest extends TestCase {
	public static function dataForTestSearchAndReplaceIsSame() {
		return array(
			array(
				'\b(te.t)\b',
				'replaced [$1]',
				true,
				false,
				<<< JSON
				{
					"id": "1",
					"type": "table",
					"data": {
						"withHeadings": false,
						"content": [
							[
								"Test content",
								"test content"
							],
							[
								"tEsT content",
								"tE-t content"
							]
						]
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
					"type": "table",
					"data": {
						"withHeadings": false,
						"content": [
							[
								"replaced [Test] content",
								"replaced [test] content"
							],
							[
								"replaced [tEsT] content",
								"replaced [tE-t] content"
							]
						]
					},
					"tunes": {
						"layout": {
							"stretched": false,
							"width": null
						}
					}
				}
				JSON,
				'Test content test content tEsT content tE-t content'
			)
		);
	}

	public static function dataForTestToAgentIsSame() {
		return array(
			array(
				<<< JSON
				{
					"id": "1",
					"type": "table",
					"data": {
						"withHeadings": false,
						"content": [
							[
								"header a",
								"header b"
							],
							[
								"row a",
								"row b"
							]
						]
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
					"type": "table",
					"stretched": true,
					"data.content": [
						[
							"header a",
							"header b"
						],
						[
							"row a",
							"row b"
						]
					]
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
			'Table',
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
		Block::testToAgent($this, 'Table', $blockJson, $expectedAgentJson);
	}
}
