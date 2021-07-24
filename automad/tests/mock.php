<?php

namespace Automad\Tests;

use Automad\Core\Context;
use Automad\Core\Page;
use Automad\Core\Shared;
use PHPUnit\Framework\TestCase;

defined('AUTOMAD') or die('Direct access not permitted!');


class Mock extends TestCase {
	
		
	/**
	 *	Create a mock of the Automad object with a single page.
	 * 	A template can be passed optionally to the page.
	 *
	 *	@param string $template
	 *	@return object The Automad Mock
	 */
		
	public function createAutomad($template = '') {
		
		$Shared = new Shared();
		$Shared->data['text'] = 'Shared default text content'; 
		$collection = $this->createCollection($Shared, $template);
		$methods = array_diff(
			get_class_methods('\Automad\Core\Automad'), 
			array(
				'getPage',
				'getRequestedPage',
				'getFilelist',
				'getPagelist',
				'loadTemplate'
			)
		);
	
		$AutomadMock = $this->getMockBuilder('\Automad\Core\Automad')
							->setMethods($methods)
							->disableOriginalConstructor()
							->getMock();
							
		$AutomadMock->method('getCollection')->willReturn($collection);
		$AutomadMock->Shared = $Shared;
		$AutomadMock->Context = new Context($collection['/']);
		
		return $AutomadMock;
		
	}


	/**
	 *	Create a collection of test pages.
	 *
	 *	@param \Automad\Core\Shared $Shared
	 *	@param string $template
	 *	@return array the collection
	 */

	private function createCollection($Shared, $template) {

		return array(
			'/' => new Page(
				array(
					'title' => 'Test',
					'url' => '/page',
					':path' => '/01.page/',
					':origUrl' => '/page',
					'theme' => '../automad/tests/templates',
					':template' => $template,
					'date' => '2018-07-21 12:00:00',
					'test' => 'Test String',
					'quoted' => '"Quoted" "Test" "String"',
					'x' => '10',
					'image' => 'image.jpg',
					'link' => 'test'
				),
				$Shared
			),
			'/text-search' => new Page(
				array(
					'title' => 'Text Search',
					'url' => '/text-search',
					':path' => '/01.text-search/',
					':origUrl' => '/text-search',
					'theme' => '../automad/tests/templates',
					':template' => $template,
					'text' => 'find this lower case string',
				),
				$Shared
			),
			'/block-search' => new Page(
				array(
					'title' => 'Block Search',
					'url' => '/block-search',
					':path' => '/01.block-search/',
					':origUrl' => '/block-search',
					'theme' => '../automad/tests/templates',
					':template' => $template,
					'+main' => <<< JSON
						{
							"time": 1627118722514,
							"blocks": [
								{
									"type": "header",
									"data": {
										"text": "Find this String",
										"level": 2,
										"alignment": "left"
									}
								},
								{
									"type": "section",
									"data": {
										"content": {
											"time": 1627118702124,
											"blocks": [
												{
													"type": "paragraph",
													"data": {
														"text": "Find this String",
														"large": false,
														"alignment": "left"
													}
												}
											],
											"version": "2.20.2"
										},
										"style": {},
										"justify": "start",
										"gap": "",
										"minBlockWidth": ""
									}
								}
							],
							"version": "2.20.2"
						}
JSON
				),
				$Shared
			)
		);

	}
	
	
}