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
 * Copyright (c) 2020-2024 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\System;

use Automad\Core\Automad;
use Automad\Models\MailConfig;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The mail class.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2020-2024 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Mail {
	/**
	 * Save status to avoid a second trigger for example in pagelists or teaser snippets.
	 */
	private static bool $sent = false;

	/**
	 * Send an email.
	 *
	 * @param string $to
	 * @param string $subject
	 * @param string $message
	 * @param ?string $from
	 * @return bool
	 */
	public static function send(string $to, string $subject, string $message, ?string $from = null): bool {
		$MailConfig = new MailConfig();

		$dsn = $MailConfig->transport === 'sendmail'
			? 'sendmail://default'
			: "smtp://{$MailConfig->smtpUsername}:{$MailConfig->smtpPassword}@{$MailConfig->smtpServer}:{$MailConfig->smtpPort}";

		$transport = Transport::fromDsn($dsn);
		$mailer = new Mailer($transport);

		$email = (new Email())
			->from($from ?? $MailConfig->from)
			->to($to)
			->subject($subject)
			->html($message);

		try {
			$mailer->send($email);

			return true;
		} catch (\Throwable $error) {
			return false;
		}
	}

	/**
	 * Send content of mail form.
	 *
	 * @param array $data
	 * @param Automad $Automad
	 * @return bool|string the sendig status
	 */
	public static function sendForm(array $data, Automad $Automad): bool|string {
		// Prevent a second call.
		if (self::$sent) {
			return $data['success'];
		}

		// Define field names.
		$honeypot = 'human';
		$from = 'from';
		$subject = 'subject';
		$message = 'message';

		// Basic checks.
		if (empty($_POST) || empty($data['to'])) {
			return false;
		}

		// Check optional honeypot to verify human.
		if (isset($_POST[$honeypot]) && $_POST[$honeypot] != false) {
			return false;
		}

		// Check if form fields are not empty.
		if (empty($_POST[$from]) || empty($_POST[$subject]) || empty($_POST[$message])) {
			return $data['error'];
		}

		// Check if form fields are actually strings.
		if (!is_string($_POST[$from]) || !is_string($_POST[$subject]) || !is_string($_POST[$message])) {
			return $data['error'];
		}

		// Prepare mail.
		$subject = $Automad->Shared->get(Fields::SITENAME) . ': ' . strip_tags($_POST[$subject]);
		$message = strip_tags($_POST[$message]);

		if (self::send($data['to'], $subject, $message, $_POST[$from])) {
			self::$sent = true;

			return $data['success'];
		}

		return '';
	}
}
