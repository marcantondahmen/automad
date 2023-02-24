<?php

namespace Automad\Traits;

trait ValidatePasswordTrait
{
	private static string $message;

	/**
	 * Validate Password
	 *
	 * @param string $password1
	 * @param string $password2
	 * @return bool
	 */
	public static function validatePassword(string $password1, string $password2): bool {

		$uppercase    = preg_match('/[A-Z]/', $password1);
		$lowercase    = preg_match('/[a-z]/', $password1);
		$number       = preg_match('/[0-9]/', $password1);
		$specialChars = preg_match('/[^\w]/', $password1);

		if (strlen($password1) < 8) {
			self::$message = 'passwordStrengthErrorCharacterCount';

			return false;
		}

		if (!$specialChars) {
			self::$message = 'passwordStrengthErrorSpecialCharacter';

			return false;
		}

		if (!$number) {
			self::$message = 'passwordStrengthErrorNumber';

			return false;
		}

		if (!$lowercase) {
			self::$message = 'passwordStrengthErrorLowercase';

			return false;
		}

		if (!$uppercase) {
			self::$message = 'passwordStrengthErrorUppercase';

			return false;
		}

		if ($password1 != $password2) {
			self::$message = 'repeatPassword';

			return false;
		}

		return true;
	}

	/**
	 * Returns error string
	 *
	 * @return string
	 */
	public static function getPasswordError(): string {
		return self::$message;
	}
}
