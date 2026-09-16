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

namespace Automad\Auth;

use Automad\System\FileSystem;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The access token config. Stores the access tokens issued for this installation,
 * e.g. for connecting to Automad's MCP server, similar to a collection of personal access tokens.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class AccessTokenConfig {
	/**
	 * The current class name of the access token config type.
	 */
	private const TYPE = 'Automad\Auth\AccessTokenConfig';

	/**
	 * The replacement for the access token config type class in a serialized string.
	 */
	private const TYPE_SERIALIZED = 'O:*:"~"';

	/**
	 * The issued access tokens.
	 */
	public array $tokens = array();

	/**
	 * Add a new token.
	 *
	 * @param array $token
	 */
	public function addToken(array $token): void {
		$this->tokens[] = $token;
	}

	/**
	 * Find a token by its hash.
	 *
	 * @param string $hash
	 * @return array|null
	 */
	public function findTokenByHash(string $hash): array|null {
		foreach ($this->tokens as $token) {
			if (hash_equals($token['tokenHash'], $hash)) {
				return $token;
			}
		}

		return null;
	}

	/**
	 * Load the config or create an empty one. The class name of a previously saved config
	 * is replaced with the current class name before unserializing, so that a future rename of
	 * this class doesn't break already saved config files (see save()).
	 *
	 * @return AccessTokenConfig
	 */
	public static function load(): AccessTokenConfig {
		$path = self::getPath();

		if (is_readable($path)) {
			$serialized = str_replace(
				self::TYPE_SERIALIZED,
				'O:' . strlen(self::TYPE) . ':"' . self::TYPE . '"',
				trim(strval(file_get_contents($path)))
			);

			try {
				$AccessTokenConfig = unserialize($serialized);

				if ($AccessTokenConfig instanceof AccessTokenConfig) {
					return $AccessTokenConfig;
				}
			} catch (\Throwable $th) {
			}
		}

		return new AccessTokenConfig();
	}

	/**
	 * Remove a token.
	 *
	 * @param string $id
	 */
	public function removeToken(string $id): void {
		$this->tokens = array_values(array_filter(
			$this->tokens,
			function ($token) use ($id) {
				return $token['id'] !== $id;
			}
		));
	}

	/**
	 * Save the config. The class name is replaced with a placeholder in order to be able to
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

		return FileSystem::write(self::getPath(), $serialized);
	}

	/**
	 * Get the path to the config file.
	 *
	 * @return string
	 */
	private static function getPath(): string {
		return AM_BASE_DIR . '/config/tokens.auth.php';
	}
}
