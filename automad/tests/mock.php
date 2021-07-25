<?php

namespace Automad\Tests;

use Automad\Core\Context;
use Automad\Core\Page;
use Automad\Core\Parse;
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
		$Shared->data['shared'] = 'Shared default text content'; 
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
		$AutomadMock->Context = new Context($collection['/page']);
		
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

		$theme = '../automad/tests/templates';

		return array(
			'/' => new Page(
				array(
					'title' => 'Home',
					'url' => '/',
					':path' => '/',
					':origUrl' => '/',
					'theme' => $theme,
					':template' => $template
				),
				$Shared
			),
			'/page' => new Page(
				array_merge(
					array(
						'url' => '/page',
						':path' => '/01.page/',
						':origUrl' => '/page',
						'theme' => $theme,
						':template' => $template
					),
					Parse::textFile(__DIR__ . '/data/page.txt')
				),
				$Shared
			),
			'/text' => new Page(
				array_merge(
					array(
						'url' => '/text',
						':path' => '/01.text/',
						':origUrl' => '/text',
						'theme' => $theme,
						':template' => $template,
					),
					Parse::textFile(__DIR__ . '/data/text.txt')
				),
				$Shared
			),
			'/blocks' => new Page(
				array_merge(
					array(
						'url' => '/blocks',
						':path' => '/01.blocks/',
						':origUrl' => '/blocks',
						'theme' => $theme,
						':template' => $template,
					),
					Parse::textFile(__DIR__ . '/data/blocks.txt')
				),
				$Shared
			)
		);

	}
	
	
}