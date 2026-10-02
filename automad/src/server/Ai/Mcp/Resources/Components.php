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

use Automad\Core\Blocks;
use Automad\Models\ComponentCollection;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The components resource.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class Components extends AbstractResource {
	/**
	 * @return string
	 */
	public function getDescription(): string {
		return <<< TXT
			A list of available components.

			Use the component `id` in order to reference a component in the `page_create` and `page_update` tools.
			`blocks` contains the actual structured data of the component.
			`preview` contains simplified string representation of all contained blocks.
			TXT;
	}

	/**
	 * @return callable
	 */
	public function getHandler(): callable {
		return function () {
			$ComponentCollection = new ComponentCollection();

			return array_map(
				function (array $component) use ($ComponentCollection): array {
					return array(
						'id' => $component['id'],
						'name' => $component['name'],
						'blocks' => Blocks::toAgent($component['blocks'] ?? array(), $ComponentCollection),
						'preview' => html_entity_decode(strip_tags(Blocks::toString($component['blocks'] ?? array(), $ComponentCollection)))
					);
				},
				$ComponentCollection->get()
			);
		};
	}

	/**
	 * The resource's name.
	 *
	 * @return string
	 */
	public function getName(): string {
		return 'components';
	}

	/**
	 * @return string
	 */
	public function getTitle(): string {
		return 'Components';
	}

	/**
	 * Return true in order to make tool or resource private.
	 *
	 * @return bool
	 */
	public static function requiresAuth(): bool {
		return true;
	}
}
