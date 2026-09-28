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
use Automad\Api\EditLock;
use Automad\Core\Automad;
use Automad\Core\Blocks;
use Automad\Core\Cache;
use Automad\Core\Value;
use Automad\Models\Page;
use Automad\System\Fields;
use Mcp\Exception\ToolCallException;
use Mcp\Schema\ToolAnnotations;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The page update tool.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class PageUpdate extends AbstractTool {
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
			Update a page.

			1. Use the `page_read` tool in order to get the current state of the page.
			   If the user does not provide a valid page ID, use the `page_search` tool
			   to get find the page that should be updated and use the page ID in the 
			   results.
			2. Update the page content according to the given input schema.
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
			string $title,
			string $template,
			array $tags = array(),
			array $fields = array()
		) {
			$Automad = Automad::fromCache();
			$PageTransformer = new PageTransformer($Automad);
			$Page = $Automad->getPage($id);

			if (!$Page) {
				throw new ToolCallException("Page [$id] not found.");
			}

			$data = $PageTransformer->toAgent($Page);
			$data[Fields::TITLE] = $title;
			$data[Fields::TAGS] = join(', ', $tags);

			foreach ($fields as $key => $blocks) {
				$originalBlocks = array();

				if (array_key_exists($key, $Page->data)) {
					// Forward existing blocks from data store for merging tunes
					// and other complex details that are not exposed to MCP.
					$originalField = Value::asEditorArray($Page->data[$key]);
					$originalBlocks = $originalField['blocks'] ?? array();
				}

				$data[$key] = array('blocks' => Blocks::fromAgent($blocks, $originalBlocks));
			}

			$Page->save($data, $template);

			Cache::clear();

			// See also automad/src/client/admin/components/Forms/Form.ts
			// in order to match the lock handle style.
			EditLock::set("page-$id", 'mcp');

			return Page::fromCache($id);
		};
	}

	/**
	 * The tool's input schema.
	 *
	 * @return array
	 */
	public function getInputSchema(): array {
		return PageSchema::update();
	}

	/**
	 * The tool's name, as used by MCP clients to call it.
	 *
	 * @return string
	 */
	public function getName(): string {
		return 'page_update';
	}

	/**
	 * The tool's human-readable title.
	 *
	 * @return string
	 */
	public function getTitle(): string {
		return 'Page: Update';
	}
}
