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
 * Copyright (c) 2021-2022 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Admin\API;

use Automad\Core\Debug;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Response class.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021-2022 Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Response {
	/**
	 * The response code.
	 */
	private $code = null;

	/**
	 * The main response data object.
	 */
	private $data = null;

	/**
	 * The debug output buffer.
	 */
	private $debug = null;

	/**
	 * The output buffer used for error notifications.
	 */
	private $error = null;

	/**
	 * The output buffer used to store a redirect URL.
	 */
	private $redirect = null;

	/**
	 * The output buffer used to store the reload state.
	 */
	private $reload = null;

	/**
	 * The output buffer used for success notifications.
	 */
	private $success = null;

	/**
	 * The response time.
	 */
	private $time = null;

	/**
	 * The output constructor.
	 */
	public function __construct() {
		Debug::log('Instanciated new Response instance');

		$this->setCode(200);
		$this->time = time();
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
	 * Set the response code.
	 *
	 * @see $code
	 * @param int $code
	 * @return Response
	 */
	public function setCode(int $code) {
		$this->code = $code;
		http_response_code($code);

		return $this;
	}

	/**
	 * Set the response data.
	 *
	 * @see $data
	 * @param array $data
	 * @return Response
	 */
	public function setData(array $data) {
		$this->data = $data;

		return $this;
	}

	/**
	 * Set the debug property.
	 *
	 * @see $debug
	 * @param array $log
	 * @return Response
	 */
	public function setDebug(array $log) {
		$this->debug = $log;

		return $this;
	}

	/**
	 * Set the error property.
	 *
	 * @see $error
	 * @param string $value
	 * @return Response
	 */
	public function setError(string $value = '') {
		$this->error = $value;

		return $this;
	}

	/**
	 * Set the redirect property.
	 *
	 * @see $redirect
	 * @param string $url
	 * @return Response
	 */
	public function setRedirect(string $url = '') {
		$this->redirect = $url;

		return $this;
	}

	/**
	 * Set the reload property.
	 *
	 * @see $reload
	 * @param bool $value
	 * @return Response
	 */
	public function setReload(bool $value) {
		$this->reload = $value;

		return $this;
	}

	/**
	 * Set the success property.
	 *
	 * @see $success
	 * @param string $value
	 * @return Response
	 */
	public function setSuccess(string $value = '') {
		$this->success = $value;

		return $this;
	}
}
