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
 * Copyright (c) 2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

namespace Automad\System;

use Automad\Core\Config;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The config data model.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class ConfigFile {
	/**
	 * The array with the loaded settings.
	 */
	private array $data;

	/**
	 * The configuration name.
	 */
	private string $name;

	/**
	 * The constructor.
	 *
	 * @param string $name
	 */
	public function __construct(string $name = '') {
		$this->data = Config::read($name);
		$this->name = $name;
	}

	/**
	 * Set a value in a given config array only it is not define in the server environment.
	 *
	 * @param string $key
	 * @param mixed $value
	 */
	public function set(string $key, mixed $value): void {
		if (!in_array($key, Config::$envKeys)) {
			$this->data[$key] = $value;
		}
	}

	/**
	 * Write the config file.
	 *
	 * @return bool
	 */
	public function write(): bool {
		ksort($this->data);

		return Config::write($this->data, $this->name);
	}
}
