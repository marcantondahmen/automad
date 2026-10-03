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
use Automad\Models\Page;
use Automad\System\Fields;
use Mcp\Exception\ToolCallException;
use Mcp\Schema\ToolAnnotations;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The page search tool.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class PageSearch extends AbstractTool {
	/**
	 * The maximum number of results.
	 */
	const int LIMIT = 50;

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
			Search for pages by one or more keywords.

			Keywords can be separated by space. 
			Multiple keywords will narrow down the search.
			Only pages that contain all keywords will be 
			included in the search results.

			The search results are sorted by search hit count
			in descending order. Use the optional `scopeId` in order to only search below
			a given page. The scope page itself is not part of the results.
			The homepage `/` as scope searches the whole site.

			Use the `page_read` tool in order to get the 
			full content for a specific page.

			The following fields control the visibility of a page:
			- `publicationState`: `draft` or `published`, unpublished changes can only be viewed by admins
			- `private`: if true, the page can only be viewed by admins, independent from `publicationState`
			- `hidden`: if true, the page is publicly accessible but hidden in page lists and navigations

			The result limit is 50 pages.
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
			string $search,
			string|null $scopeId = null
		) {
			$Automad = Automad::fromCache();
			$PageTransformer = new PageTransformer($Automad);
			$scope = rtrim(trim((string) $scopeId), '/');

			if ($scope !== '' && !$Automad->getPage($scope)) {
				throw new ToolCallException("Scope page [$scopeId] not found.");
			}

			// Only pages below the scope page are matched. The homepage as scope matches all pages.
			$match = $scope === '' ? false : json_encode(array(
				Fields::ORIG_URL => '~^' . preg_quote($scope, '~') . '/~'
			));

			$Automad->Pagelist->config(array(
				'context' => false,
				'currentLanguageOnly' => false,
				'excludeCurrent' => false,
				'excludeHidden' => false,
				'filter' => false,
				'limit' => PageSearch::LIMIT,
				'match' => $match,
				'offset' => 0,
				'page' => false,
				'search' => $search,
				'sort' => Fields::SEARCH_RESULTS_COUNT . ' desc',
				'template' => false,
				'type' => false
			));

			return array_values(array_map(
				fn (Page $Page) => array(
					...$PageTransformer->baseData($Page),
					'context' => html_entity_decode(strip_tags($Page->get(Fields::SEARCH_RESULTS_CONTEXT))),
					'hitCount' => intval($Page->get(Fields::SEARCH_RESULTS_COUNT))
				),
				$Automad->Pagelist->getPages()
			));
		};
	}

	/**
	 * The tool's input schema.
	 *
	 * @return array|null
	 */
	public function getInputSchema(): array|null {
		return PageSchema::search();
	}

	/**
	 * The tool's name, as used by MCP clients to call it.
	 *
	 * @return string
	 */
	public function getName(): string {
		return 'page_search';
	}

	/**
	 * The tool's human-readable title.
	 *
	 * @return string
	 */
	public function getTitle(): string {
		return 'Page: Search';
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
