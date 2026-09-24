<?php

namespace Automad\Blocks;

use Automad\Test\Block;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class QuoteTest extends TestCase {
	public static function dataForTestSearchAndReplaceIsSame() {
		return array(
			array(
				'Test',
				'Replaced',
				false,
				false,
				<<< JSON
				{
					"id": "1",
					"type": "quote",
					"data": {
						"text": "Test quote content",
						"caption": "Test author"
					},
					"tunes": []
				}
				JSON,
				<<< JSON
				{
					"id": "1",
					"type": "quote",
					"data": {
						"text": "Replaced quote content",
						"caption": "Replaced author"
					},
					"tunes": []
				}
				JSON,
				'Test quote content Test author'
			)
		);
	}

	public static function dataForTestToAgentIsSame() {
		return array(
			array(
				<<< JSON
				{
					"id": "1",
					"type": "quote",
					"data": {
						"caption": "caption",
						"text": "quote text"
					},
					"tunes": []
				}
				JSON,
				<<< JSON
				{
					"id": "1",
					"type": "quote",
					"data.text": "quote text",
					"data.caption": "caption"
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
			'Quote',
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
		Block::testToAgent($this, 'Quote', $blockJson, $expectedAgentJson);
	}
}
