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
 * Copyright (c) 2023-2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Controllers\API;

use Automad\API\Response;
use Automad\Core\Cache;
use Automad\Core\Messenger;
use Automad\Core\Request;
use Automad\Core\Session;
use Automad\Core\Text;
use Automad\Models\MailConfig;
use Automad\Models\UserCollection;
use Automad\System\Mail;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The email config controller.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2023-2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class MailConfigController {
	/**
	 * Reset the mail config.
	 *
	 * @return Response the Response object
	 */
	public static function reset(): Response {
		$Response = new Response();

		if (AM_CLOUD_MODE_ENABLED) {
			return $Response->setError(Text::get('permissionsDeniedError'));
		}

		MailConfig::reset();
		Cache::clear();

		return $Response;
	}

	/**
	 * Save email config data.
	 *
	 * @return Response the response object
	 */
	public static function save(): Response {
		$Response = new Response();
		$transport = Request::post('transport');

		if (AM_CLOUD_MODE_ENABLED) {
			return $Response->setError(Text::get('permissionsDeniedError'));
		}

		if (empty($transport)) {
			return $Response;
		}

		$MailConfig = new MailConfig();

		$MailConfig->transport = $transport;
		$MailConfig->from = Request::post('from');
		$MailConfig->smtpServer = Request::post('smtpServer');
		$MailConfig->smtpUsername = Request::post('smtpUsername');
		$MailConfig->smtpPort = intval(Request::post('smtpPort'));

		$password = Request::post('smtpPassword');

		if (strlen($password)) {
			$MailConfig->smtpPassword = $password;
		}

		if ($MailConfig->save()) {
			$Response->setSuccess(Text::get('savedSuccess'));
		} else {
			$Response->setError(Text::get('systemMailConfigError'));
		}

		Cache::clear();

		return $Response;
	}

	/**
	 * Send test mail.
	 *
	 * @return Response the Response object
	 */
	public static function test(): Response {
		$Response = new Response();
		$UserCollection = new UserCollection();
		$User = $UserCollection->getUser(Session::getUsername());
		$to = $User->email ?? '';

		if (!$to) {
			return $Response->setError(Text::get('systemMailSendTestNoEmail'));
		}

		$Messenger = new Messenger();
		$success = Mail::send($to, 'Automad Mail Config Test', '<h1>Success</h1>', null, $Messenger);

		if ($success) {
			return $Response->setSuccess(Text::get('systemMailSendTestSuccess') . ' ' . $to);
		}

		return $Response->setError($Messenger->getError());
	}
}
