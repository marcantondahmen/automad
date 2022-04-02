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
 * Copyright (c) 2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\UI;

use Automad\Core\Debug;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Response class.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Response {
	/**
	 * The output buffer for autocomplete JSON data.
	 */
	private $autocomplete = null;

	/**
	 * The output buffer used by Composer commands.
	 */
	private $buffer = null;

	/**
	 * The output buffer used by CLI commands.
	 */
	private $cli = null;

	/**
	 * The debug output buffer.
	 */
	private $debug = null;

	/**
	 * The output buffer used for error notifications.
	 */
	private $error = null;

	/**
	 * The main rendered output of a view or component.
	 */
	private $html = null;

	/**
	 * The output buffer used to store a redirect URL.
	 */
	private $redirect = null;

	/**
	 * The output buffer used to store the reload state.
	 */
	private $reload = null;

	/**
	 * The status output buffer.
	 */
	private $status = null;

	/**
	 * The output buffer used for success notifications.
	 */
	private $success = null;

	/**
	 * The output buffer used to store the event name that is triggered after sending a response.
	 */
	private $trigger = null;

	/**
	 * The output constructor.
	 */
	public function __construct() {
		Debug::log('Instanciated new Response instance');
	}

	/**
	 * Get the buffer property.
	 *
	 * @see $buffer
	 * @return string $buffer
	 */
	public function getBuffer() {
		return $this->buffer;
	}

	/**
	 * Get the cli property.
	 *
	 * @see $cli
	 * @return string $cli
	 */
	public function getCli() {
		return $this->cli;
	}

	/**
	 * Get the error property.
	 *
	 * @see $error
	 * @return string $error
	 */
	public function getError() {
		return $this->error;
	}

	/**
	 * Return a json encoded and filterd array of properties.
	 *
	 * @return string the json encoded array of response properties
	 */
	public function json() {
		$properties = array_filter(get_object_vars($this));

		return json_encode($properties, JSON_UNESCAPED_SLASHES);
	}

	/**
	 * Set the autocomplete property.
	 *
	 * @see $autocomplete
	 * @param array $values
	 */
	public function setAutocomplete(array $values) {
		$this->autocomplete = $values;
	}

	/**
	 * Set the buffer property.
	 *
	 * @see $buffer
	 * @param string $buffer
	 */
	public function setBuffer(string $buffer = '') {
		$this->buffer = $buffer;
	}

	/**
	 * Set the cli property.
	 *
	 * @see $cli
	 * @param string $value
	 */
	public function setCli(string $value = '') {
		$this->cli = $value;
	}

	/**
	 * Set the debug property.
	 *
	 * @see $debug
	 * @param array $log
	 */
	public function setDebug(array $log) {
		$this->debug = $log;
	}

	/**
	 * Set the error property.
	 *
	 * @see $error
	 * @param string $value
	 */
	public function setError(string $value = '') {
		$this->error = $value;
	}

	/**
	 * Set the html property.
	 *
	 * @see $html
	 * @param string $html
	 */
	public function setHtml(string $html = '') {
		$this->html = $html;
	}

	/**
	 * Set the redirect property.
	 *
	 * @see $redirect
	 * @param string $url
	 */
	public function setRedirect(string $url = '') {
		$this->redirect = $url;
	}

	/**
	 * Set the reload property.
	 *
	 * @see $reload
	 * @param bool $value
	 */
	public function setReload(bool $value) {
		$this->reload = $value;
	}

	/**
	 * Set the status property.
	 *
	 * @see $status
	 * @param string $value
	 */
	public function setStatus(string $value = '') {
		$this->status = $value;
	}

	/**
	 * Set the success property.
	 *
	 * @see $success
	 * @param string $value
	 */
	public function setSuccess(string $value = '') {
		$this->success = $value;
	}

	/**
	 * Set the trigger property.
	 *
	 * @see $trigger
	 * @param string $value
	 */
	public function setTrigger(string $value) {
		$this->trigger = $value;
	}
}
