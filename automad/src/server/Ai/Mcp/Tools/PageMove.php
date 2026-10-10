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
use Automad\Ai\Mcp\Validator\PageStructureValidator;
use Automad\Core\Automad;
use Automad\Core\Cache;
use Automad\Models\Page;
use Mcp\Exception\ToolCallException;
use Mcp\Schema\ToolAnnotations;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The page move tool.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class PageMove extends AbstractTool {
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
			Move an Automad page to a new parent.

			Example 1: 
			{
				"id": "/project",
				"target": "/work"	
			}

			This will move the page with the ID /project into /work.
			Its new URL will be /work/project after the move.

			Example 2: 
			{
				"id": "/work/project",
				"target": "/"	
			}

			This will move the page "project" with the ID /work/project
			back to the first level under the homepage. The new URL
			will be /project after moving.

			Critical: The homepage with ID `/` must not be moved. A page also can not be moved
			into itself or one of its own sub-pages, or below the parent it already has.

			The tool returns the basic data of the moved page. Moving a page changes its `id` and `url`.
			The new `id` is returned together with the old one as `previousId`. Use the new `id`
			for all further calls. In case the target already contains a page with the same name,
			a suffix is appended to the name of the moved page.
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
			string $target
		) {
			$Automad = Automad::fromCache();
			$Page = $Automad->getPage($id);
			$TargetPage = $Automad->getPage($target);

			if (!$Page) {
				throw new ToolCallException("Page [$id] not found.");
			}

			if (!$TargetPage) {
				throw new ToolCallException("Target page [$target] not found.");
			}

			(new PageStructureValidator())->assertMovable(
				$Page->origUrl,
				$Page->path,
				$Page->parentUrl,
				$TargetPage->origUrl,
				$TargetPage->path
			);

			// Use the name of the current directory as slug like the dashboard does. The slug field
			// can already contain the slug of a changed title that is not yet published.
			$newPath = $Page->moveDirAndUpdateLinks($TargetPage->path, basename($Page->path));
			Cache::clear();

			$MovedPage = Page::findByPath($newPath);

			if (!$MovedPage) {
				throw new ToolCallException("The page [$id] could not be moved.");
			}

			return array(
				...(new PageTransformer($Automad))->baseData($MovedPage),
				'previousId' => $id
			);
		};
	}

	/**
	 * The tool's input schema.
	 *
	 * @return array|null
	 */
	public function getInputSchema(): array|null {
		return PageSchema::inputMove();
	}

	/**
	 * The tool's output schema.
	 *
	 * @return array|null
	 */
	public function getOutputSchema(): array|null {
		return PageSchema::outputMove();
	}

	/**
	 * The tool's name, as used by MCP clients to call it.
	 *
	 * @return string
	 */
	public function getName(): string {
		return 'page_move';
	}

	/**
	 * The tool's human-readable title.
	 *
	 * @return string
	 */
	public function getTitle(): string {
		return 'Page: Move';
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
