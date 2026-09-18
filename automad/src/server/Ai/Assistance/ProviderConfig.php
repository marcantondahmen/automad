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
use Automad\System\FileSystem;

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
	 * The current class name of the provider config type.
	 */
	private const TYPE = 'Automad\Ai\Assistance\ProviderConfig';

	/**
	 * The class name used by provider config files saved before the Ai\Assistance refactor.
	 */
	private const LEGACY_TYPE = 'Automad\Ai\ProviderConfig';

	/**
	 * The replacement for the provider config type class in a serialized string.
	 */
	private const TYPE_SERIALIZED = 'O:*:"~"';

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
	 * Load a provider config or create an empty one. The class name of a previously saved config
	 * is replaced with the current class name before unserializing, so that a future rename of
	 * this class doesn't break already saved config files (see save()).
	 *
	 * @param string $providerId
	 * @return ProviderConfig|null
	 */
	public static function load(string $providerId): ProviderConfig|null {
		$path = self::getPath($providerId);

		if (!is_readable($path)) {
			return null;
		}

		$serialized = str_replace(
			array(
				self::TYPE_SERIALIZED,
				'O:' . strlen(self::LEGACY_TYPE) . ':"' . self::LEGACY_TYPE . '"'
			),
			'O:' . strlen(self::TYPE) . ':"' . self::TYPE . '"',
			trim(strval(file_get_contents($path)))
		);

		try {
			$ProviderConfig = unserialize($serialized);
		} catch (\Throwable $th) {
			return null;
		}

		return $ProviderConfig instanceof self ? $ProviderConfig : null;
	}

	/**
	 * Save a config. The class name is replaced with a placeholder in order to be able to
	 * refactor this class in the future easily, without breaking already saved config files.
	 *
	 * @return bool
	 */
	public function save(): bool {
		$serialized = str_replace(
			'O:' . strlen(self::TYPE) . ':"' . self::TYPE . '"',
			self::TYPE_SERIALIZED,
			serialize($this)
		);

		return FileSystem::write(static::getPath($this->providerId), $serialized);
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
