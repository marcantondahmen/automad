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
 * Copyright (c) 2023 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Core;

use Automad\API\RequestHandler;
use Automad\System\Fields;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The I18n class is responsible for language detection.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2023 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class I18n {
	/**
	 * Available languages.
	 */
	private array $available = array();

	/**
	 * The singelton instance.
	 */
	private static I18n|null $instance = null;

	/**
	 * The active language.
	 */
	private string $lang = '';

	/**
	 * The constructor.
	 */
	private function __construct() {
		if (!AM_I18N_ENABLED || $this->isDashboard()) {
			Debug::log('Disable i18n for dashboard and API');

			return;
		}

		$available = array_map(
			function ($item) {
				return basename($item);
			},
			FileSystem::glob(AM_BASE_DIR . AM_DIR_PAGES . '/*', GLOB_ONLYDIR)
		);

		$serverLang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '', 0, 2);
		$urlLang = substr(trim(AM_REQUEST, '/'), 0, 2);
		$lang = AM_I18N_DEFAULT_LANG;

		if (in_array($urlLang, $available)) {
			$lang = $urlLang;
		} elseif (in_array($serverLang, $available)) {
			$lang = $serverLang;
		}

		if (!$lang) {
			$lang = $available[0];
		}

		$this->lang = $lang;
		$this->available = $available;

		Debug::log(array('lang' => $this->lang, 'available' => $this->available));
	}

	/**
	 * Apply structural modifications to the homepage in order to automatically set the correct context for navigations.
	 *
	 * @param Automad $Automad
	 */
	public function apply(Automad $Automad): void {
		if (!AM_I18N_ENABLED || $this->isDashboard()) {
			return;
		}

		$Home = $Automad->getPage('/');
		$localizedHome = $Automad->getPage("/$this->lang");

		if (!$Home) {
			return;
		}

		$Home->data[Fields::URL] = "/$this->lang";
		$Home->data[Fields::ORIG_URL] = "/$this->lang";

		if (!$localizedHome) {
			return;
		}

		$Home->data[Fields::TITLE] = $localizedHome->get(Fields::TITLE);
	}

	/**
	 * Return the singelton instance.
	 *
	 * @return I18n the singelton instance
	 */
	public static function get(): I18n {
		if (!I18n::$instance) {
			I18n::$instance = new I18n();
		}

		return I18n::$instance;
	}

	/**
	 * Return the active language.
	 *
	 * @return string the active language
	 */
	public function getLanguage(): string {
		return $this->lang;
	}

	/**
	 * Test whether a path can be added to the collection.
	 *
	 * @param string $path
	 * @return bool
	 */
	public function isInCurrentLang(string $path): bool {
		if (!AM_I18N_ENABLED || $path == '/' || $this->isDashboard()) {
			return true;
		}

		return str_starts_with(trim($path, '/'), $this->lang);
	}

	/**
	 * Test whether request is targeting the dashboard or the API.
	 *
	 * @return bool true if it is a dashboard or API request
	 */
	private function isDashboard(): bool {
		return AM_REQUEST == AM_PAGE_DASHBOARD || str_starts_with(AM_REQUEST, RequestHandler::$apiBase);
	}
}
