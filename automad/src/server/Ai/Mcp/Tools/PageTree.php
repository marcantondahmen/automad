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
use Automad\Core\Automad;
use Automad\Models\Page;
use Automad\System\Fields;
use Mcp\Exception\ToolCallException;
use Mcp\Schema\ToolAnnotations;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The page tree tool.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class PageTree extends AbstractTool {
	/**
	 * The tool's behavioral hints for clients (read-only, destructive, idempotent, open-world).
	 *
	 * @return ToolAnnotations|null
	 */
	public function getAnnotations(): ToolAnnotations|null {
		return new ToolAnnotations(
			readOnlyHint: true,
			destructiveHint: false,
			idempotentHint: true,
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
			The hierarchical structure of the website. 

			Use this resource to understand how pages are organized and how they relate to each other. 
			Each node represents a page and contains its ID and child pages. 

			Use this tool also to efficiently get a list of sub-pages for a specific page.

			Use the `page_read` tool to retrieve the full content of a specific page.
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
			string $id = '/',
			int $depth = 10
		) {
			$Automad = Automad::fromCache();
			$pages = $Automad->getPages();
			$Start = $Automad->getPage($id);

			if (!$Start) {
				throw new ToolCallException("Page [$id] not found.");
			}

			$maxLevel = intval($Start->get(Fields::LEVEL)) + $depth;

			$branch = function (Page $Parent) use ($pages, $maxLevel, &$branch): array {
				unset($pages[$Parent->origUrl]);

				$data = array(
					'id' => $Parent->origUrl,
					'title' => $Parent->get(Fields::TITLE),
					'last_modified' => $Parent->get(Fields::TIME_LAST_MODIFIED),
					'template' => $Parent->get(Fields::TEMPLATE)
				);

				if (intval($Parent->get(Fields::LEVEL)) < $maxLevel) {
					$children = array();

					foreach ($pages as $Page) {
						if ($Page->parentUrl === $Parent->origUrl) {
							$children[] = $branch($Page);
						}
					}

					if (!empty($children)) {
						$data['children'] = $children;
					}
				}

				return $data;
			};

			return $branch($Start);
		};
	}

	/**
	 * The tool's input schema.
	 *
	 * @return array
	 */
	public function getInputSchema(): array {
		return PageSchema::tree();
	}

	/**
	 * The tool's name, as used by MCP clients to call it.
	 *
	 * @return string
	 */
	public function getName(): string {
		return 'page_tree';
	}

	/**
	 * The tool's human-readable title.
	 *
	 * @return string
	 */
	public function getTitle(): string {
		return 'Page: Tree';
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
