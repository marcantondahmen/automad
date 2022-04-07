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
 * Copyright (c) 2016-2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\UI\Controllers;

use Automad\Core\Request;
use Automad\UI\Components\Grid\Users;
use Automad\UI\Models\UserCollectionModel;
use Automad\UI\Response;
use Automad\UI\Utils\Messenger;
use Automad\UI\Utils\Text;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The user collection class provides all methods for creating and loading user accounts.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2016-2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class UserCollectionController {
	/**
	 * Add user account based on $_POST.
	 *
	 * @return Response the response object
	 */
	public static function createUser() {
		$Response = new Response();
		$Messenger = new Messenger();

		$username = Request::post('username');
		$password1 = Request::post('password1');
		$password2 = Request::post('password2');
		$email = Request::post('email');

		$UserCollectionModel = new UserCollectionModel();

		if (!$UserCollectionModel->createUser($username, $password1, $password2, $email, $Messenger)) {
			$Response->setError($Messenger->getError());

			return $Response;
		}

		if (!$UserCollectionModel->save($Messenger)) {
			$Response->setError($Messenger->getError());

			return $Response;
		}

		$Response->setSuccess(Text::get('success_added') . ' "' . $username . '"');

		return $Response;
	}

	/**
	 * Optionally remove posted accounts and
	 * return the accounts grid.
	 *
	 * @return Response the response object
	 */
	public static function edit() {
		$Response = new Response();
		$Messenger = new Messenger();
		$UserCollectionModel = new UserCollectionModel();

		if ($users = Request::post('delete')) {
			if ($UserCollectionModel->delete($users, $Messenger)) {
				$Response->setSuccess(Text::get('success_remove') . ' "' . implode('", "', $users) . '"');
			} else {
				$Response->setError($Messenger->getError());
			}
		}

		$Response->setHtml(Users::render($UserCollectionModel->getCollection()));

		return $Response;
	}

	/**
	 * Install the first user account.
	 *
	 * @return string Error message in case of an error
	 */
	public static function install() {
		if (empty($_POST)) {
			return '';
		}

		$UserCollectionModel = new UserCollectionModel();
		$Messenger = new Messenger();

		if (!$UserCollectionModel->createUser(
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
		header('Content-Disposition: attachment; filename=' . basename(AM_FILE_ACCOUNTS));
		ob_end_flush();

		exit($UserCollectionModel->generatePHP());
	}

	/**
	 * Invite a new user by email.
	 *
	 * @return Response a response object
	 */
	public static function inviteUser() {
		$Response = new Response();
		$UserCollectionModel = new UserCollectionModel();
		$Messenger = new Messenger();

		$username = trim(Request::post('username'));
		$email = trim(Request::post('email'));
		$password = str_shuffle(sha1(microtime()));

		if (!$UserCollectionModel->createUser($username, $password, $password, $email, $Messenger)) {
			$Response->setError($Messenger->getError());

			return $Response;
		}

		if (!$UserCollectionModel->save($Messenger)) {
			$Response->setError($Messenger->getError());

			return $Response;
		}

		if (!$UserCollectionModel->sendInvitation($username, $email, $Messenger)) {
			$Response->setError($Messenger->getError());

			return $Response;
		}

		$Response->setSuccess(Text::get('success_user_invite'));

		return $Response;
	}
}
