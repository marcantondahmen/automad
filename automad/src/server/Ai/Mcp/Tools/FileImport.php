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

use Automad\Ai\Mcp\Schema\FileSchema;
use Automad\Core\Automad;
use Automad\Core\Messenger;
use Automad\Core\Text;
use Automad\Models\File;
use Automad\System\DiskUsage;
use Mcp\Exception\ToolCallException;
use Mcp\Schema\ToolAnnotations;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The file import tool.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class FileImport extends AbstractTool {
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
			openWorldHint: true
		);
	}

	/**
	 * The tool's description.
	 *
	 * @return string
	 */
	public function getDescription(): string {
		return <<< TXT
			Import a file from a URL to a page or to the shared files.

			Use this tool whenever an image should be displayed in an `image`, `gallery` or
			`imageSlideshow` block and the image is not yet attached to the page. The tool downloads
			the file from the given `url` and stores it in the directory of the page that is given by
			the `page` property. The page must already exist, so when creating a new page,
			first use the `page_create` tool and import the files afterwards. If the `page` property is
			omitted, the file is stored in the shared files.

			The name of the imported file is derived from the URL and can differ from the original
			file name, since it is sanitized and an extension is added when missing.
			Always use the `link` property of the result as it is in order to reference the
			imported file in a block, that is as `url` of an `image` block or as an item of
			the `files` list of a `gallery` or `imageSlideshow` block.
			Use the `page_create` or `page_update` tool to add the link to the page content.

			Note that an existing file with the same name will be replaced. Importing a URL that has
			no file extension fails when the resulting file name already exists.

			Example: import an image to the page /about and use it in an image block.

			1. Call this tool:
				{
					"url": "https://example.com/images/team.jpg",
					"page": "/about"
				}

			2. The result contains the link:
				{
					"file": "team.jpg",
					"link": "team.jpg",
					"page": "/about"
				}

			3. Use the link in the `page_update` tool:
				{"type": "image", "data": {"url": "team.jpg", "alt": "The team"}}

			This tool only accepts URLs. Local files can not be imported.
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
			string $url,
			string $page = ''
		) {
			// Fetch does not restrict protocols, so only allow remote URLs here in order
			// to prevent reading local files using for example file:// URLs.
			if (!preg_match('#^https?://#i', $url)) {
				throw new ToolCallException('The `url` must be a full remote URL starting with http:// or https://.');
			}

			if (DiskUsage::quotaExceeded()) {
				throw new ToolCallException(Text::get('diskQuotaExceeded'));
			}

			if ($page !== '' && !Automad::fromCache()->getPage($page)) {
				throw new ToolCallException("Page [$page] not found.");
			}

			$Messenger = new Messenger();
			$file = File::import($url, $page, $Messenger);

			if ($file === '') {
				throw new ToolCallException($Messenger->getError() ?: 'The file could not be imported.');
			}

			return array(
				'file' => $file,
				'link' => $page !== '' ? $file : AM_DIR_SHARED . '/' . $file,
				'page' => $page
			);
		};
	}

	/**
	 * The tool's input schema.
	 *
	 * @return array|null
	 */
	public function getInputSchema(): array|null {
		return FileSchema::import();
	}

	/**
	 * The tool's name, as used by MCP clients to call it.
	 *
	 * @return string
	 */
	public function getName(): string {
		return 'file_import';
	}

	/**
	 * The tool's human-readable title.
	 *
	 * @return string
	 */
	public function getTitle(): string {
		return 'File: Import';
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
