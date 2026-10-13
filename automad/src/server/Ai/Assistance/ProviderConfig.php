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

namespace Automad\Ai\Assistance;

use Automad\Core\Str;
use Automad\System\SerializedFile;

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
		self::getFile($this->providerId)->delete();
	}

	/**
	 * Load a provider config.
	 *
	 * @param string $providerId
	 * @return ProviderConfig|null
	 */
	public static function load(string $providerId): ProviderConfig|null {
		$ProviderConfig = self::getFile($providerId)->read();

		return $ProviderConfig instanceof self ? $ProviderConfig : null;
	}

	/**
	 * Save a config.
	 *
	 * @return bool
	 */
	public function save(): bool {
		return self::getFile($this->providerId)->write($this);
	}

	/**
	 * Get the encrypted serialized file of a provider config.
	 *
	 * @see SerializedFile
	 * @param string $providerId
	 * @return SerializedFile
	 */
	private static function getFile(string $providerId): SerializedFile {
		return new SerializedFile(self::getPath($providerId), self::class, true);
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
