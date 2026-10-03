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
use Automad\Api\EditLock;
use Automad\Core\Automad;
use Automad\Core\Cache;
use Automad\Models\Page;
use Automad\System\Fields;
use Mcp\Exception\ClientException;
use Mcp\Exception\ToolCallException;
use Mcp\Schema\Elicitation\BooleanSchemaDefinition;
use Mcp\Schema\Elicitation\ElicitationSchema;
use Mcp\Schema\ToolAnnotations;
use Mcp\Server\ClientGateway;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The page delete tool.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class PageDelete extends AbstractTool {
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
			Delete an Automad page.

			The `id` property represents the local absolute page URL path
			like for example `/about` or `/work/projects`.

			If the user does not specify a valid `id` property value, use the `page_search` tool
			and select the page that should be deleted.

			The user is asked to confirm the deletion by the client before the page is actually deleted.
			Clients that are not able to ask for confirmation cannot delete pages.
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
			ClientGateway $Client,
		) {
			$Automad = Automad::fromCache();
			$Page = $Automad->getPage($id);

			if (!$Page) {
				throw new ToolCallException("Page [$id] not found.");
			}

			// The confirmation must happen before any side effect, since handlers
			// are executed again from the top when a client answers an elicitation
			// on a stateless protocol revision.
			self::confirm($Client, $Page, $id);

			$Page->delete();
			Cache::clear();

			// See also automad/src/client/admin/components/Forms/Form.ts
			// in order to match the lock handle style.
			EditLock::set("page-$id", 'mcp');

			return "The page [$id] was deleted successfully.";
		};
	}

	/**
	 * The tool's input schema.
	 *
	 * @return array
	 */
	public function getInputSchema(): array {
		return PageSchema::delete();
	}

	/**
	 * The tool's name, as used by MCP clients to call it.
	 *
	 * @return string
	 */
	public function getName(): string {
		return 'page_delete';
	}

	/**
	 * The tool's human-readable title.
	 *
	 * @return string
	 */
	public function getTitle(): string {
		return 'Page: Delete';
	}

	/**
	 * Return true in order to make the tool private.
	 *
	 * @return bool
	 */
	public static function requiresAuth(): bool {
		return true;
	}

	/**
	 * Ask the user to confirm the deletion by sending an elicitation request to the client.
	 * Fails closed, which means that the deletion is only allowed when the user explicitly accepted.
	 *
	 * @param ClientGateway $Client
	 * @param Page $Page
	 * @param string $id
	 * @throws ToolCallException when the deletion was not confirmed
	 */
	private static function confirm(ClientGateway $Client, Page $Page, string $id): void {
		if (!$Client->supportsElicitation()) {
			throw new ToolCallException(
				"The connected MCP client does not support confirmation requests, so the page [$id] was not deleted."
			);
		}

		$title = strval($Page->get(Fields::TITLE));
		$Schema = new ElicitationSchema(
			array('confirm' => new BooleanSchemaDefinition('Delete page', 'Delete the page and all of its subpages.', false)),
			array('confirm')
		);

		try {
			$Result = $Client->elicit(
				"Delete the page \"$title\" [$id] including all of its subpages?",
				$Schema,
				key: 'confirm_delete'
			);
		} catch (ClientException $e) {
			throw new ToolCallException("Could not get a confirmation for deleting the page [$id]: " . $e->getMessage());
		}

		if (!$Result->isAccepted() || ($Result->content['confirm'] ?? false) !== true) {
			throw new ToolCallException("The deletion of the page [$id] was not confirmed by the user.");
		}
	}
}
