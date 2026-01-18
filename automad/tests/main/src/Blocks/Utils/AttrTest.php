<?php

namespace Automad\Blocks\Utils;

use Automad\Core\Blocks;
use Automad\Test\Mock;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class AttrTest extends TestCase {
	public static function dataForTestUniqueIdsIsSame() {
		return array(
			array(
				json_decode(<<< JSON
					{
						"blocks": [
							{
								"id": "1",
								"type": "header",
								"data": {
									"text": "Headline",
									"level": 2
								},
								"tunes": {
									"id": ""
								}
							},
							{
								"id": "2",
								"type": "paragraph",
								"data": {
									"text": "Text",
									"large": false
								},
								"tunes": {
									"id": ""
								}
							},
							{
								"id": "3",
								"type": "section",
								"data": {
									"content": {
										"blocks": [
											{
												"id": "4",
												"type": "header",
												"data": {
													"text": "Headline",
													"level": 2
												},
												"tunes": {
													"id": ""
												}
											},
											{
												"id": "5",
												"type": "paragraph",
												"data": {
													"text": "Text",
													"large": false
												},
												"tunes": {
													"id": ""
												}
											}
										]
									},
									"style": [],
									"justify": "start",
									"align": "normal",
									"gap": "",
									"minBlockWidth": ""
								},
								"tunes": {
									"id": ""
								}
							},
							{
								"id": "6",
								"type": "header",
								"data": {
									"text": "Headline",
									"level": 2
								},
								"tunes": {
									"id": ""
								}
							},
							{
								"id": "7",
								"type": "header",
								"data": {
									"text": "Headline",
									"level": 2
								},
								"tunes": {
									"id": "custom-id"
								}
							},
							{
								"id": "8",
								"type": "header",
								"data": {
									"text": "Custom",
									"level": 2
								},
								"tunes": {
									"id": "headline"
								}
							},
							{
								"id": "9",
								"type": "paragraph",
								"data": {
									"text": "Text",
									"large": false
								},
								"tunes": {
									"id": "custom-id"
								}
							}
						]
					}
					JSON, true),
				<<< HTML
					<h2 id="headline" class="am-block">Headline</h2>
					<p class="am-block">Text</p>	
					<section class="am-block">
						<am-layout-section class="am-justify-start am-align-normal" style="border-width: 0px;">
							<h2 id="headline-1" class="am-block">Headline</h2>
							<p class="am-block">Text</p>
						</am-layout-section>
					</section>
					<h2 id="headline-2" class="am-block">Headline</h2>
					<h2 id="custom-id" class="am-block">Headline</h2>
					<h2 id="headline-3" class="am-block">Custom</h2>
					<p id="custom-id-1" class="am-block">Text</p>
					HTML
			)
		);
	}

	#[DataProvider('dataForTestUniqueIdsIsSame')]
	public function testUniqueIdsIsSame(array $data, string $html) {
		$Mock = new Mock();

		$actual = preg_replace('/\>\s+\</', '><', Blocks::render($data, $Mock->createAutomad()));
		$expected = preg_replace('/\>\s+\</', '><', $html);

		/** @disregard */
		$this->assertSame($expected, $actual);
	}
}
