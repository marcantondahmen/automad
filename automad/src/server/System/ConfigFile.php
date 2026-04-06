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
use Automad\Core\Debug;
use Automad\Core\FileSystem;

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
		$this->data = ConfigFile::read($name);
		$this->name = $name;
	}

	/**
	 * Read configuration overrides as JSON string form PHP or JSON file
	 * and decode the returned string. Note that now the configuration is stored in
	 * PHP files instead of JSON files to make it less easy to access from outside.
	 *
	 * @param string $name
	 * @return array The configuration array
	 */
	public static function read(string $name = ''): array {
		$json = false;
		$file = self::getConfigPath($name);

		try {
			if (is_readable($file)) {
				ob_start();
				$json = require $file;
				ob_clean();
			}

			if ($json) {
				$config = json_decode($json, true);

				if (is_array($config)) {
					return $config;
				}
			}
		} catch (\Throwable $th) {
		}

		return array();
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
	 * Remove a key.
	 *
	 * @param string $key
	 */
	public function unset(string $key): void {
		unset($this->data[$key]);
	}

	/**
	 * Write the config file.
	 *
	 * @return bool
	 */
	public function write(): bool {
		ksort($this->data);

		$json = json_encode($this->data, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
		$content = "<?php return <<< JSON\r\n$json\r\nJSON;\r\n";
		$file = ConfigFile::getConfigPath($this->name);
		$success = FileSystem::write($file, $content);

		Debug::log($file);

		if ($success && function_exists('opcache_invalidate')) {
			opcache_invalidate($file, true);
		}

		return $success;
	}

	/**
	 * Get the config path for a given optional name.
	 *
	 * @param string $name
	 * @return string
	 */
	private static function getConfigPath(string $name): string {
		return $name ? AM_BASE_DIR . '/config/config.' . $name . '.php' : Config::FILE;
	}
}
