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
use Automad\Ai\Mcp\Transformer\PageTransformer;
use Automad\Core\Automad;
use Automad\System\FileUtils;
use Mcp\Exception\ToolCallException;
use Mcp\Schema\ToolAnnotations;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The page read tool.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class PageRead extends AbstractTool {
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
			Read a page by providing its ID (the local URL path).

			The `content` field contains the main page content.
		
			The `meta` field contains additional meta data and settings
			such as the template, last modification date, the url etc.
		
			The `files` field contains all files that are attached to the page.
			Those files can be linked as is in image or gallery blocks, all
			paths are resolved automatically by the render engine.

			The following fields control the visibility of a page:
			- `publicationState`: `draft` or `published`, unpublished changes can only be viewed by admins
			- `private`: if true, the page can only be viewed by admins, independent from `publicationState`
			- `hidden`: if true, the page is publicly accessible but hidden in page lists and navigations

			Example: page_read {"id": "/about", "format": "structured"} returns
			{
				"id": "/about",
				"parent": "/",
				"url": "http://domain.com/",
				"title": "About Me",
				"date": "",
				"publicationState": "published",
				"private": false,
				"hidden": false,
				"tags": ["..."]
				"content": {"+main": [ ...blocks with id, type, data... ]},
				"meta": {"template": "page_sidebar", "title": "About", ":lastModified": "..."}
			}

			Use "text" output `format` only for reading; it returns plain text per field and cannot be passed to `page_update`.
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
			string $format = 'structured'
		) {
			$Automad = Automad::fromCache();
			$PageTransformer = new PageTransformer($Automad);
			$Page = $Automad->getPage($id);
			$toString = $format !== 'structured';

			if (!$Page) {
				throw new ToolCallException("Page [$id] not found.");
			}

			$data = $PageTransformer->toAgent($Page, $toString);
			$data['files'] = array_map(
				fn ($file) => basename($file),
				FileUtils::fileDeclaration('*.*', $Page, true)
			);

			return $data;
		};
	}

	/**
	 * The tool's input schema.
	 *
	 * @return array|null
	 */
	public function getInputSchema(): array|null {
		return PageSchema::read();
	}

	/**
	 * The tool's name, as used by MCP clients to call it.
	 *
	 * @return string
	 */
	public function getName(): string {
		return 'page_read';
	}

	/**
	 * The tool's human-readable title.
	 *
	 * @return string
	 */
	public function getTitle(): string {
		return 'Page: Read';
	}

	/**
	 * Return true in order to make the tool private.
	 *
	 * @return bool
	 */
	public static function requiresAuth(): bool {
		return false;
	}
}
