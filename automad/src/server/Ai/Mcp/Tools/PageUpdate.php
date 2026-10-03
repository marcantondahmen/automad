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
			   to find the page that should be updated and use the page ID in the 
			   results. In the context of retrieving page data that serves as 
			   basis for the actual update, the `format` property of the `page_read` 
			   tool must be `structured`.
			2. Update the page `content` according to the given input schema. 
			   Use the full structured output of the `page_read` tool as basis for 
			   modifications and try to keep block IDs stable when possible.
			   Each field inside `content` replaces the existing field with the same name.
			   Fields that are not part of `content` are kept as they are.
				
			Example: change the text of one paragraph on /about and add a new block at the end,
			leaving everything else untouched.
				
			1. Read the page with `page_read` and `format: "structured"`. The `+main` field contains:
				[
					{"id": "b1", "type": "header",	"data": {"level": 2, "text": "Team"}},
					{"id": "b2", "type": "paragraph", "data": {"text": "Old text."}}
				]
				
			2. Send the complete array for every content field you change. Existing blocks keep
			   their `id`; a new block has no `id`:
				{
					"id": "/about",
					"content": {
						"+main": [
							{"id": "b1", "type": "header",	"data": {"level": 2, "text": "Team"}},
							{"id": "b2", "type": "paragraph", "data": {"text": "New text."}},
							{"type": "paragraph", "data": {"text": "A new paragraph."}}
						]
					}
				}
			   
			- Only the `id` is required. Properties that are omitted or `null` keep their
			  current value on the existing page.
			- Use an empty value in order to clear a property: `date` as an empty string,
			  `private` or `hidden` as `false` and `tags` as an empty array.
			  The `title` can not be cleared.
			- Send an empty array for a content field in order to remove all of its blocks.
			- Blocks missing from a field's array are deleted.
			- The order of the array is the order on the page.

			Note that updates will be saved as drafts. A page must be published first using
			the `page_publish` tool in order to make changes publicly visible. This step allows
			the user to review the changes before going live. 
			Ask the user first before publishing a page.
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
			?string $title = null,
			?string $template = null,
			?string $theme = null,
			?string $date = null,
			?bool $private = null,
			?bool $hidden = null,
			?array $tags = null,
			?array $content = null
		) {
			$Automad = Automad::fromCache();
			$PageTransformer = new PageTransformer($Automad);
			$Page = $Automad->getPage($id);

			if (!$Page) {
				throw new ToolCallException("Page [$id] not found.");
			}

			$data = $PageTransformer->updateFromAgent(
				$Page,
				$title,
				$template,
				$theme,
				$date,
				$private,
				$hidden,
				$tags,
				$content
			);

			$Page->save($data, $data[Fields::TEMPLATE] ?? '');

			// See also automad/src/client/admin/components/Forms/Form.ts
			// in order to match the lock handle style.
			EditLock::set("page-$id", 'mcp');

			$Page = Page::fromCache($id);

			if (!$Page) {
				throw new ToolCallException("Page [$id] could not be updated.");
			}

			return $PageTransformer->toAgent($Page);
		};
	}

	/**
	 * The tool's input schema.
	 *
	 * @return array|null
	 */
	public function getInputSchema(): array|null {
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

	/**
	 * Return true in order to make the tool private.
	 *
	 * @return bool
	 */
	public static function requiresAuth(): bool {
		return true;
	}
}
