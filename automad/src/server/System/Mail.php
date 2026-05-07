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
 * Copyright (c) 2020-2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

namespace Automad\System;

use Automad\Core\Automad;
use Automad\Core\Debug;
use Automad\Core\Messenger;
use Automad\Models\MailConfig;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\File;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The mail class.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2020-2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class Mail {
	const LOGO = AM_BASE_DIR . '/automad/assets/logo-email.png';
	const LOGO_CID = 'logo@automad';

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
	 * @param ?string $replyTo
	 * @param ?Messenger $Messenger
	 * @return bool
	 */
	public static function send(string $to, string $subject, string $message, ?string $replyTo = null, ?Messenger $Messenger = null): bool {
		$MailConfig = new MailConfig();

		$smtpUsername = rawurlencode($MailConfig->smtpUsername);
		$smtpPassword = rawurlencode($MailConfig->smtpPassword);

		$dsn = $MailConfig->transport === 'sendmail'
			? 'sendmail://default'
			: "smtp://{$smtpUsername}:{$smtpPassword}@{$MailConfig->smtpServer}:{$MailConfig->smtpPort}";

		$transport = Transport::fromDsn($dsn);
		$mailer = new Mailer($transport);

		$email = (new Email())
			->from("Automad <$MailConfig->from>")
			->to($to)
			->subject($subject)
			->html($message);

		if ($replyTo) {
			$email->replyTo($replyTo);
		}

		if (preg_match('/src="cid:' . self::LOGO_CID . '"/', $message)) {
			$part = new DataPart(new File(self::LOGO));
			$part->setContentId(self::LOGO_CID);
			$email->addPart($part->asInline());
		}

		try {
			$mailer->send($email);

			return true;
		} catch (\Throwable $error) {
			Debug::log($error->getMessage());

			if ($Messenger) {
				$Messenger->setError($error->getMessage());
			}

			return false;
		}
	}

	/**
	 * Send content of mail form.
	 *
	 * @param array $data
	 * @param Automad $Automad
	 * @return bool|string the sendig status message
	 */
	public static function sendForm(array $data, Automad $Automad): bool|string {
		// Prevent a second call.
		if (self::$sent) {
			return $data['success'];
		}

		// Define field names.
		$honeypot = 'nickname'; // Not an obvious spam bot trap name.
		$from = 'from'; // The "from" field will be used for "reply to".
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

		// Note that "$from" means in this context "reply to".
		if (self::send($data['to'], $subject, $message, $_POST[$from])) {
			self::$sent = true;

			return $data['success'];
		}

		return '';
	}
}
