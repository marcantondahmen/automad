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

use Automad\Ai\Mcp\Transformer\PageTransformer;
use Automad\Core\Automad;
use Automad\Models\Page;
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
			string $search
		) {
			$Automad = Automad::fromCache();
			$PageTransformer = new PageTransformer($Automad);

			$Automad->Pagelist->config(array(
				'context' => false,
				'currentLanguageOnly' => false,
				'excludeCurrent' => false,
				'excludeHidden' => false,
				'filter' => false,
				'limit' => null,
				'match' => false,
				'offset' => 0,
				'page' => false,
				'search' => $search,
				'sort' => false,
				'template' => false,
				'type' => false
			));

			return array_map(
				fn (Page $Page) => $PageTransformer->toAgent($Page),
				$Automad->Pagelist->getPages(true)
			);
		};
	}

	/**
	 * The tool's input schema.
	 *
	 * @return array
	 */
	public function getInputSchema(): array {
		return array(
			'$schema' => 'https://json-schema.org/draft/2020-12/schema',
			'title' => 'Automad Search Schema',
			'description' => 'A input schema for searching an Automad website.',
			'type' => 'object',
			'properties' => array(
				'search' => array(
					'type' => 'string',
					'description' => <<< TXT
						The search string. Can be multiple words. 
						More words will narrow down the search results.
						TXT
				)
			)
		);
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
}
