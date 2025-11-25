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

namespace Automad\Models;

use Automad\API\RequestHandler;
use Automad\Core\Cache;
use Automad\Core\Debug;
use Automad\Core\Error;
use Automad\Core\Messenger;
use Automad\Core\PublicationState;
use Automad\Core\Session;
use Automad\Core\Text;
use Automad\Core\Value;
use Automad\Stores\DataStore;
use Automad\System\Fields;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Shared class represents a collection of all shared site-wide data.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2016-2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Shared {
	/**
	 * The shared data array.
	 */
	public array $data = array();

	/**
	 * Parse the shared data file.
	 */
	public function __construct() {
		// Use the server name as default site name.
		$defaults = array(
			Fields::SITENAME => $_SERVER['SERVER_NAME'] ?? 'Site'
		);

		$DataStore = new DataStore();

		// Merge defaults with settings from file.
		$this->data = array_merge(
			$defaults,
			$DataStore->getState(empty(Session::getUsername())) ?? array()
		);

		Debug::log(array('Defaults' => $defaults, 'Shared Data' => $this->data));

		// Check whether there is a theme defined in the Shared object data.
		if (!$this->get(Fields::THEME) && strpos(AM_REQUEST, RequestHandler::API_BASE) !== 0) {
			Error::exit('No main theme defined', 'Please define a theme in our shared settings.');
		}
	}

	/**
	 * Return requested value.
	 *
	 * @param string $key
	 * @return string The requested value
	 */
	public function get(string $key): string {
		if (array_key_exists($key, $this->data)) {
			return Value::asString($this->data[$key]);
		}

		return '';
	}

	/**
	 * The resolved filesystem path to the data file.
	 *
	 * @return string
	 */
	public static function getFile(): string {
		$DataStore = new DataStore();

		return $DataStore->getFile();
	}

	/**
	 * Publish shared settings.
	 *
	 * @param Messenger $Messenger
	 */
	public function publish(Messenger $Messenger): void {
		$DataStore = new DataStore();
		$published = $DataStore->getState(PublicationState::PUBLISHED) ?? array();
		$draft = $DataStore->getState(PublicationState::DRAFT) ?? array();

		$publishedTheme = $published[Fields::THEME] ?? '';
		$publishedSitename = $published[Fields::SITENAME] ?? '';
		$draftTheme = $draft[Fields::THEME] ?? '';
		$draftSitename = $draft[Fields::SITENAME] ?? '';

		if (!$DataStore->publish()) {
			$Messenger->setError(Text::get('error_permission'));

			return;
		}

		$Messenger->setSuccess(Text::get('publishedSuccessfully'));

		Cache::clear();
	}

	/**
	 * Save shared data.
	 *
	 * @param array $data
	 * @param Messenger $Messenger
	 * @return bool true on in case a reload is required
	 */
	public function save(array $data, Messenger $Messenger): bool {
		$DataStore = new DataStore();

		$draft = $DataStore->getState(PublicationState::DRAFT);
		$DataStore->setState(PublicationState::DRAFT, $data);

		if (!$DataStore->save()) {
			$Messenger->setError(Text::get('permissionsDeniedError'));

			return false;
		}

		Cache::clear();

		if (($draft[Fields::THEME] ?? '') != ($data[Fields::THEME] ?? '')) {
			return true;
		}

		return false;
	}

	/**
	 * Set key/value pair in data.
	 *
	 * @param string $key
	 * @param mixed $value
	 */
	public function set(string $key, $value): void {
		$this->data[$key] = $value;
	}
}
