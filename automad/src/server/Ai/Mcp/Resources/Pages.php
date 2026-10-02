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
 * Copyright (c) 2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

namespace Automad\Ai\Mcp\Resources;

use Automad\Ai\Mcp\Transformer\PageTransformer;
use Automad\Core\Automad;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * All pages resource.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class Pages extends AbstractResource {
	/**
	 * @return string
	 */
	public function getDescription(): string {
		return 'A collection of all pages that are visible to the caller. Authenticated clients also receive private pages and drafts. Use the page uri that is associated with a page in order to get the entire page object including all associated block data.';
	}

	/**
	 * @return callable
	 */
	public function getHandler(): callable {
		return function () {
			$pages = array();
			$Automad = Automad::fromCache();
			$PageTransformer = new PageTransformer($Automad);

			foreach ($Automad->getPages() as $Page) {
				$pages[] = $PageTransformer->toAgent($Page);
			}

			return $pages;
		};
	}

	/**
	 * The resource's name.
	 *
	 * @return string
	 */
	public function getName(): string {
		return 'pages';
	}

	/**
	 * @return string
	 */
	public function getTitle(): string {
		return 'All pages';
	}

	/**
	 * Return true in order to make tool or resource private.
	 *
	 * @return bool
	 */
	public static function requiresAuth(): bool {
		return false;
	}
}
