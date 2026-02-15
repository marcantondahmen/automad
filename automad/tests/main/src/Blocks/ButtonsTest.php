<?php

namespace Automad\Blocks;

use Automad\Test\Block;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ButtonsTest extends TestCase {
	public static function dataForTestSearchAndReplaceIsSame() {
		return array(
			array(
				'Button',
				'Cool Button',
				false,
				false,
				<<< JSON
				{
					"id": "1",
					"type": "buttons",
					"data": {
						"primaryText": "First Button",
						"primaryLink": "/url",
						"primaryStyle": [],
						"primaryOpenInNewTab": false,
						"secondaryText": "Second Button",
						"secondaryLink": "/url",
						"secondaryStyle": [],
						"secondaryOpenInNewTab": false,
						"justify": "start",
						"gap": "1rem"
					},
					"tunes": []
				}
				JSON,
				<<< JSON
				{
					"id": "1",
					"type": "buttons",
					"data": {
						"primaryText": "First Cool Button",
						"primaryLink": "/url",
						"primaryStyle": [],
						"primaryOpenInNewTab": false,
						"secondaryText": "Second Cool Button",
						"secondaryLink": "/url",
						"secondaryStyle": [],
						"secondaryOpenInNewTab": false,
						"justify": "start",
						"gap": "1rem"
					},
					"tunes": []
				}
				JSON,
				'First Button Second Button'
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
			'Buttons',
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
