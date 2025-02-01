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
 * Copyright (c) 2016-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Core;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Text class provides all methods related to the text modules used in the UI.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2016-2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Text {
	const FILE_MODULES = AM_BASE_DIR . '/automad/lang/english.json';
	const LANG_PACKS_DIR = AM_BASE_DIR . '/lib/vendor/automad/language-packs/lang';
	const PACKAGE = 'automad/language-packs';

	/**
	 * Array of UI text modules.
	 */
	private static ?array $modules = null;

	/**
	 * Return the requested text module.
	 *
	 * @param string $key
	 * @return string The requested text module
	 */
	public static function get(string $key): string {
		self::loadModules();

		if (isset(self::$modules[$key])) {
			return self::$modules[$key];
		}

		return '';
	}

	/**
	 * Return the modules as object to be used in heredoc strings.
	 *
	 * @return object The modules array as object
	 */
	public static function getObject(): object {
		self::loadModules();

		return (object) self::$modules;
	}

	/**
	 * Parse the text modules file and store all modules in self::$modules.
	 * In case AM_FILE_UI_TRANSLATION is defined, the translated text modules
	 * will be merged into Text:$modules.
	 */
	private static function loadModules(): void {
		if (self::$modules) {
			return;
		}

		self::$modules = FileSystem::readJson(Text::FILE_MODULES);

		if (AM_FILE_UI_TRANSLATION) {
			$translationFile = AM_BASE_DIR . AM_FILE_UI_TRANSLATION;

			if (is_readable($translationFile)) {
				$translation = FileSystem::readJson($translationFile);

				self::$modules = array_merge(self::$modules, $translation);
			}
		}

		array_walk(self::$modules, function (string &$item) {
			$item = Str::markdown($item, true);
			// Remove all line breaks to avoid problems when using text modules in JS notify.
			$item = str_replace(array("\n", "\r"), '', $item);
		});

		Debug::log('Parsed text modules');
	}
}
