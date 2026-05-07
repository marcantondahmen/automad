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

use Automad\Core\FileSystem;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * A password reset token handler.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class PasswordResetToken {
	const LIFETIME = 300;
	const TOKEN_DIR = AM_DIR_TMP . '/password_reset_tokens';

	/**
	 * The timestamp when the token was created.
	 */
	public readonly int $expires;

	/**
	 * The token hash.
	 */
	public readonly string $tokenHash;

	/**
	 * The constructor.
	 *
	 * @param string $username
	 * @param string $token
	 */
	public function __construct(string $username, string $token) {
		$this->expires = time() + PasswordResetToken::LIFETIME;
		$this->tokenHash = password_hash($token . $username, PASSWORD_DEFAULT);

		$this->write($username);
	}

	/**
	 * A static method that generates a random 48 character token.
	 *
	 * @return string
	 */
	public static function generate(): string {
		return bin2hex(random_bytes(32));
	}

	/**
	 * Reset the token object in the user session.
	 *
	 * @param string $username
	 */
	public static function reset(string $username): void {
		$path = self::getTokenFilePath($username);

		if (is_readable($path)) {
			unlink($path);
		}
	}

	/**
	 * Verify a token/username combination.
	 *
	 * @param string $username
	 * @param string $token
	 * @return bool
	 */
	public static function verify(string $username, string $token): bool {
		$PasswordResetToken = self::read($username);

		if (is_null($PasswordResetToken)) {
			return false;
		}

		if ($PasswordResetToken->expires < time()) {
			return false;
		}

		if (!password_verify($token . $username, $PasswordResetToken->tokenHash)) {
			return false;
		}

		return true;
	}

	/**
	 * Get the path to the token file.
	 *
	 * @param string $username
	 * @return string
	 */
	private static function getTokenFilePath(string $username): string {
		return self::TOKEN_DIR . '/' . sha1($username);
	}

	/**
	 * Read the token instance.
	 *
	 * @param string $username
	 * @return PasswordResetToken|null
	 */
	private static function read(string $username): PasswordResetToken|null {
		$path = self::getTokenFilePath($username);

		if (!is_readable($path)) {
			return null;
		}

		if ($contents = file_get_contents($path)) {
			try {
				return unserialize($contents);
			} catch (\Throwable $th) {
				self::reset($username);
			}
		}

		return null;
	}

	/**
	 * Write the token to disk.
	 *
	 * @param string $username
	 */
	private function write(string $username): void {
		FileSystem::write(PasswordResetToken::getTokenFilePath($username), serialize($this));
	}
}
