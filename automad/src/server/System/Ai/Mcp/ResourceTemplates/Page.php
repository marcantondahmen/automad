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

namespace Automad\System\Ai\Mcp\Resources;

use Automad\Core\Automad;
use Automad\System\Ai\Mcp\ResourceId;
use Automad\System\Ai\Mcp\ResourceTemplates\AbstractResourceTemplate;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The page template.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class Page extends AbstractResourceTemplate {
	/**
	 * @return string
	 */
	public function getDescription(): string {
		return 'A single page that can be accessed by an URI provided in the automad://pages resource.';
	}

	/**
	 * @return callable
	 */
	public function getHandler(): callable {
		return function (string $page_id) {
			$url = ResourceId::decode($page_id);
			$Automad = Automad::fromCache();

			return array_merge(array('id' => $page_id), $Automad->getPage($url)->data ?? array());
		};
	}

	/**
	 * The resource's name.
	 *
	 * @return string
	 */
	public function getName(): string {
		return 'page';
	}

	/**
	 * @return string
	 */
	public function getTitle(): string {
		return 'Page';
	}

	/**
	 * The template's uri.
	 *
	 * @return string
	 */
	public function getUriTemplate(): string {
		return 'page/{page_id}';
	}
}
