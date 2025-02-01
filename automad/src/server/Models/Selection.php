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
 * Copyright (c) 2013-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Models;

use Automad\Core\Debug;
use Automad\Core\I18n;
use Automad\Core\Parse;
use Automad\Core\Str;
use Automad\Models\Search\Search;
use Automad\System\Fields;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Selection class holds all methods to filter and sort the collection of pages and return them as a new selection.
 *
 * Every instance can return a filtered and sorted array of pages without hurting the original Automad object.
 * That means the Automad class object has to be created only once.
 * To get multiple different (sorted and filtered) collections, this class can be used by just passing the collection array.
 *
 * All the filter function directly modify $this->selection. After all modifications to that selection,
 * it can be returned once by $this->getSelection().
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2013-2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Selection {
	/**
	 * Initially holds the whole collection.
	 *
	 * $selection is basically the internal working copy of the collection array.
	 * It can be sorted and filtered without hurting the original collection.
	 *
	 * @var array<Page>
	 */
	private array $selection = array();

	/**
	 * Pass a set of pages to $this->selection excluding all hidden pages.
	 *
	 * @param array $pages
	 */
	public function __construct(array $pages) {
		$this->selection = $pages;
	}

	/**
	 * Exclude the current page from the selection.
	 */
	public function excludeCurrent(): void {
		$this->excludePage(AM_REQUEST);
	}

	/**
	 * Remove a page from the selection.
	 *
	 * @param string $url
	 */
	public function excludePage(string $url): void {
		if ($url && array_key_exists($url, $this->selection)) {
			unset($this->selection[$url]);
		}
	}

	/**
	 * Collect all pages along a given URL.
	 *
	 * @param string $url
	 */
	public function filterBreadcrumbs(string $url): void {
		$I18n = I18n::get();
		$lang = $I18n->getLanguage();
		$home = "/$lang";

		// Test whether $url is the URL of a real page.
		// "Real" pages have a URL (not like search or error pages) and they exist in the selection array (not hidden).
		// For all other $url, just the home page will be returned.
		if (strpos($url, '/') === 0 && array_key_exists($url, $this->selection)) {
			$pages = array();

			// While $url is not the home page, strip each segement one by one and
			// add the corresponding Page object to $pages.
			while ($url != $home) {
				$pages[$url] = $this->selection[$url];
				$url = '/' . trim(substr($url, 0, (int) strrpos($url, '/')), '/');
			}

			// Add home page
			$pages[$home] = $this->selection[$home];

			// Reverse the $pages array and pass it to $this->selection.
			$this->selection = array_reverse($pages);
		} else {
			// If $url is not a valid URL, only add the home page to the selection.
			// This might be the case for "virtual pages", like the "error" or "search results" pages,
			// which don't have a $page->url.
			$this->selection = array($this->selection[$home]);
		}
	}

	/**
	 * Filter $this->selection by multiple keywords (a search string), if $str is not empty.
	 *
	 * @param string $str
	 */
	public function filterByKeywords(string $str): void {
		if (!$str) {
			return;
		}

		$filtered = $this->selection;
		$keywords = explode(' ', str_replace('/', ' ', Str::stripTags($str)));

		foreach ($keywords as $keyword) {
			$Search = new Search($keyword, false, false, $filtered, null);
			$fileResultsArray = $Search->searchPerFile();
			$filtered = array();

			foreach ($fileResultsArray as $FileResult) {
				$context = array();

				foreach ($FileResult->fieldResultsArray as $FieldResult) {
					$context[] = $FieldResult->context;
				}

				if ($FileResult->url) {
					$Page = $this->selection[$FileResult->url];
					$Page->set(Fields::SEARCH_CONTEXT, implode(' ... ', $context));
					$filtered[$FileResult->url] = $Page;
				}
			}
		}

		$this->selection = $filtered;
	}

	/**
	 * Filter $this->selection by relative url of the parent page.
	 *
	 * @param string $parent
	 */
	public function filterByParentUrl(string $parent): void {
		$filtered = array();

		foreach ($this->selection as $key => $Page) {
			// Use identical comparison operator (===) here to avoid getting all pages in case $parent is set true.
			if ($Page->parentUrl === $parent) {
				$filtered[$key] = $Page;
			}
		}

		$this->selection = $filtered;
	}

	/**
	 * Filter $this->selection by tag.
	 *
	 * @param string $tag
	 */
	public function filterByTag(string $tag): void {
		if ($tag) {
			$filtered = array();

			foreach ($this->selection as $key => $Page) {
				if (in_array($tag, $Page->tags)) {
					$filtered[$key] = $Page;
				}
			}

			$this->selection = $filtered;
		}
	}

	/**
	 * Filter $this->selection by template. A regex can be used as filter string.
	 * For example passing 'page|home' as parameter will include all pages with a template that
	 * contains 'page' or 'home' as substrings.
	 *
	 * @param string $regex
	 */
	public function filterByTemplate(string $regex): void {
		if ($regex) {
			$filtered = array();

			foreach ($this->selection as $key => $Page) {
				if (preg_match('/(' . $regex . ')/i', $Page->template)) {
					$filtered[$key] = $Page;
				}
			}

			$this->selection = $filtered;
		}
	}

	/**
	 * Only include pages that have the current language.
	 */
	public function filterCurrentLanguage(): void {
		if (!AM_I18N_ENABLED) {
			return;
		}

		$I18n = I18n::get();
		$filtered = array();

		foreach ($this->selection as $key => $Page) {
			if ($I18n->isInCurrentLang($Page->path)) {
				$filtered[$key] = $Page;
			}
		}

		$this->selection = $filtered;
	}

	/**
	 * Filter out the non-hidden neighbors (previous and next page) to the passed URL.
	 *
	 * $this->selection only holds two pages after completion with the keys ['prev'] and ['next'] instead of the URL-key.
	 * If there is only one page in the array (has no siblings), the selection will be empty. For two pages, it will only
	 * contain the ['next'] page. For more than two pages, both neighbors will be set in the selection.
	 *
	 * @param string $url
	 */
	public function filterPrevAndNextToUrl(string $url): void {
		if (array_key_exists($url, $this->selection)) {
			// To be able to hide the hidden pages as neighbors and jump directly to the closest non-hidden pages (both sides),
			// in case one or both neigbors is/are hidden, $this->excludeHidden() has to be called here already, because only excluding the hidden pages
			// later, when calling getSelection(), will cause a "gap" in the neighbors-array, which will lead to a missing link, for a hidden neighbor.
			// To keep the correct position of the current page within the selection, even if the current page itself is hidden,
			// $Page-hidden has to be set temporary to false.
			$Page = $this->selection[$url];
			// Cache the original value for $Page->hidden.
			$hiddenCache = $Page->hidden;
			$Page->hidden = false;
			$this->excludeHidden();
			// Restore the original value for $Page->hidden.
			$Page->hidden = $hiddenCache;

			$keys = array_keys($this->selection);
			$keyIndexes = array_flip($keys);
			$currentIndex = $keyIndexes[$url];

			$neighbors = array();

			if ($currentIndex > 0) {
				$neighbors['prev'] = $this->selection[$keys[$currentIndex - 1]];

				// Exclude home page when i18n language routing is enabled.
				if ($neighbors['prev']->level == -1) {
					unset($neighbors['prev']);
				}
			}

			if ($currentIndex < count($keys) - 1) {
				$neighbors['next'] = $this->selection[$keys[$currentIndex + 1]];
			}

			$this->selection = $neighbors;
		}
	}

	/**
	 * Filter all pages having one or more tag in common with $Page. If there are not tags defined for the passed page,
	 * the selection will be an empty array. (no tags = no related pages)
	 *
	 * @param Page $Page
	 */
	public function filterRelated(Page $Page): void {
		$tags = $Page->tags;

		$filtered = array();

		if ($tags) {
			foreach ($tags as $tag) {
				foreach ($this->selection as $key => $p) {
					if (in_array($tag, $p->tags)) {
						$filtered[$key] = $p;
					}
				}
			}
		}

		$this->selection = $filtered;
		$this->excludePage($Page->url);
	}

	/**
	 * 	Return the array with the selected (filtered and sorted) pages.
	 *
	 * @param bool $excludeHidden
	 * @param bool $excludeCurrent
	 * @param int $offset
	 * @param int|null $limit
	 * @return array $this->selection
	 */
	public function getSelection(
		bool $excludeHidden = true,
		bool $excludeCurrent = false,
		int $offset = 0,
		?int $limit = null
	): array {
		if ($excludeHidden) {
			$this->excludeHidden();
		}

		if ($excludeCurrent) {
			$this->excludeCurrent();
		}

		return array_slice($this->selection, $offset, $limit);
	}

	/**
	 * While iterating a set of variable/regex combinations in $options, all pages where
	 * a given variable is not matching its assigned regex are removed from the selection.
	 *
	 * @param array|null $options
	 */
	public function match(?array $options): void {
		if (empty($options)) {
			return;
		}

		foreach ($options as $key => $regex) {
			if (@preg_match($regex, '') !== false) {
				$this->selection = array_filter(
					$this->selection,
					function ($Page) use ($key, $regex) {
						return preg_match($regex, $Page->get($key));
					}
				);
			}
		}
	}

	/**
	 * Sorts $this->selection based on a sorting options string.
	 *
	 * The option string consists of multiple pairs of
	 * a data key and a sort order, separated by a comma like this:
	 * $Selection->sortPages('date desc, title asc')
	 * The above example will sort first all pages in the selection by 'date' (descending) and then by 'title' (ascending).
	 *
	 * Valid values for the order are 'asc' and 'desc'.
	 * In case a sort order is missing in a key/order combination, the 'asc' is used as a fallback.
	 *
	 * @param string|null $options
	 */
	public function sortPages(?string $options = null): void {
		$sort = array();
		$parameters = array();

		// Define default option in case an empty string gets passed.
		if (!$options) {
			$options = Fields::PAGE_INDEX . ' asc';
		}

		// Parse options string.

		// First create an array out of single key/order combinations (separated by comma).
		$pairs = Parse::csv($options);

		// Append the default sorting order to each pair and create subarrays out of the first two space-separated items.
		foreach ($pairs as $pair) {
			// Add default order to avoid having a string without a given order
			// and convert the first two separate strings into variables ($key and $order).
			// If there is already an order, the default will simply be ignored as the third parameter.
			list($key, $order) = explode(' ', $pair . ' asc');

			// Set order to the default order, if its value is invalid.
			if (!in_array($order, array('asc', 'desc'))) {
				$order = 'asc';
			}

			// Create the actual subarray and convert the order into the real constant value.
			$sort[] = array('key' => $key, 'order' => constant(strtoupper('sort_' . $order)));
		}

		// Add the values to sort by to each sort array.
		foreach ($sort as $i => $sortItem) {
			$sort[$i]['values'] = array();

			foreach ($this->selection as $Page) {
				$sort[$i]['values'][] = trim(strtolower(Str::stripTags($Page->get($sortItem['key']))));
			}
		}

		// Build parameters and call array_multisort function.
		foreach ($sort as $sortItem) {
			$parameters[] = $sortItem['values'] ?? '';
			$parameters[] = $sortItem['order'];
			$parameters[] = SORT_NATURAL;
		}

		Debug::log($parameters, 'Parameters');

		$parameters[] = &$this->selection;
		call_user_func_array('array_multisort', $parameters);
	}

	/**
	 * Exclude all hidden pages from the selection.
	 */
	private function excludeHidden(): void {
		foreach ($this->selection as $url => $Page) {
			if ($Page->hidden) {
				unset($this->selection[$url]);
			}
		}
	}
}
