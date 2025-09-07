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
 * Copyright (c) 2021-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Test;

use Automad\Core\Automad;
use Automad\Models\Page;
use Automad\Models\Shared;
use PHPUnit\Framework\TestCase;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The test mock class.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021-2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Mock extends TestCase {
	/**
	 * Create a mock of the Automad object with a single page.
	 * A template can be passed optionally to the page.
	 *
	 * @param string $template
	 * @return Automad
	 */
	public function createAutomad(string $template = ''): object {
		$Shared = new Shared();
		$Shared->data['shared'] = 'Shared default text content';
		$Shared->data['+default'] = json_decode('{"blocks": [{"id": "abc","type": "paragraph","data": {"text": "test"}}],"time": "123456789","version": "1.2.3","automadVersion": "1.2.3"}', true);
		$pages = $this->createCollection($Shared, $template);
		$Automad = new Automad($pages, $Shared);

		return $Automad;
	}

	/**
	 * Create a collection of test pages.
	 *
	 * @param Shared $Shared
	 * @param string $template
	 * @return array the collection
	 */
	private function createCollection(Shared $Shared, string $template): array {
		$theme = 'templates';

		return array(
			'/' => new Page(
				array(
					'title' => 'Home',
					'url' => '/',
					':path' => '/',
					':origUrl' => '/',
					'theme' => $theme,
					'template' => $template,
					':level' => 0,
					':index' => '1',
					'tags' => 'test',
					'searchTest' => 'no results'
				),
				$Shared
			),
			'/page' => new Page(
				array_merge(
					array(
						'url' => '/page',
						':path' => '/page-slug/',
						':origUrl' => '/page',
						':parent' => '/',
						'theme' => $theme,
						'template' => $template,
						':level' => 1,
						':index' => '1.1',
						'tags' => 'test'
					),
					Data::load('/page-slug'),
					Data::load('/inheritance'),
					Data::load('/falsy'),
					Data::load('/invalid'),
					Data::load('/find-first-image'),
					Data::load('/component')
				),
				$Shared
			),
			'/page/subpage' => new Page(
				array(
					'title' => 'Subpage',
					'url' => '/page/subpage',
					':path' => '/page-slug/subpage/',
					':origUrl' => '/page/subpage',
					':parent' => '/page',
					'theme' => $theme,
					'template' => $template,
					':level' => 2,
					':index' => '1.1.1',
					'tags' => 'test',
					'searchTest' => 'not included'
				),
				$Shared
			),
			'/page/subpage/breadcrumbs-test' => new Page(
				array(
					'title' => 'BreadcrumbsTest',
					'url' => '/page/subpage/breadcrumbs-test',
					':path' => '/page-slug/subpage/breadcrumbs-test',
					':origUrl' => '/page/subpage/breadcrumbs-test',
					':parent' => '/page/subpage',
					'theme' => $theme,
					'template' => $template,
					':level' => 3,
					':index' => '1.1.1.1',
					'tags' => ''
				),
				$Shared
			),
			'/text' => new Page(
				array_merge(
					array(
						'url' => '/text',
						':path' => '/text/',
						':origUrl' => '/text',
						':parent' => '/',
						'theme' => $theme,
						'template' => $template,
						':level' => 1,
						':index' => '1.2'
					),
					Data::load('/text')
				),
				$Shared
			),
			'/blocks' => new Page(
				array_merge(
					array(
						'url' => '/blocks',
						':path' => '/blocks-slug/',
						':origUrl' => '/blocks',
						':parent' => '/',
						'theme' => $theme,
						'template' => $template,
						':level' => 1,
						':index' => '1.3'
					),
					Data::load('/blocks')
				),
				$Shared
			)
		);
	}
}
