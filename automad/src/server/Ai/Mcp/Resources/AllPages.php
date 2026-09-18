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

use Automad\Ai\Mcp\ResourceId;
use Automad\Core\Automad;
use Automad\Models\PageCollection;
use Automad\Models\Shared;
use Automad\System\Fields;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * All pages resource.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class AllPages extends AbstractResource {
	/**
	 * @return string
	 */
	public function getDescription(): string {
		return 'A collection of all public and private pages. Use the page uri that is associated with a page in order to get the entire page content.';
	}

	/**
	 * @return callable
	 */
	public function getHandler(): callable {
		return function () {
			$pages = array();
			$PageCollection = new PageCollection(new Shared());
			$Automad = Automad::fromCache();

			foreach ($Automad->getPages() as $Page) {
				$id = ResourceId::encode($Page->origUrl);
				$uri = "automad://page/$id";
				$pages[] = array('id' => $id, 'uri' => $uri, 'title' =>  $Page->get(Fields::TITLE), 'url' => $Page->origUrl);
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
}
