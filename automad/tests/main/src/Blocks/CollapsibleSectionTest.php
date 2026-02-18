<?php

namespace Automad\Blocks;

use Automad\Test\Block;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class CollapsibleSectionTest extends TestCase {
	public static function dataForTestSearchAndReplaceIsSame() {
		return array(
			array(
				'Collapsible',
				'Test',
				false,
				true,
				<<< JSON
				{
					"id": "1",
					"type": "collapsibleSection",
					"data": {
						"title": "Collapsible Section Title",
						"content": {
							"blocks": [
								{
									"id": "2",
									"type": "layoutSection",
									"data": {
										"content": {
											"blocks": [
												{
													"id": "3",
													"type": "paragraph",
													"data": {
														"text": "Collapsible test content inside a layout section.",
														"large": false
													},
													"tunes": {
														"layout": {
															"stretched": false,
															"width": null
														},
														"spacing": null,
														"className": "",
														"id": ""
													}
												}
											]
										},
										"style": [],
										"justify": "start",
										"align": "start"
									},
									"tunes": {
										"layout": {
											"stretched": false,
											"width": null
										},
										"spacing": null,
										"className": "",
										"id": ""
									}
								}
							]
						},
						"group": "Test",
						"collapsed": false
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
					"type": "collapsibleSection",
					"data": {
						"title": "Test Section Title",
						"content": {
							"blocks": [
								{
									"id": "2",
									"type": "layoutSection",
									"data": {
										"content": {
											"blocks": [
												{
													"id": "3",
													"type": "paragraph",
													"data": {
														"text": "Test test content inside a layout section.",
														"large": false
													},
													"tunes": {
														"layout": {
															"stretched": false,
															"width": null
														},
														"spacing": null,
														"className": "",
														"id": ""
													}
												}
											]
										},
										"style": [],
										"justify": "start",
										"align": "start"
									},
									"tunes": {
										"layout": {
											"stretched": false,
											"width": null
										},
										"spacing": null,
										"className": "",
										"id": ""
									}
								}
							]
						},
						"group": "Test",
						"collapsed": false
					},
					"tunes": {
						"layout": {
							"stretched": false,
							"width": null
						}
					}
				}
				JSON,
				'Collapsible Section Title Collapsible test content inside a layout section.'
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
			'CollapsibleSection',
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
