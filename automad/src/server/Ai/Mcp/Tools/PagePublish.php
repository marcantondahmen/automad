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
use Mcp\Exception\ToolCallException;
use Mcp\Schema\ToolAnnotations;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The page publish tool.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class PagePublish extends AbstractTool {
	/**
	 * The tool's behavioral hints for clients (read-only, destructive, idempotent, open-world).
	 *
	 * @return ToolAnnotations|null
	 */
	public function getAnnotations(): ToolAnnotations|null {
		return new ToolAnnotations(
			readOnlyHint: false,
			destructiveHint: true,
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
			Publish an Automad page.

			All content edits are saved as draft first. 
			In order to make them publicly available, a page must
			be published.
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
			string $id,
		) {
			$Automad = Automad::fromCache();
			$Page = $Automad->getPage($id);

			if (!$Page) {
				throw new ToolCallException("Page [$id] not found.");
			}

			return $Page->publish();
		};
	}

	/**
	 * The tool's input schema.
	 *
	 * @return array|null
	 */
	public function getInputSchema(): array|null {
		return PageSchema::publish();
	}

	/**
	 * The tool's name, as used by MCP clients to call it.
	 *
	 * @return string
	 */
	public function getName(): string {
		return 'page_publish';
	}

	/**
	 * The tool's human-readable title.
	 *
	 * @return string
	 */
	public function getTitle(): string {
		return 'Page: Publish';
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
