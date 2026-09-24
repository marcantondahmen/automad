<?php

namespace Automad\Blocks;

use Automad\Test\Block;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class HeaderTest extends TestCase {
	public static function dataForTestSearchAndReplaceIsSame() {
		return array(
			array(
				'Test',
				'Replaced',
				false,
				true,
				<<< JSON
				{
					"id": "1",
					"type": "header",
					"data": {
						"text": "Test Header",
						"level": 2
					},
					"tunes": []
				}
				JSON,
				<<< JSON
				{
					"id": "1",
					"type": "header",
					"data": {
						"text": "Replaced Header",
						"level": 2
					},
					"tunes": []
				}
				JSON,
				'Test Header'
			)
		);
	}

	public static function dataForTestToAgentIsSame() {
		return array(
			array(
				<<< JSON
				{
					"id": "1",
					"type": "header",
					"data": {
						"level": 2,
						"text": "Heading text"
					},
					"tunes": []
				}
				JSON,
				<<< JSON
				{
					"id": "1",
					"type": "header",
					"data.level": 2,
					"data.text": "Heading text"
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
			'Header',
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
		Block::testToAgent($this, 'Header', $blockJson, $expectedAgentJson);
	}
}
