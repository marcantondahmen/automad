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

namespace Automad\Models\Search;

use Automad\Core\Blocks;
use Automad\Core\Debug;
use Automad\Core\Value;
use Automad\Models\ComponentCollection;
use Automad\Models\Page;
use Automad\Models\Shared;
use Automad\System\Fields;
use Exception;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The search index.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class SearchIndex {
	const IGNORED = array(
		Fields::HIDDEN,
		Fields::PRIVATE,
		Fields::TEMPLATE,
		Fields::THEME,
		Fields::SYNTAX_THEME,
		Fields::URL,
		Fields::CUSTOM_CONSENT_ACCEPT,
		Fields::CUSTOM_CONSENT_COLOR_BACKGROUND,
		Fields::CUSTOM_CONSENT_COLOR_BORDER,
		Fields::CUSTOM_CONSENT_COLOR_TEXT,
		Fields::CUSTOM_CONSENT_DECLINE,
		Fields::CUSTOM_CONSENT_PLACEHOLDER_COLOR_BACKGROUND,
		Fields::CUSTOM_CONSENT_PLACEHOLDER_COLOR_TEXT,
		Fields::CUSTOM_CONSENT_PLACEHOLDER_TEXT,
		Fields::CUSTOM_CONSENT_REVOKE,
		Fields::CUSTOM_CONSENT_TEXT,
		Fields::CUSTOM_CONSENT_TOOLTIP,
		Fields::CUSTOM_CSS,
		Fields::CUSTOM_HTML_BODY_END,
		Fields::CUSTOM_HTML_HEAD,
		Fields::CUSTOM_JS_BODY_END,
		Fields::CUSTOM_JS_HEAD,
		Fields::CUSTOM_OPEN_GRAPH_IMAGE_COLOR_BACKGROUND,
		Fields::CUSTOM_OPEN_GRAPH_IMAGE_COLOR_TEXT,
	);

	/**
	 * The shared entry.
	 */
	public SearchIndexEntry $sharedEntry;

	/**
	 * A map for page entries.
	 *
	 * @var array<string, SearchIndexEntry>
	 */
	private array $pageEntries = array();

	/**
	 * The index constructor only initializes an instance that can be passed around without doing actual intensive work.
	 *
	 * @param Page[] $pages
	 * @param Shared $Shared
	 * @param ComponentCollection $ComponentCollection
	 */
	public function __construct(private array $pages, private Shared $Shared, private ComponentCollection $ComponentCollection) {
		$this->sharedEntry = new SearchIndexEntry(null, null);
		$this->build();
	}

	/**
	 * Get a page entry by path.
	 *
	 * @param string $path
	 * @return SearchIndexEntry|null
	 */
	public function getPageEntry(string $path): ?SearchIndexEntry {
		return $this->pageEntries[$path] ?? null;
	}

	/**
	 * Add all valid non-ignored fields from a data array to an entry.
	 *
	 * @param SearchIndexEntry $entry
	 * @param array $data
	 */
	private function addData(SearchIndexEntry $entry, array $data): void {
		foreach ($data as $field => $raw) {
			if (preg_match('/^(:|date|checkbox|tags|color)/', $field) || in_array($field, SearchIndex::IGNORED)) {
				continue;
			}

			$value = '';

			if (str_starts_with($field, '+')) {
				try {
					$value = Blocks::toString($raw['blocks'], $this->ComponentCollection);
				} catch (Exception $error) {
					continue;
				}
			} else {
				$value = Value::asString($raw);
			}

			$entry->addField($field, $value);
		}
	}

	/**
	 * Build the index.
	 */
	private function build(): void {
		Debug::log('Building new search index');

		$this->addData($this->sharedEntry, $this->Shared->data);

		foreach ($this->pages as $Page) {
			$pageEntry = new SearchIndexEntry($Page->origUrl, $Page->path);

			$this->addData($pageEntry, $Page->data);
			$this->pageEntries[$Page->path] = $pageEntry;
		}
	}
}
