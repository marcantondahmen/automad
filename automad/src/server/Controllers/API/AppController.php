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

use Automad\Admin\State;
use Automad\API\Response;
use Automad\API\ResponseCache;
use Automad\Core\Automad;
use Automad\Core\Cache;
use Automad\Core\FileSystem;
use Automad\Core\FileUtils;
use Automad\Core\Parse;
use Automad\Core\Session;
use Automad\Core\Str;
use Automad\Core\Text;
use Automad\Models\UserCollection;
use Automad\System\Composer;
use Automad\System\Fields;
use Automad\System\Theme;
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
		$ResponseCache = new ResponseCache(function () {
			$Response = new Response;
			$Automad = Automad::fromCache();

			return $Response->setData(array(
				'dashboard' => AM_BASE_INDEX . AM_PAGE_DASHBOARD,
				'languages' => self::getLanguages(),
				'reservedFields' => Fields::$reserved,
				'sitename' => $Automad->Shared->get(Fields::SITENAME),
				'text' => Text::getObject(),
				'version' => AM_VERSION
			));
		});

		return $ResponseCache->get();
	}

	/**
	 * Get server information
	 *
	 * @return Response the Response object
	 */
	public static function getServerInfo(): Response {
		$Response = new Response();

		return $Response->setData(array(
			'hostName' => gethostname(),
			'hostIp' => gethostbyname(gethostname()),
			'serverOs' => php_uname('s') . ' / ' . php_uname('r'),
			'serverSoftware' => getenv('SERVER_SOFTWARE'),
			'phpVersion' => phpversion(),
			'phpSapiName' => php_sapi_name(),
			'memoryLimit' => ini_get('memory_limit'),
		));
	}

	/**
	 * Send updated dynamic data that is required to update the dashboard app state.
	 *
	 * @return Response the Response object
	 */
	public static function updateState(): Response {
		// Close session here already in order to prevent blocking other requests.
		session_write_close();

		$ResponseCache = new ResponseCache(function () {
			$Response = new Response();
			$State = new State();

			return $Response->setData($State->get());
		});

		return $ResponseCache->get();
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
