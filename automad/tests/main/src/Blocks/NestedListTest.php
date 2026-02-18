<?php

namespace Automad\Blocks;

use Automad\Test\Block;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class NestedListTest extends TestCase {
	public static function dataForTestSearchAndReplaceIsSame() {
		return array(
			array(
				'te.t',
				'replaced',
				true,
				false,
				<<< JSON
				{
					"id": "1",
					"type": "nestedList",
					"data": {
						"style": "unordered",
						"items": [
							{
								"content": "First level item",
								"items": [
									{
										"content": "Second level test item",
										"items": []
									},
									{
										"content": "Another one",
										"items": []
									}
								]
							},
							{
								"content": "Another first level test item",
								"items": []
							}
						]
					},
					"tunes": []
				}
				JSON,
				<<< JSON
				{
					"id": "1",
					"type": "nestedList",
					"data": {
						"style": "unordered",
						"items": [
							{
								"content": "First level item",
								"items": [
									{
										"content": "Second level replaced item",
										"items": []
									},
									{
										"content": "Another one",
										"items": []
									}
								]
							},
							{
								"content": "Another first level replaced item",
								"items": []
							}
						]
					},
					"tunes": []
				}
				JSON,
				'First level item Second level test item Another one Another first level test item'
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
			'NestedList',
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
