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
 * Copyright (c) 2016-2023 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Controllers\API;

use Automad\API\Response;
use Automad\Core\Messenger;
use Automad\Core\Request;
use Automad\Core\Text;
use Automad\Models\UserCollection;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The user collection class provides all methods for creating and loading user accounts.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2016-2023 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class UserCollectionController {
	/**
	 * Add user account based on $_POST.
	 *
	 * @return Response the response object
	 */
	public static function createUser(): Response {
		$Response = new Response();
		$Messenger = new Messenger();

		$username = Request::post('username');
		$password1 = Request::post('password1');
		$password2 = Request::post('password2');
		$email = Request::post('email');

		$UserCollection = new UserCollection();

		if (!$UserCollection->createUser($username, $password1, $password2, $email, $Messenger)) {
			return $Response->setError($Messenger->getError());
		}

		if (!$UserCollection->save($Messenger)) {
			return $Response->setError($Messenger->getError());
		}

		return $Response->setSuccess(Text::get('addedSuccess') . ' "' . $username . '"');
	}

	/**
	 * Optionally remove posted accounts and
	 * return the accounts grid.
	 *
	 * @return Response the response object
	 */
	public static function edit(): Response {
		$Response = new Response();
		$Messenger = new Messenger();
		$UserCollection = new UserCollection();

		if ($users = Request::post('delete')) {
			if (!is_array($users)) {
				return $Response;
			}

			$users = array_keys($users);

			if ($UserCollection->delete($users, $Messenger)) {
				$Response->setSuccess(Text::get('deteledSuccess') . ' "' . implode('", "', $users) . '"');
			} else {
				$Response->setError($Messenger->getError());
			}
		}

		return $Response;
	}

	/**
	 * Install the first user account.
	 *
	 * @return string Error message in case of an error
	 */
	/*


	public static function install() {
		if (empty($_POST)) {
			return '';
		}

		$UserCollection = new UserCollection();
		$Messenger = new Messenger();

		if (!$UserCollection->createUser(
			Request::post('username'),
			Request::post('password1'),
			Request::post('password2'),
			Request::post('email'),
			$Messenger
		)) {
			return $Messenger->getError();
		}

		header('Expires: -1');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Content-Type: application/octet-stream');
		header('Content-Transfer-Encoding: binary');
		header('Content-Disposition: attachment; filename=' . basename(UserCollection::FILE_ACCOUNTS));
		ob_end_flush();

		exit($UserCollection->generatePHP());
	}


	 */

	/**
	 * Invite a new user by email.
	 *
	 * @return Response a response object
	 */
	public static function inviteUser(): Response {
		$Response = new Response();
		$UserCollection = new UserCollection();
		$Messenger = new Messenger();

		$username = trim(Request::post('username'));
		$email = trim(Request::post('email'));
		$password = str_shuffle(sha1(microtime()));

		if (!$UserCollection->createUser($username, $password, $password, $email, $Messenger)) {
			return $Response->setError($Messenger->getError());
		}

		if (!$UserCollection->save($Messenger)) {
			return $Response->setError($Messenger->getError());
		}

		if (!$UserCollection->sendInvitation($username, $email, $Messenger)) {
			return $Response->setError($Messenger->getError());
		}

		return $Response->setSuccess(Text::get('systemUsersSendInvitationSuccess'));
	}
}
