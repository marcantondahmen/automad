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

namespace Automad\System\Ai;

use Automad\Core\FileSystem;
use Automad\Core\Str;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The provider config.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class ProviderConfig {
	/**
	 * The api key.
	 */
	public string $apiKey;

	/**
	 * The selected model.
	 */
	public string $model;

	/**
	 * The provider id.
	 */
	public string $providerId;

	/**
	 * The constyructor.
	 *
	 * @param string $providerId
	 */
	public function __construct(string $providerId) {
		$this->providerId = $providerId;
		$this->apiKey = '';
		$this->model = '';
	}

	/**
	 * Delete the config.
	 */
	public function delete(): void {
		$path = self::getPath($this->providerId);

		if (is_readable($path)) {
			unlink($path);
		}
	}

	/**
	 * Load a provider config or create an empty one.
	 *
	 * @param string $providerId
	 * @return ProviderConfig|null
	 */
	public static function load(string $providerId): ProviderConfig|null {
		$path = self::getPath($providerId);

		if (!is_readable($path)) {
			return null;
		}

		try {
			return unserialize(strval(file_get_contents($path)));
		} catch (\Throwable $th) {
			return null;
		}
	}

	/**
	 * Save a config.
	 *
	 * @return bool
	 */
	public function save(): bool {
		return FileSystem::write(static::getPath($this->providerId), serialize($this));
	}

	/**
	 * Get the path to a provider config.
	 *
	 * @param string $providerId
	 * @return string
	 */
	private static function getPath(string $providerId): string {
		$providerId = Str::sanitize($providerId, true);

		return AM_BASE_DIR . "/config/ai.$providerId.php";
	}
}
