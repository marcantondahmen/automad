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
use Automad\Core\Messenger;
use Automad\Core\Text;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * A password reset code handler.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class PasswordResetCode {
	const CODE_DIR = AM_DIR_TMP . '/password_reset_codes';
	const LIFETIME = 600;
	const MAX_ATTEMPTS = 5;

	/**
	 * The number of failed attempts.
	 */
	public int $attempts = 0;

	/**
	 * The code hash.
	 */
	public readonly string $codeHash;

	/**
	 * The timestamp when the code was created.
	 */
	public readonly int $expires;

	/**
	 * The constructor.
	 *
	 * @param string $username
	 * @param string $code
	 */
	public function __construct(string $username, string $code) {
		$this->expires = time() + PasswordResetCode::LIFETIME;
		$this->codeHash = password_hash($code . $username, PASSWORD_DEFAULT);

		$this->write($username);
	}

	/**
	 * A static method that generates a random 8 character long code with
	 * character that can't be mixed up, skipping 0, O, 1, and I,
	 * for better readability.
	 *
	 * @return string
	 */
	public static function generate(): string {
		$alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

		$code = '';

		for ($i = 0; $i < 8; $i++) {
			$code .= $alphabet[random_int(0, 31)];
		}

		return $code;
	}

	/**
	 * Register when a wrong code has been used for a given user.
	 *
	 * @param string $username
	 */
	public static function registerFailure(string $username): void {
		$PasswordResetCode = self::read($username);

		if (is_null($PasswordResetCode)) {
			return;
		}

		$PasswordResetCode->attempts++;
		$PasswordResetCode->write($username);
	}

	/**
	 * Reset the code object in the user session.
	 *
	 * @param string $username
	 */
	public static function reset(string $username): void {
		$path = self::getCodeFilePath($username);

		if (is_readable($path)) {
			unlink($path);
		}
	}

	/**
	 * Verify a code/username combination.
	 *
	 * @param string $username
	 * @param string $code
	 * @param Messenger $Messanger
	 * @return bool
	 */
	public static function verify(string $username, string $code, Messenger $Messanger): bool {
		$PasswordResetCode = self::read($username);

		if (is_null($PasswordResetCode)) {
			$Messanger->setError(Text::get('passwordResetCodeInvalidError'));

			return false;
		}

		if ($PasswordResetCode->attempts >= self::MAX_ATTEMPTS) {
			$Messanger->setError(Text::get('passwordResetCodeFailedAttemptsLimit'));

			return false;
		}

		if ($PasswordResetCode->expires < time()) {
			self::reset($username);
			$Messanger->setError(Text::get('passwordResetCodeInvalidError'));

			return false;
		}

		if (!password_verify($code . $username, $PasswordResetCode->codeHash)) {
			self::registerFailure($username);
			$Messanger->setError(Text::get('passwordResetCodeInvalidError'));

			return false;
		}

		return true;
	}

	/**
	 * Get the path to the code file.
	 *
	 * @param string $username
	 * @return string
	 */
	private static function getCodeFilePath(string $username): string {
		return self::CODE_DIR . '/' . sha1($username);
	}

	/**
	 * Read the code instance.
	 *
	 * @param string $username
	 * @return PasswordResetCode|null
	 */
	private static function read(string $username): PasswordResetCode|null {
		$path = self::getCodeFilePath($username);

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
	 * Write the code to disk.
	 *
	 * @param string $username
	 */
	private function write(string $username): void {
		FileSystem::write(PasswordResetCode::getCodeFilePath($username), serialize($this));
	}
}
