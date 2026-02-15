<?php

namespace Automad\Blocks;

use Automad\Test\Block;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class CalloutTest extends TestCase {
	public static function dataForTestSearchAndReplaceIsSame() {
		return array(
			array(
				'important',
				'test',
				false,
				false,
				<<< JSON
				{
					"id": "1",
					"type": "callout",
					"data": {
						"title": "Important",
						"text": "Some important callout text is here."
					},
					"tunes": []
				}
				JSON,
				<<< JSON
				{
					"id": "1",
					"type": "callout",
					"data": {
						"title": "test",
						"text": "Some test callout text is here."
					},
					"tunes": []
				}
				JSON,
				'Important Some important callout text is here.'
			),
			array(
				'important',
				'test',
				false,
				true,
				<<< JSON
				{
					"id": "1",
					"type": "callout",
					"data": {
						"title": "Important",
						"text": "Some important callout text is here."
					},
					"tunes": []
				}
				JSON,
				<<< JSON
				{
					"id": "1",
					"type": "callout",
					"data": {
						"title": "Important",
						"text": "Some test callout text is here."
					},
					"tunes": []
				}
				JSON,
				'Important Some important callout text is here.'
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
			'Callout',
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
