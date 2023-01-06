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
 * Copyright (c) 2016-2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\System;

use Automad\Engine\PatternAssembly;
use Automad\Models\Page;
use Automad\System\Theme;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Fields class provides all methods to search all kind of content variables (fields of the data array) used in templates.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2016-2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Fields {
	/**
	 * Array with reserved variable fields.
	 */
	public static $reserved = array(
		'AM_KEY_DATE' => AM_KEY_DATE,
		'AM_KEY_HIDDEN' => AM_KEY_HIDDEN,
		'AM_KEY_PRIVATE' => AM_KEY_PRIVATE,
		'AM_KEY_TAGS' => AM_KEY_TAGS,
		'AM_KEY_THEME' => AM_KEY_THEME,
		'AM_KEY_TITLE' => AM_KEY_TITLE,
		'AM_KEY_SITENAME' => AM_KEY_SITENAME,
		'AM_KEY_URL' => AM_KEY_URL
	);

	/**
	 * Find all variable fields in the currently used template and all included snippets (and ignore those fields in $this->reserved).
	 *
	 * @param Page $Page
	 * @param Theme|null $Theme
	 * @return array fields in the currently used template (without reserved fields)
	 */
	public static function inCurrentTemplate(Page $Page, ?Theme $Theme = null) {
		if (empty($Theme)) {
			return array();
		}

		// Don't use $Page->getTemplate() to prevent exit on errors.
		$file = AM_BASE_DIR . AM_DIR_PACKAGES . '/' . $Page->get(AM_KEY_THEME) . '/' . $Page->template . '.php';
		$fields = self::inTemplate($file);

		return self::cleanUp($fields, $Theme->getMask('page'));
	}

	/**
	 * Find all variable fields in a template and all included snippets (and ignore those fields in $this->reserved).
	 *
	 * @param string $file
	 * @return array fields in a given template (without reserved fields)
	 */
	public static function inTemplate(string $file) {
		$fields = array();

		if (is_readable($file)) {
			// Find all variable fields in the template file.
			$content = file_get_contents($file);
			// Remove ~ characters to match includes correctly.
			$content = str_replace(
				array(AM_DEL_STATEMENT_OPEN . '~', '~' . AM_DEL_STATEMENT_CLOSE),
				array(AM_DEL_STATEMENT_OPEN, AM_DEL_STATEMENT_CLOSE),
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
	public static function inTheme(Theme $Theme) {
		$fields = array();

		foreach ($Theme->templates as $file) {
			$fields = array_merge($fields, self::inTemplate($file));
		}

		return self::cleanUp($fields, $Theme->getMask('shared'));
	}

	/**
	 * Cleans up an array of fields. All reserved and duplicate fields get removed
	 * and the optional UI mask is applied.
	 *
	 * @param array $fields
	 * @param array $mask
	 * @return array The sorted and filtered fields array
	 */
	private static function cleanUp(array $fields, array $mask = array()) {
		if (empty($fields)) {
			return array();
		}

		if (!empty($mask)) {
			$fields = array_filter($fields, function ($key) use ($mask) {
				return !in_array($key, $mask);
			});
		}

		return array_unique(array_diff($fields, array_values(self::$reserved)));
	}
}
