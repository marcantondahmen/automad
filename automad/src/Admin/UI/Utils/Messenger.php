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

namespace Automad\Admin\UI\Utils;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Messenger object allows for pushing error messages to the calling method in order to separate return values from error details.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Messenger {
	/**
	 * Misc data array.
	 */
	private $data = array();

	/**
	 * The last pushed error.
	 */
	private $error = '';

	/**
	 * The last pushed success.
	 */
	private $success = '';

	/**
	 * The messenger constructor.
	 */
	public function __construct() {
	}

	/**
	 * Return the stored data array.
	 *
	 * @return array the data array
	 */
	public function getData() {
		return $this->data;
	}

	/**
	 * Return the stored error message.
	 *
	 * @return string the error message
	 */
	public function getError() {
		return $this->error;
	}

	/**
	 * Return the stored success message.
	 *
	 * @return string the success message
	 */
	public function getSuccess() {
		return $this->success;
	}

	/**
	 * Set the data array.
	 *
	 * @param array $data
	 */
	public function setData(array $data) {
		$this->data = $data;
	}

	/**
	 * Set the last error.
	 *
	 * @param string $message
	 */
	public function setError(string $message) {
		$this->error = $message;
	}

	/**
	 * Set the last success message.
	 *
	 * @param string $message
	 */
	public function setSuccess(string $message) {
		$this->success = $message;
	}
}
