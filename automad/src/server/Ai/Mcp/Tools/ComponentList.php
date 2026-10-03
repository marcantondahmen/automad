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

namespace Automad\Ai\Mcp\Tools;

use Automad\Core\Blocks;
use Automad\Core\Str;
use Automad\Models\ComponentCollection;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The component list tool.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class ComponentList extends AbstractTool {
	/**
	 * @return string
	 */
	public function getDescription(): string {
		return <<< TXT
			A list of available components.

			Use the component `id` in order to reference a component in the `page_create` and `page_update` tools.
			`content` contains a simplified string representation of all contained blocks.
			TXT;
	}

	/**
	 * The tool's main handler. Its parameters are reflected on to derive the tool's input schema
	 * and to map incoming call arguments by name.
	 *
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
						'content' => Str::shorten(html_entity_decode(strip_tags(Blocks::toString($component['blocks'] ?? array(), $ComponentCollection))), 500)
					);
				},
				$ComponentCollection->get()
			);
		};
	}

	/**
	 * The tool's input schema.
	 *
	 * @return array
	 */
	public function getInputSchema(): array {
		return array();
	}

	/**
	 * The tool's name.
	 *
	 * @return string
	 */
	public function getName(): string {
		return 'component_list';
	}

	/**
	 * @return string
	 */
	public function getTitle(): string {
		return 'Component: List';
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
