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

namespace Automad\Admin\Controllers;

use Automad\Admin\API\Response;
use Automad\Admin\Models\AppModel;
use Automad\Admin\Models\UserCollectionModel;
use Automad\Admin\Session;
use Automad\Admin\UI\Utils\Text;
use Automad\Core\Cache;
use Automad\Core\FileSystem;
use Automad\Core\FileUtils;
use Automad\Core\Parse;
use Automad\Core\Str;
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
		$ThemeCollection = new ThemeCollection();

		return $Response->setData(array(
			'appId' => Session::setAppId(),
			'version' => AM_VERSION,
			'text' => Text::getObject(),
			'themes' => $ThemeCollection->getThemes(),
			'base' => AM_BASE_URL,
			'baseIndex' => AM_BASE_INDEX,
			'dashboard' => AM_BASE_INDEX . AM_PAGE_DASHBOARD,
			'feed' => Server::url() . AM_BASE_INDEX . AM_FEED_URL,
			'reservedFields' => Fields::$reserved,
			'allowedFileTypes' => FileUtils::allowedFileTypes(),
			'languages' => self::getLanguages(),
			'contentFields' => self::getContentFields()
		));
	}

	/**
	 * Send updated dynamic data that is required to update the dashboard app state.
	 *
	 * @return Response the Response object
	 */
	public static function updateState() {
		$Response = new Response;
		$Cache = new Cache();
		$Automad = $Cache->getAutomad();
		$UserCollectionModel = new UserCollectionModel();

		return $Response->setData(array(
			'tags' => $Automad->getPagelist()->getTags(),
			'pages' => AppModel::pages($Automad),
			'sitename' => $Automad->Shared->get(AM_KEY_SITENAME),
			'mainTheme' => $Automad->Shared->get(AM_KEY_THEME),
			'user' => $UserCollectionModel->getUser(Session::getUsername()),
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
				'users'=> $UserCollectionModel->getCollection(),
				'tempDirectory' => FileSystem::getTmpDir()
			)
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
