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
 * Copyright (c) 2022-2023 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Controllers\API;

use Automad\API\Response;
use Automad\Core\Automad;
use Automad\Core\FileSystem;
use Automad\Core\FileUtils;
use Automad\Core\Parse;
use Automad\Core\Session;
use Automad\Core\Str;
use Automad\Core\Text;
use Automad\Models\UserCollection;
use Automad\System\Composer;
use Automad\System\Fields;
use Automad\System\ThemeCollection;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The App controller handles all requests related to the app state of the dashboard.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2022-2023 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class AppController {
	/**
	 * Send the minimum static data that is required to bootstrap the dashboard app.
	 *
	 * @return Response the Response object
	 */
	public static function bootstrap(): Response {
		$Response = new Response;
		$Automad = Automad::fromCache();

		return $Response->setData(array(
			'base' => AM_BASE_URL,
			'baseIndex' => AM_BASE_INDEX,
			'dashboard' => AM_BASE_INDEX . AM_PAGE_DASHBOARD,
			'languages' => self::getLanguages(),
			'reservedFields' => Fields::$reserved,
			'sitename' => $Automad->Shared->get(Fields::SITENAME),
			'text' => Text::getObject(),
			'packageRepo' => AM_PACKAGE_REPO,
			'version' => AM_VERSION
		));
	}

	/**
	 * Install or update the automad/language-packs package.
	 *
	 * @return Response the Response object
	 */
	public static function getLanguagePacks(): Response {
		// Close session here already in order to prevent blocking other requests.
		session_write_close();
		ignore_user_abort(true);

		$Response = new Response;
		$Composer = new Composer();
		$package = Text::PACKAGE;

		if (!is_readable(Text::LANG_PACKS_DIR)) {
			if (!$Composer->run("require --prefer-install=dist {$package}:dev-master")) {
				$Response->setSuccess(
					'Successfully installed the language pack extension! Reload the page in order to select another language in the system settings.'
				);
			}
		}

		$Composer->run("update {$package}");

		return $Response;
	}

	/**
	 * Send updated dynamic data that is required to update the dashboard app state.
	 *
	 * @return Response the Response object
	 */
	public static function updateState(): Response {
		// Close session here already in order to prevent blocking other requests.
		session_write_close();

		$Response = new Response;
		$Automad = Automad::fromCache();
		$UserCollection = new UserCollection();
		$ThemeCollection = new ThemeCollection();

		return $Response->setData(array(
			'allowedFileTypes' => FileUtils::allowedFileTypes(),
			'contentFields' => self::getContentFields(),
			'feed' => AM_SERVER . AM_BASE_INDEX . AM_FEED_URL,
			'mainTheme' => $Automad->Shared->get(Fields::THEME),
			'pages' => $Automad->getNavigationMetaData(),
			'sitename' => $Automad->Shared->get(Fields::SITENAME),
			'system' => array(
				'cache' => array(
					'enabled' => AM_CACHE_ENABLED,
					'lifetime' => AM_CACHE_LIFETIME,
					'monitorDelay' => AM_CACHE_MONITOR_DELAY
				),
				'debug' => AM_DEBUG_ENABLED,
				'feed' => array(
					'enabled' => AM_FEED_ENABLED,
					'fields' => Parse::csv(AM_FEED_FIELDS)
				),
				'translation' => AM_FILE_UI_TRANSLATION,
				'users'=> array_values($UserCollection->getCollection()),
				'tempDirectory' => FileSystem::getTmpDir()
			),
			'tags' => $Automad->getPagelist()->getTags(),
			'themes' => $ThemeCollection->getThemes(),
			'user' => $UserCollection->getUser(Session::getUsername())
		));
	}

	/**
	 * Get all relevant text based fields from all themes.
	 *
	 * @return array the fields array
	 */
	private static function getContentFields(): array {
		$ThemeCollection = new ThemeCollection();
		$fields = array();

		foreach ($ThemeCollection->getThemes() as $Theme) {
			foreach ($Theme->templates as $file) {
				$fields = array_merge($fields, Fields::inTemplate($file));
			}
		}

		$fields = array_unique($fields);
		$fields = array_filter($fields, function ($field) {
			return preg_match('/^(\+|text)/', $field);
		});

		return array_values($fields);
	}

	/**
	 * Get the array of installed languages.
	 *
	 * @return array the array of languages
	 */
	private static function getLanguages(): array {
		$languages = array('English' => '');

		foreach (glob(Text::LANG_PACKS_DIR . '/*.json') as $file) {
			$value = Str::stripStart($file, AM_BASE_DIR);
			$key = ucfirst(str_replace(array('_', '.json'), array(' ', ''), basename($file)));
			$languages[$key] = $value;
		}

		return $languages;
	}
}
