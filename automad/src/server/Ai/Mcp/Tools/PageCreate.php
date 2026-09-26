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
use Automad\Core\Blocks;
use Automad\Core\Cache;
use Automad\Models\Page;
use Mcp\Exception\ToolCallException;
use Mcp\Schema\ToolAnnotations;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The page create tool.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class PageCreate extends AbstractTool {
	/**
	 * The tool's behavioral hints for clients (read-only, destructive, idempotent, open-world).
	 *
	 * @return ToolAnnotations|null
	 */
	public function getAnnotations(): ToolAnnotations|null {
		return new ToolAnnotations(
			readOnlyHint: false,
			destructiveHint: false,
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
			Create a new Automad page.

			Before creating the page:

			1. Read the `automad://pages` resource and select the page that should be the parent that 
			   contains the new page. Use the `url` property of that parent page for the `parent` field.
			2. Read the `automad://templates/page` resource and select an appropriate installed template.
			3. Inspect the selected template's available content fields and their descriptions.
			4. Place the page's primary content in the field that is intended for the main content. 
			   Use other fields such as hero or footer only when appropriate.
			5. Use only fields provided by the selected template.
			6. Use the available block types defined by the input schema to construct the page content.

			The `parent`, `title`, and `template` properties are required. Content fields are template-specific 
			and are provided through the `fields` property.
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
			string $title,
			string $template,
			array $tags = array(),
			array $fields = array()
		) {
			$Automad = Automad::fromCache();
			$Parent = $Automad->getPage($parent);

			if (!$Parent) {
				throw new ToolCallException('Parent page "$parent" not found.');
			}

			$data = array();

			foreach ($fields as $key => $blocks) {
				$data[$key] = array('blocks' => Blocks::fromAgent($blocks));
			}

			$Page = Page::add($Parent, $title, $template, true, $data);

			Cache::clear();

			return $Page;
		};
	}

	/**
	 * The tool's input schema.
	 *
	 * @return array
	 */
	public function getInputSchema(): array {
		return PageSchema::create();
	}

	/**
	 * The tool's name, as used by MCP clients to call it.
	 *
	 * @return string
	 */
	public function getName(): string {
		return 'page/create';
	}

	/**
	 * The tool's human-readable title.
	 *
	 * @return string
	 */
	public function getTitle(): string {
		return 'Page / Create';
	}
}
