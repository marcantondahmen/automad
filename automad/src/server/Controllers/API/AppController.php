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
 * Copyright (c) 2022 by Marc Anton Dahmen
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
use Automad\System\Fields;
use Automad\System\Server;
use Automad\System\ThemeCollection;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The App controller handles all requests related to the app state of the dashboard.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2022 Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class AppController {
	/**
	 * Send the minimum static data that is required to bootstrap the dashboard app.
	 *
	 * @return Response the Response object
	 */
	public static function bootstrap() {
		$Response = new Response;
		$Automad = Automad::fromCache();

		return $Response->setData(array(
			'base' => AM_BASE_URL,
			'baseIndex' => AM_BASE_INDEX,
			'dashboard' => AM_BASE_INDEX . AM_PAGE_DASHBOARD,
			'languages' => self::getLanguages(),
			'reservedFields' => Fields::$reserved,
			'sitename' => $Automad->Shared->get(AM_KEY_SITENAME),
			'text' => Text::getObject(),
			'version' => AM_VERSION
		));
	}

	/**
	 * Send updated dynamic data that is required to update the dashboard app state.
	 *
	 * @return Response the Response object
	 */
	public static function updateState() {
		$Response = new Response;
		$Automad = Automad::fromCache();
		$UserCollection = new UserCollection();
		$ThemeCollection = new ThemeCollection();

		return $Response->setData(array(
			'allowedFileTypes' => FileUtils::allowedFileTypes(),
			'contentFields' => self::getContentFields(),
			'feed' => Server::url() . AM_BASE_INDEX . AM_FEED_URL,
			'mainTheme' => $Automad->Shared->get(AM_KEY_THEME),
			'pages' => $Automad->getNavigationMetaData(),
			'sitename' => $Automad->Shared->get(AM_KEY_SITENAME),
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
	private static function getContentFields() {
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
	private static function getLanguages() {
		$languages = array();

		foreach (glob(dirname(AM_FILE_UI_TEXT_MODULES) . '/*.txt') as $file) {
			if (strpos($file, 'english.txt') !== false) {
				$value = '';
			} else {
				$value = Str::stripStart($file, AM_BASE_DIR);
			}

			$key = ucfirst(str_replace(array('_', '.txt'), array(' ', ''), basename($file)));
			$languages[$key] = $value;
		}

		return $languages;
	}
}