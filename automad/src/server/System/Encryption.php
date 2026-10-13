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

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Encryption class encrypts and decrypts secrets that are stored in the config directory.
 * Encryption is only enabled when a key with a length of at least 32 characters is available.
 * The key is either defined directly using the AM_ENCRYPTION_KEY environment variable
 * or read from a plain text file whose path is defined using the AM_ENCRYPTION_KEY_FILE environment variable.
 * In case both are defined, AM_ENCRYPTION_KEY takes precedence. Relative key file paths are resolved
 * against the base directory, allowing for storing the key file outside of the document root.
 * The variables can be defined in the server environment, using SetEnv in .htaccess
 * or in the .env file during development.
 * The key is resolved only once during Config::init() and then kept in memory, since the key file
 * may not be readable anymore after the open_basedir restriction has been applied.
 * Note that the key is never defined as a constant in order to not leak it.
 *
 * @see FileSystem::writePhpString()
 * @see FileSystem::readPhpString()
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class Encryption {
	/**
	 * The name of the environment variable that contains the key.
	 */
	const ENV_KEY = 'AM_ENCRYPTION_KEY';

	/**
	 * The name of the environment variable that contains the path to the key file.
	 */
	const ENV_KEY_FILE = 'AM_ENCRYPTION_KEY_FILE';

	/**
	 * The minimum length of a valid key.
	 */
	const KEY_MIN_LENGTH = 32;

	/**
	 * The marker that all encrypted strings start with, independent of the version.
	 */
	const MARKER = 'amenc:';

	/**
	 * The prefix of strings encrypted with the current version.
	 */
	const PREFIX = 'amenc:v1:';

	/**
	 * The cipher method.
	 */
	private const CIPHER = 'aes-256-gcm';

	/**
	 * The context that is used for deriving the actual key.
	 */
	private const HKDF_INFO = 'automad-encryption';

	/**
	 * The length of the initialization vector.
	 */
	private const IV_LENGTH = 12;

	/**
	 * The length of the authentication tag.
	 */
	private const TAG_LENGTH = 16;

	/**
	 * Whether the key has already been loaded.
	 */
	private static bool $isLoaded = false;

	/**
	 * The derived key or null in case encryption is not available.
	 */
	private static ?string $key = null;

	/**
	 * Decrypt a string that was encrypted using encrypt().
	 * Null is returned in case the key is missing or wrong or the data is invalid.
	 * Note that this method must never write to the file system or log anything,
	 * since it is also used to read config files during Config::init().
	 *
	 * @param string $data
	 * @return string|null
	 */
	public static function decrypt(string $data): ?string {
		if (!str_starts_with($data, self::PREFIX)) {
			return null;
		}

		$key = self::getKey();

		if ($key === null) {
			return null;
		}

		$raw = base64_decode(substr($data, strlen(self::PREFIX)), true);

		if ($raw === false || strlen($raw) < self::IV_LENGTH + self::TAG_LENGTH) {
			return null;
		}

		$iv = substr($raw, 0, self::IV_LENGTH);
		$tag = substr($raw, self::IV_LENGTH, self::TAG_LENGTH);
		$ciphertext = substr($raw, self::IV_LENGTH + self::TAG_LENGTH);
		$plaintext = openssl_decrypt($ciphertext, self::CIPHER, $key, OPENSSL_RAW_DATA, $iv, $tag, self::PREFIX);

		return $plaintext === false ? null : $plaintext;
	}

	/**
	 * Encrypt a string. Null is returned in case encryption is not enabled.
	 *
	 * @param string $data
	 * @return string|null
	 */
	public static function encrypt(string $data): ?string {
		$key = self::getKey();

		if ($key === null) {
			return null;
		}

		$iv = random_bytes(self::IV_LENGTH);
		$tag = '';
		$ciphertext = openssl_encrypt($data, self::CIPHER, $key, OPENSSL_RAW_DATA, $iv, $tag, self::PREFIX, self::TAG_LENGTH);

		if ($ciphertext === false) {
			return null;
		}

		return self::PREFIX . base64_encode($iv . $tag . $ciphertext);
	}

	/**
	 * Test whether encryption is enabled, meaning that a valid key is set and the cipher is supported.
	 *
	 * @return bool
	 */
	public static function isEnabled(): bool {
		return self::getKey() !== null;
	}

	/**
	 * Test whether a string is encrypted.
	 *
	 * @param string $data
	 * @return bool
	 */
	public static function isEncrypted(string $data): bool {
		return str_starts_with($data, self::MARKER);
	}

	/**
	 * Resolve the key from the environment and keep the derived key in memory.
	 * This method is called once during Config::init() in order to read the key file
	 * before the open_basedir restriction is applied. It must be called again after changing
	 * the environment, for example in tests.
	 * Note that loading the key must never write or log anything,
	 * since it is also used to read config files during Config::init().
	 */
	public static function loadKey(): void {
		self::$isLoaded = true;
		self::$key = null;

		if (!function_exists('openssl_encrypt') || !in_array(self::CIPHER, openssl_get_cipher_methods())) {
			return;
		}

		$value = self::getEnv(self::ENV_KEY);

		if ($value === '') {
			$value = self::readKeyFile(self::getEnv(self::ENV_KEY_FILE));
		}

		if (strlen($value) < self::KEY_MIN_LENGTH) {
			return;
		}

		self::$key = hash_hkdf('sha256', $value, 32, self::HKDF_INFO);
	}

	/**
	 * Get the value of an environment variable. Variables are also looked up in $_SERVER,
	 * since FastCGI setups may only expose variables defined with SetEnv in .htaccess there,
	 * optionally with a "REDIRECT_" prefix after rewriting.
	 *
	 * @param string $name
	 * @return string
	 */
	private static function getEnv(string $name): string {
		$value = getenv($name);

		if (!is_string($value) || $value === '') {
			$value = $_SERVER[$name] ?? $_SERVER['REDIRECT_' . $name] ?? '';
		}

		return is_scalar($value) ? trim(strval($value)) : '';
	}

	/**
	 * Get the derived key or null in case encryption is not available.
	 * The key is loaded in case it hasn't been loaded before.
	 *
	 * @see loadKey()
	 * @return string|null
	 */
	private static function getKey(): ?string {
		if (!self::$isLoaded) {
			self::loadKey();
		}

		return self::$key;
	}

	/**
	 * Read the key from a file. Relative paths are resolved against the base directory.
	 * Errors are suppressed, since reading outside of the document root
	 * may be restricted by an open_basedir restriction of the hosting provider.
	 *
	 * @param string $path
	 * @return string the key or an empty string
	 */
	private static function readKeyFile(string $path): string {
		if ($path === '') {
			return '';
		}

		if (!str_starts_with($path, '/') && !str_starts_with($path, '\\') && !preg_match('#^[a-zA-Z]:[/\\\\]#', $path)) {
			$path = AM_BASE_DIR . '/' . $path;
		}

		if (!@is_readable($path) || !@is_file($path)) {
			return '';
		}

		return trim(strval(@file_get_contents($path)));
	}
}
