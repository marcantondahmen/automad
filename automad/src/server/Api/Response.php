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
 * Copyright (c) 2021-2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

namespace Automad\API;

use Automad\Core\Debug;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Response class.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021-2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class Response {
	/**
	 * The response code.
	 */
	private ?int $code = null;

	/**
	 * The main response data object.
	 */
	private ?array $data = null;

	/**
	 * The debug log array.
	 */
	private ?array $debug = null;

	/**
	 * The output buffer used for error notifications.
	 */
	private ?string $error = null;

	/**
	 * The exception array for all exceptions that are caught by the exception handler
	 * defined in Error::setJsonResponseHandler().
	 */
	private ?array $exception = null;

	/**
	 * The output buffer used to store a redirect URL.
	 */
	private ?string $redirect = null;

	/**
	 * The output buffer used to store the reload state.
	 */
	private ?bool $reload = null;

	/**
	 * The output buffer used to store the reload dialog text.
	 */
	private ?string $reloadDialog = null;

	/**
	 * The output buffer used for success notifications.
	 */
	private ?string $success = null;

	/**
	 * The output constructor.
	 */
	public function __construct() {
		$this->setCode(200);
	}

	/**
	 * Return a json encoded and filterd array of properties.
	 *
	 * @return string the json encoded array of response properties
	 */
	public function json(): string {
		$properties = array_filter(get_object_vars($this), function ($item) {
			return !is_null($item);
		});

		return strval(json_encode($properties, JSON_UNESCAPED_SLASHES));
	}

	/**
	 * Set the response code.
	 *
	 * @see $code
	 * @param int $code
	 * @return Response
	 */
	public function setCode(int $code): Response {
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
	public function setData(array $data): Response {
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
	public function setDebug(array $log): Response {
		if (!empty($log) && Debug::$browserIsEnabled) {
			$this->debug = $log;
		}

		return $this;
	}

	/**
	 * Set the error property.
	 *
	 * @see $error
	 * @param string $value
	 * @return Response
	 */
	public function setError(string $value = ''): Response {
		$this->error = $value;

		return $this;
	}

	/**
	 * Set the exception property.
	 *
	 * @see $exception
	 * @param array $value
	 * @return Response
	 */
	public function setException(array $value = array()): Response {
		$this->exception = $value;

		return $this;
	}

	/**
	 * Set the redirect property.
	 *
	 * @see $redirect
	 * @param string $url
	 * @return Response
	 */
	public function setRedirect(string $url = ''): Response {
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
	public function setReload(bool $value): Response {
		$this->reload = $value;

		return $this;
	}

	/**
	 * Set the reloadDialog property.
	 *
	 * @see $reloadDialog
	 * @param string $value
	 * @return Response
	 */
	public function setReloadDialog(string $value): Response {
		$this->reloadDialog = $value;

		return $this;
	}

	/**
	 * Set the success property.
	 *
	 * @see $success
	 * @param string $value
	 * @return Response
	 */
	public function setSuccess(string $value = ''): Response {
		$this->success = $value;

		return $this;
	}
}
