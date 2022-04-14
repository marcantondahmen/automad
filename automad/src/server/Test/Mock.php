<?php
/*
 *                    ....
 *                  .:   '':.
 *                  ::::     ':..
 *                  ::.         ''..
 *       .:'.. ..':.:::'    . :.   '':.
 *      :.   ''     ''     '. ::::.. ..:
 *      ::::.        ..':.. .''':::::  .
 *      :::::::..    '..::::  :. ::::  :
 *      ::'':::::::.    ':::.'':.::::  :
 *      :..   ''::::::....':     ''::  :
 *      :::::.    ':::::   :     .. '' .
 *   .''::::::::... ':::.''   ..''  :.''''.
 *   :..:::'':::::  :::::...:''        :..:
 *   ::::::. '::::  ::::::::  ..::        .
 *   ::::::::.::::  ::::::::  :'':.::   .''
 *   ::: '::::::::.' '':::::  :.' '':  :
 *   :::   :::::::::..' ::::  ::...'   .
 *   :::  .::::::::::   ::::  ::::  .:'
 *    '::'  '':::::::   ::::  : ::  :
 *              '::::   ::::  :''  .:
 *               ::::   ::::    ..''
 *               :::: ..:::: .:''
 *                 ''''  '''''
 *
 *
 * AUTOMAD
 *
 * Copyright (c) 2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Test;

use Automad\Core\Context;
use Automad\Core\Page;
use Automad\Core\Parse;
use Automad\Core\Shared;
use PHPUnit\Framework\TestCase;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The test mock class.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Mock extends TestCase {
	/**
	 * Create a mock of the Automad object with a single page.
	 * A template can be passed optionally to the page.
	 *
	 * @param string $template
	 * @return object The Automad Mock
	 */
	public function createAutomad(string $template = '') {
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
	 * Create a collection of test pages.
	 *
	 * @param Shared $Shared
	 * @param string $template
	 * @return array the collection
	 */
	private function createCollection(Shared $Shared, string $template) {
		$theme = 'templates';
		$testsDir = AM_BASE_DIR . '/automad/tests';

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
					Parse::dataFile($testsDir . '/data/page.txt'),
					Parse::dataFile($testsDir . '/data/inheritance.txt')
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
					Parse::dataFile($testsDir . '/data/text.txt')
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
					Parse::dataFile($testsDir . '/data/blocks.txt')
				),
				$Shared
			)
		);
	}
}
