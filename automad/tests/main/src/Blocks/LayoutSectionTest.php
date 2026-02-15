<?php

namespace Automad\Blocks;

use Automad\Test\Block;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class LayoutSectionTest extends TestCase {
	public static function dataForTestSearchAndReplaceIsSame() {
		return array(
			array(
				'test',
				'replaced',
				false,
				false,
				<<< JSON
				{
					"id": "1",
					"type": "layoutSection",
					"data": {
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
														"text": "Nested test text",
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
											"width": "1/2"
										},
										"spacing": null,
										"className": "",
										"id": ""
									}
								},
								{
									"id": "4",
									"type": "layoutSection",
									"data": {
										"content": {
											"blocks": [
												{
													"id": "5",
													"type": "paragraph",
													"data": {
														"text": "More nested test text",
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
											"width": "1/2"
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
						}
					}
				}
				JSON,
				<<< JSON
				{
					"id": "1",
					"type": "layoutSection",
					"data": {
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
														"text": "Nested replaced text",
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
											"width": "1/2"
										},
										"spacing": null,
										"className": "",
										"id": ""
									}
								},
								{
									"id": "4",
									"type": "layoutSection",
									"data": {
										"content": {
											"blocks": [
												{
													"id": "5",
													"type": "paragraph",
													"data": {
														"text": "More nested replaced text",
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
											"width": "1/2"
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
						}
					}
				}
				JSON,
				'Nested test text More nested test text'
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
			'LayoutSection',
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
