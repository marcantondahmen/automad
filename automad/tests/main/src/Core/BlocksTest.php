<?php

namespace Automad\Core;

use Automad\Test\Mock;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class BlocksTest extends TestCase {
	public static function dataForTestToAgentIsSame() {
		return array(
			array(
				<<< JSON
				[
					{
						"id": "1",
						"type": "paragraph",
						"data": {
							"text": "Test paragraph text",
							"large": false
						},
						"tunes": []
					},
					{
						"id": "2",
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
				]
				JSON,
				<<< JSON
				[
					{
						"id": "1",
						"type": "paragraph",
						"data.text": "Test paragraph text"
					},
					{
						"id": "2",
						"type": "buttons",
						"data.primaryLink": "https://domain.com",
						"data.primaryText": "First Button"
					}
				]
				JSON,
			),
		);
	}

	#[DataProvider('dataForTestToAgentIsSame')]
	public function testToAgentIsSame($blocksJson, $expectedToAgentJson) {
		$Mock = new Mock();
		$Automad = $Mock->createAutomad();

		/** @disregard */
		$this->assertSame(
			Blocks::toAgent(json_decode($blocksJson, true), $Automad->ComponentCollection),
			json_decode($expectedToAgentJson, true)
		);
	}
}
