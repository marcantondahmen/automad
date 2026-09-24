<?php

namespace Automad\Blocks;

use Automad\Test\Block;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ParagraphTest extends TestCase {
	public static function dataForTestSearchAndReplaceIsSame() {
		return array(
			array(
				'teSt ParAgraph',
				'Replaced paragraph',
				false,
				false,
				<<< JSON
				{
					"id": "1",
					"type": "paragraph",
					"data": {
						"text": "Test paragraph",
						"large": false
					},
					"tunes": []
				}
				JSON,
				<<< JSON
				{
					"id": "1",
					"type": "paragraph",
					"data": {
						"text": "Replaced paragraph",
						"large": false
					},
					"tunes": []
				}
				JSON,
				'Test paragraph'
			)
		);
	}

	public static function dataForTestToAgentIsSame() {
		return array(
			array(
				<<< JSON
				{
					"id": "1",
					"type": "paragraph",
					"data": {
						"large": false,
						"text": "Test paragraph text"
					},
					"tunes": []
				}
				JSON,
				<<< JSON
				{
					"id": "1",
					"type": "paragraph",
					"data.text": "Test paragraph text"
				}
				JSON,
			),
			array(
				<<< JSON
				{
					"id": "2",
					"type": "paragraph",
					"data": {
						"large": true,
						"text": "Test paragraph text"
					},
					"tunes": {
						"layout": {
							"stretched": false,
							"width": "1/2"
						}
					}
				}
				JSON,
				<<< JSON
				{
					"id": "2",
					"type": "paragraph",
					"width": "1/2",
					"data.large": true,
					"data.text": "Test paragraph text"
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
			'Paragraph',
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
		Block::testToAgent($this, 'Paragraph', $blockJson, $expectedAgentJson);
	}
}
