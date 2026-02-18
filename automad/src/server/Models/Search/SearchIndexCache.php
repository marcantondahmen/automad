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

use Automad\Core\Cache;
use Automad\Core\Debug;
use Automad\Core\FileSystem;
use Automad\Core\Session;
use Automad\Models\ComponentCollection;
use Automad\Models\Page;
use Automad\Models\Shared;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The search index caching engine.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class SearchIndexCache {
	const FILE_ADMIN = AM_DIR_TMP . '/index_admin';
	const FILE_PUBLIC = AM_DIR_TMP . '/index_public';

	/**
	 * The index cache constructor only initializes an instance that can be passed around without doing actual intensive work.
	 *
	 * @param Page[] $pages
	 * @param Shared $Shared
	 * @param ComponentCollection $ComponentCollection
	 */
	public function __construct(private array $pages, private Shared $Shared, private ComponentCollection $ComponentCollection) {
	}

	/**
	 * Get the index from cache or build a fresh one.
	 *
	 * @return SearchIndex
	 */
	public function getIndex(): SearchIndex {
		if (!AM_CACHE_ENABLED) {
			return $this->buildIndex();
		}

		$Cache = new Cache();
		$siteMTime = $Cache->getSiteMTime();
		$path = Session::getUsername() ? SearchIndexCache::FILE_ADMIN : SearchIndexCache::FILE_PUBLIC;
		$indexMTime = is_readable($path) ? intval(filemtime($path)) : 0;

		Debug::log($path, 'Search index caching path');

		if (is_readable($path) && $siteMTime < $indexMTime && time() < $indexMTime + AM_CACHE_LIFETIME) {
			Debug::log('Loading search index from cache');

			try {
				return unserialize(strval(file_get_contents($path)));
			} catch (\Throwable $th) {
				Debug::log('Error loading search index from cache');
			}
		}

		$SearchIndex = $this->buildIndex();

		FileSystem::write($path, serialize($SearchIndex));

		return $SearchIndex;
	}

	/**
	 * Build a fresh index.
	 *
	 * @return SearchIndex
	 */
	private function buildIndex(): SearchIndex {
		return new SearchIndex($this->pages, $this->Shared, $this->ComponentCollection);
	}
}
