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

use Automad\Ai\Mcp\Schema\PageSchema;
use Automad\Ai\Mcp\Validator\PageStructureValidator;
use Automad\Core\Automad;
use Automad\Core\Cache;
use Automad\Core\PageIndex;
use Automad\Models\Page;
use Mcp\Exception\ToolCallException;
use Mcp\Schema\ToolAnnotations;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The page reorder children tool.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class PageReorderChildren extends AbstractTool {
	/**
	 * The tool's behavioral hints for clients (read-only, destructive, idempotent, open-world).
	 *
	 * @return ToolAnnotations|null
	 */
	public function getAnnotations(): ToolAnnotations|null {
		return new ToolAnnotations(
			readOnlyHint: false,
			destructiveHint: true,
			idempotentHint: false,
			openWorldHint: false
		);
	}

	/**
	 * The tool's description.
	 *
	 * @return string
	 */
	public function getDescription(): string {
		return <<< TXT
			Reorder all sub-pages below a given parent page.

			Use the `page_tree` tool with the `id` of the parent page and
			a `depth` of 1 in order to get the contained children (sub-pages).

			Construct the new layout (ordered list of pages) by building an
			array of basenames (no slashes) of the sub-page IDs in the new order.

			Example:
			
			A given structure looks like this:
			/root
			  /parent-page
			    /parent-page/another-page
			    /parent-page/sub-page

			In order to reorder the sub-pages to this:
			/root
			  /parent-page
			    /parent-page/sub-page
			    /parent-page/another-page
					
			use this tool as follows:
			{
				"parent": "/parent-page",
				"order": ["sub-page", "another-page"]	
			}

			Only existing sub-pages of the parent are allowed, unknown names are rejected.
			Duplicates are ignored. Sub-pages that are missing in the list are appended in their
			current order. The tool returns an object with an `order` array that contains the effective order of all sub-pages.
			TXT;
	}

	/**
	 * The tool's main handler. Its parameters are reflected on to derive the tool's input schema
	 * and to map incoming call arguments by name.
	 *
	 * @return callable
	 */
	public function getHandler(): callable {
		return function (
			string $parent,
			array $order
		) {
			$Automad = Automad::fromCache();
			$Parent = $Automad->getPage($parent);

			if (!$Parent) {
				throw new ToolCallException("Page [$parent] not found.");
			}

			// The pages are collected in their current order, parents are always followed by their children.
			$children = array();

			foreach ($Automad->getPages() as $Page) {
				if ($Page->parentUrl === $Parent->origUrl) {
					$children[] = basename($Page->path);
				}
			}

			$newOrder = (new PageStructureValidator())->resolveOrder($Parent->origUrl, $children, $order);

			if (!PageIndex::write($Parent->path, $newOrder) && !empty($newOrder)) {
				throw new ToolCallException("The new order for [$parent] could not be saved.");
			}

			Cache::clear();

			return array('order' => $newOrder);
		};
	}

	/**
	 * The tool's input schema.
	 *
	 * @return array|null
	 */
	public function getInputSchema(): array|null {
		return PageSchema::inputReorderChildren();
	}

	/**
	 * The tool's output schema.
	 *
	 * @return array|null
	 */
	public function getOutputSchema(): array|null {
		return PageSchema::outputReorderChildren();
	}

	/**
	 * The tool's name, as used by MCP clients to call it.
	 *
	 * @return string
	 */
	public function getName(): string {
		return 'page_reorder_children';
	}

	/**
	 * The tool's human-readable title.
	 *
	 * @return string
	 */
	public function getTitle(): string {
		return 'Page: Reorder Children';
	}

	/**
	 * Return true in order to make the tool private.
	 *
	 * @return bool
	 */
	public static function requiresAuth(): bool {
		return true;
	}
}
