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
 * Copyright (c) 2016-2024 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\System;

use Automad\Engine\Delimiters;
use Automad\Engine\PatternAssembly;
use Automad\Models\Page;
use Automad\System\Theme;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Fields class provides all methods to search all kind of content variables (fields of the data array) used in templates.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2016-2024 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Fields {
	const BASENAME = ':basename';
	const CAPTION = ':caption';
	const CURRENT_PAGE = ':current';
	const CURRENT_PATH = ':currentPath';
	const CUSTOM_CSS = 'customCSS';
	const CUSTOM_HTML_BODY_END = 'customHTMLBodyEnd';
	const CUSTOM_HTML_HEAD = 'customHTMLHead';
	const CUSTOM_JS_BODY_END = 'customJSBodyEnd';
	const CUSTOM_JS_HEAD = 'customJSHead';
	const DATE = 'date';
	const FILE = ':file';
	const FILE_RESIZED = ':fileResized';
	const FILELIST_COUNT = ':filelistCount';
	const FILTER = ':filter';
	const HEIGHT = ':height';
	const HEIGHT_RESIZED = ':heightResized';
	const HIDDEN = 'hidden';
	const LANG = ':lang';
	const LEVEL = ':level';
	const LOOP_INDEX = ':i';
	const NOW = ':now';
	const ORIG_URL = ':origUrl';
	const PAGE_INDEX = ':index';
	const PAGELIST_COUNT = ':pagelistCount';
	const PAGELIST_DISPLAY_COUNT = ':pagelistDisplayCount';
	const PAGINATION_COUNT = ':paginationCount';
	const PARENT = ':parent';
	const PATH = ':path';
	const PRIVATE = 'private';
	const PUBLICATION_STATE = ':publicationState';
	const SEARCH_CONTEXT = ':searchContext';
	const SITENAME = 'sitename';
	const SLUG = 'slug';
	const SYNTAX_THEME = 'syntaxTheme';
	const TAG = ':tag';
	const TAGS = 'tags';
	const TEMPLATE = 'template';
	const THEME = 'theme';
	const TIME_CREATED = ':created';
	const TIME_LAST_MODIFIED = ':lastModified';
	const TIME_LAST_PUBLISHED = ':lastPublished';
	const TITLE = 'title';
	const URL = 'url';
	const WIDTH = ':width';
	const WIDTH_RESIZED = ':widthResized';

	/**
	 * Array with reserved variable fields.
	 */
	public static array $reserved = array(
		'CUSTOM_CSS' => Fields::CUSTOM_CSS,
		'CUSTOM_HTML_BODY_END' => Fields::CUSTOM_HTML_BODY_END,
		'CUSTOM_HTML_HEAD' => Fields::CUSTOM_HTML_HEAD,
		'CUSTOM_JS_BODY_END' => Fields::CUSTOM_JS_BODY_END,
		'CUSTOM_JS_HEAD' => Fields::CUSTOM_JS_HEAD,
		'DATE' => Fields::DATE,
		'HIDDEN' => Fields::HIDDEN,
		'PRIVATE' => Fields::PRIVATE,
		'PUBLICATION_STATE' => Fields::PUBLICATION_STATE,
		'SITENAME' => Fields::SITENAME,
		'SLUG' => Fields::SLUG,
		'SYNTAX_THEME' => Fields::SYNTAX_THEME,
		'TAGS' => Fields::TAGS,
		'TEMPLATE' => Fields::TEMPLATE,
		'THEME' => Fields::THEME,
		'TIME_CREATED' => Fields::TIME_CREATED,
		'TIME_LAST_MODIFIED' => Fields::TIME_LAST_MODIFIED,
		'TIME_LAST_PUBLISHED' => Fields::TIME_LAST_PUBLISHED,
		'TITLE' => Fields::TITLE,
		'URL' => Fields::URL,
	);

	/**
	 * Find all variable fields in the currently used template and all included snippets (and ignore those fields in $this->reserved).
	 *
	 * @param Page $Page
	 * @param Theme|null $Theme
	 * @return array fields in the currently used template (without reserved fields)
	 */
	public static function inCurrentTemplate(Page $Page, ?Theme $Theme = null): array {
		if (empty($Theme)) {
			return array();
		}

		// Don't use $Page->getTemplate() to prevent exit on errors.
		$file = AM_BASE_DIR . AM_DIR_PACKAGES . '/' . $Page->get(Fields::THEME) . '/' . $Page->template . '.php';
		$fields = self::inTemplate($file);

		return self::cleanUp($fields, $Theme->getMask('page'), $Theme->fieldOrder);
	}

	/**
	 * Find all variable fields in a template and all included snippets (and ignore those fields in $this->reserved).
	 *
	 * @param string $file
	 * @return array fields in a given template (without reserved fields)
	 */
	public static function inTemplate(string $file): array {
		$fields = array();

		if (is_readable($file)) {
			// Find all variable fields in the template file.
			$content = file_get_contents($file);
			// Remove ~ characters to match includes correctly.
			$content = str_replace(
				array(Delimiters::STATEMENT_OPEN . '~', '~' . Delimiters::STATEMENT_CLOSE),
				array(Delimiters::STATEMENT_OPEN, Delimiters::STATEMENT_CLOSE),
				$content
			);
			preg_match_all('/' . PatternAssembly::variableKeyUI() . '/is', $content, $matches);
			$fields = $matches['varName'];

			// Match markup to get includes recursively.
			preg_match_all('/' . PatternAssembly::template() . '/is', $content, $matches, PREG_SET_ORDER);

			foreach ($matches as $match) {
				// Recursive include.
				if (!empty($match['file'])) {
					$include = dirname($file) . '/' . $match['file'];

					if (file_exists($include)) {
						$fields = array_merge($fields, self::inTemplate($include));
					}
				}
			}

			$fields = self::cleanUp($fields);
		}

		return $fields;
	}

	/**
	 * Find all variable fields in templates of a given theme.
	 *
	 * @param Theme $Theme
	 * @return array fields in all templates of the given Theme (without reserved fields)
	 */
	public static function inTheme(Theme $Theme): array {
		$fields = array();

		foreach ($Theme->templates as $file) {
			$fields = array_merge($fields, self::inTemplate($file));
		}

		return self::cleanUp($fields, $Theme->getMask('shared'), $Theme->fieldOrder);
	}

	/**
	 * Cleans up an array of fields. All reserved and duplicate fields get removed
	 * and the optional UI mask is applied. Fields are sorted according to a field order
	 * array.
	 *
	 * @param array $fields
	 * @param array $mask
	 * @param array $fieldOrder
	 * @return array The sorted and filtered fields array
	 */
	private static function cleanUp(array $fields, array $mask = array(), array $fieldOrder = array()): array {
		if (empty($fields)) {
			return array();
		}

		$fields = array_unique(array_diff($fields, array_values(self::$reserved)));

		if (!empty($mask)) {
			$fields = array_filter($fields, function ($key) use ($mask) {
				return !in_array($key, $mask);
			});
		}

		if (!empty($fieldOrder)) {
			$fields = array_keys(array_merge(array_fill_keys($fieldOrder, true), array_fill_keys($fields, true)));
		} else {
			sort($fields);
		}

		return $fields;
	}
}
