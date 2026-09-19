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

namespace Automad\Ai\Mcp\ResourceTemplates;

use Automad\Ai\Mcp\ResourceId;
use Automad\Core\Automad;
use Automad\Core\Blocks;
use Automad\Core\Debug;
use Automad\System\Fields;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The page template.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class Page extends AbstractResourceTemplate {
	const array IGNORED_FIELDS = array(
		Fields::AUTOMAD_VERSION,
	);

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
			$Automad = Automad::fromCache();
			$Page = $Automad->getPage(ResourceId::decode($page_id));

			if (!$Page) {
				return array();
			}

			$data = array(
				'id' => $page_id
			);

			foreach ($Page->data as $key => $value) {
				if (str_starts_with($key, '+')) {
					$blocks = Blocks::toAgent(
						$value['blocks'] ?? array(),
						$Automad->ComponentCollection
					);

					$data[$key] = array('blocks' => $blocks);
				} else {
					if (!in_array($key, Page::IGNORED_FIELDS)) {
						$data[$key] = $value;
					}
				}
			}

			Debug::log($data, 'Page data');

			return $data;
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
