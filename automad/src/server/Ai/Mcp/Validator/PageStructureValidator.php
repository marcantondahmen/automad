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

namespace Automad\Ai\Mcp\Validator;

use Mcp\Exception\ToolCallException;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The validator for structural page changes (moving and reordering) that are requested by MCP clients.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class PageStructureValidator {
	/**
	 * Make sure that a page can be moved below a target page.
	 *
	 * The homepage can't be moved and a page can't be moved into itself or one of its sub-pages.
	 * Moving a page below the parent it already has would not change anything.
	 *
	 * @param string $id the page ID
	 * @param string $path the page path with trailing slash
	 * @param string $parentId the ID of the current parent page
	 * @param string $targetId the ID of the target page
	 * @param string $targetPath the path of the target page with trailing slash
	 * @throws ToolCallException
	 */
	public function assertMovable(string $id, string $path, string $parentId, string $targetId, string $targetPath): void {
		if ($id === '/') {
			throw new ToolCallException('The homepage with ID [/] can not be moved.');
		}

		// Paths end with a slash, therefore a page like "/media-copy/" is not a sub-page of "/media/".
		if (str_starts_with($targetPath, $path)) {
			throw new ToolCallException(
				"The page [$id] can not be moved into itself or one of its own sub-pages. The target [$targetId] is invalid."
			);
		}

		if ($parentId === $targetId) {
			throw new ToolCallException("The page [$id] is already located below the target page [$targetId].");
		}
	}

	/**
	 * Resolve the effective order of sub-pages for a reorder request.
	 *
	 * All items are reduced to their basename. Duplicates are ignored. All existing sub-pages
	 * that are missing in the requested order are appended in their current order, so that the
	 * result always contains every sub-page exactly once.
	 *
	 * @param string $parentId the ID of the parent page
	 * @param array<int, string> $children the basenames of the existing sub-pages in their current order
	 * @param array<int, string> $order the requested order
	 * @return array<int, string> the effective order
	 * @throws ToolCallException
	 */
	public function resolveOrder(string $parentId, array $children, array $order): array {
		$order = array_values(array_unique(array_map(fn (string $item): string => basename($item), $order)));
		$unknown = array_values(array_diff($order, $children));

		if (!empty($unknown)) {
			throw new ToolCallException(
				"Unknown sub-pages of [$parentId]: " . $this->format($unknown) . '. ' .
				'Existing sub-pages: ' . $this->format($children) . '. ' .
				'Use the `page_tree` tool with a `depth` of 1 to get the sub-pages.'
			);
		}

		return array_merge($order, array_diff($children, $order));
	}

	/**
	 * Format a list of names for messages.
	 *
	 * @param array<int, string> $names
	 * @return string
	 */
	private function format(array $names): string {
		return empty($names) ? '(none)' : join(', ', array_map(fn (string $name): string => "`$name`", $names));
	}
}
