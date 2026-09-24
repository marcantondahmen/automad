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

	public static function dataForTestToAgentIsSame() {
		return array(
			array(
				<<< JSON
				{
					"id": "1",
					"type": "buttons",
					"data": {
						"gap": "1rem",
						"justify": "start",
						"primaryLink": "https://domain.com",
						"primaryOpenInNewTab": true,
						"primaryText": "First Button",
						"secondaryOpenInNewTab": true
					},
					"tunes": []
				}
				JSON,
				<<< JSON
				{
					"id": "1",
					"type": "buttons",
					"data.primaryLink": "https://domain.com",
					"data.primaryText": "First Button"
				}
				JSON
			),
			array(
				<<< JSON
				{
					"id": "2",
					"type": "buttons",
					"data": {
						"gap": "1rem",
						"justify": "start",
						"primaryLink": "https://domain.com",
						"primaryOpenInNewTab": true,
						"primaryText": "First Button",
						"secondaryLink": "https://domain.com",
						"secondaryOpenInNewTab": true,
						"secondaryText": "Second Button"
					},
					"tunes": []
				}
				JSON,
				<<< JSON
				{
					"id": "2",
					"type": "buttons",
					"data.primaryLink": "https://domain.com",
					"data.primaryText": "First Button",
					"data.secondaryLink": "https://domain.com",
					"data.secondaryText": "Second Button"
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

	#[DataProvider('dataForTestToAgentIsSame')]
	public function testToAgentIsSame(
		string $blockJson,
		string $expectedAgentJson
	) {
		Block::testToAgent($this, 'Buttons', $blockJson, $expectedAgentJson);
	}
}
