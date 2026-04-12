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
 * Copyright (c) 2016-2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

namespace Automad\Controllers\API;

use Automad\API\Response;
use Automad\Auth\LoginRateLimiter;
use Automad\Auth\Session;
use Automad\Auth\TOTP;
use Automad\Auth\User;
use Automad\Core\Messenger;
use Automad\Core\Request;
use Automad\Core\Text;
use Automad\Models\UserCollection;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The User class provides all methods related to a user account.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2016-2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class UserController {
	/**
	 * Reset a user password by email.
	 *
	 * @return Response the Response object
	 */
	public static function accountRecovery(): Response {
		$Response = new Response();
		$UserCollection = new UserCollection();
		$Messenger = new Messenger();

		// Only one field will be defined, so they can just be concatenated here.
		$nameOrEmail = trim(Request::post('name-or-email') . Request::post('username'));

		$token = trim(Request::post('token'));
		$newPassword1 = Request::post('password1');
		$newPassword2 = Request::post('password2');

		$User = $UserCollection->getUser($nameOrEmail);

		if ($nameOrEmail && !$User) {
			return $Response->setError(Text::get('userNotFoundError'));
		}

		if (!$User) {
			return $Response->setData(array('state' => 'requestToken'));
		}

		$responseData = array('username' => $User->name);

		if ($token && $newPassword1 && $newPassword2) {
			if (!self::verifyPasswordRequirements($newPassword1)) {
				$responseData['state'] = 'setPassword';

				return $Response->setData($responseData)->setError(self::generatePasswordRequirementsError());
			}

			if ($User->verifyPasswordResetToken($token)) {
				if ($User->resetPassword($newPassword1, $newPassword2, $UserCollection, $Messenger)) {
					$responseData['state'] = 'success';

					LoginRateLimiter::reset($User->name);

					return $Response->setData($responseData);
				}

				$responseData['state'] = 'setPassword';

				return $Response->setData($responseData)->setError($Messenger->getError());
			}

			$responseData['state'] = 'setPassword';

			return $Response->setData($responseData)->setError(Text::get('passwordResetVerificationError'));
		}

		if ($User->sendPasswordResetToken($Messenger)) {
			$responseData['state'] = 'setPassword';

			return $Response->setData($responseData);
		}

		return $Response->setError($Messenger->getError());
	}

	/**
	 * Change the password of the currently logged in user based on $_POST.
	 *
	 * @return Response the response object
	 */
	public static function changePassword(): Response {
		$Response = new Response();
		$currentPassword = Request::post('currentPassword');
		$newPassword = Request::post('newPassword1');

		if (!$currentPassword || !$newPassword) {
			return $Response->setError(Text::get('invalidFormError'));
		}

		if ($newPassword !== Request::post('newPassword2')) {
			return $Response->setError(Text::get('passwordRepeatError'));
		}

		if ($currentPassword === $newPassword) {
			return $Response->setError(Text::get('passwordReuseError'));
		}

		if (!self::verifyPasswordRequirements($newPassword)) {
			return $Response->setError(self::generatePasswordRequirementsError());
		}

		$UserCollection = new UserCollection();
		$User = $UserCollection->getUser(Session::getUsername());
		$Messenger = new Messenger();

		if (!$User) {
			return $Response->setError(Text::get('userNotFoundError'));
		}

		$User->changePassword($currentPassword, $newPassword, $UserCollection, $Messenger);

		return $Response
				->setError($Messenger->getError())
				->setSuccess($Messenger->getSuccess());
	}

	/**
	 * Edit user account info such as username and email.
	 *
	 * @return Response the response
	 */
	public static function edit(): Response {
		$Response = new Response();
		$Messenger = new Messenger();
		$UserCollection = new UserCollection();

		$username = Request::post('username');
		$email = Request::post('email');

		if ($UserCollection->editCurrentUserInfo($username, $email, $Messenger)) {
			if ($UserCollection->save($Messenger)) {
				return $Response->setSuccess(Text::get('savedSuccess'));
			}
		}

		return $Response->setError($Messenger->getError());
	}

	/**
	 * Confirm TOTP setup.
	 *
	 * @return Response
	 */
	public static function totpConfirmSetup(): Response {
		$Response = new Response();
		$Messenger = new Messenger();
		$code = Request::post('code');

		$confirmed = TOTP::confirmSetup($code, $Messenger);

		return $Response->setError($Messenger->getError())->setData(array('confirmed' => $confirmed));
	}

	/**
	 * Disable TOTP verification.
	 *
	 * @return Response
	 */
	public static function totpDisable(): Response {
		$Response = new Response();
		$Messenger = new Messenger();

		if (!Request::post('disableTotp')) {
			return $Response->setCode(403);
		}

		$disabled = TOTP::disable($Messenger);

		if (!$disabled) {
			$Response->setError($Messenger->getError())->setCode(500);
		}

		return $Response->setError($Messenger->getError())->setData(array('disabled' => $disabled));
	}

	/**
	 * Test if a TOTP is configured for the current user.
	 *
	 * @return Response
	 */
	public static function totpIsConfigured(): Response {
		$Response = new Response();
		$User = User::getCurrent();

		if (!$User) {
			return $Response;
		}

		return $Response->setData(array('totpIsConfigured' => $User->totpIsConfigured()));
	}

	/**
	 * Start the TOTP setup process.
	 *
	 * @return Response
	 */
	public static function totpSetup(): Response {
		$Response = new Response();
		$User = User::getCurrent();

		if (empty($User)) {
			return $Response;
		}

		$Response->setData(TOTP::setup($User->name));

		return $Response;
	}

	/**
	 * Generate a password requirements error message.
	 *
	 * @return string
	 */
	private static function generatePasswordRequirementsError(): string {
		/** @var string */
		$chars = str_replace(' ', ', ', AM_PASSWORD_REQUIRED_CHARS);

		return str_replace(array('{1}', '{2}'), array($chars, AM_PASSWORD_MIN_LENGTH), Text::get('passwordRequirementsError'));
	}

	/**
	 * Verify that a given password matches the requirements.
	 *
	 * @param string $password
	 * @return bool
	 */
	private static function verifyPasswordRequirements(string $password): bool {
		$charGroups = preg_split('/\s+/', AM_PASSWORD_REQUIRED_CHARS);
		$len = is_numeric(AM_PASSWORD_MIN_LENGTH) ? AM_PASSWORD_MIN_LENGTH : 0;

		if (!$charGroups) {
			$charGroups = array();
		}

		$regex = '';

		foreach ($charGroups as $group) {
			$regex .= '(?=.*[' . $group . '])';
		}

		$regex .= '.{' . intval(AM_PASSWORD_MIN_LENGTH) . ',}';

		return (bool) preg_match('/' . $regex . '/', $password);
	}
}
