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
 * Copyright (c) 2023-2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

namespace Automad\Models;

use Automad\Core\Config;
use Automad\System\ConfigFile;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The mail config class.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2023-2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class MailConfig {
	const CONFIG_NAME = 'mail';
	const DEFAULT_PORT = 587;
	const DEFAULT_TRANSPORT = 'sendmail';

	/**
	 * The from address that is used when no from address is passed to the Mail::send() method.
	 */
	public string $from;

	/**
	 * The SMTP password.
	 */
	public string $smtpPassword;

	/**
	 * The SMTP port.
	 */
	public int $smtpPort;

	/**
	 * The SMTP server.
	 */
	public string $smtpServer;

	/**
	 * The SMTP username.
	 */
	public string $smtpUsername;

	/**
	 * The transport method.
	 */
	public string $transport;

	/**
	 * The constructor.
	 */
	public function __construct() {
		$this->transport = AM_MAIL_TRANSPORT;
		$this->from = AM_MAIL_FROM ? AM_MAIL_FROM : MailConfig::getDefaultFrom();
		$this->smtpServer = AM_MAIL_SMTP_SERVER;
		$this->smtpUsername = AM_MAIL_SMTP_USERNAME;
		$this->smtpPassword = AM_MAIL_SMTP_PASSWORD;
		$this->smtpPort = AM_MAIL_SMTP_PORT;
	}

	/**
	 * Return a default from address.
	 *
	 * @return string
	 */
	public static function getDefaultFrom(): string {
		return 'noreply@' . ($_SERVER['SERVER_NAME'] ?? 'localhost');
	}

	/**
	 * Reset the config to defaults.
	 *
	 * @return bool
	 */
	public static function reset(): bool {
		$ConfigFile = new ConfigFile(MailConfig::CONFIG_NAME);

		$ConfigFile->set('AM_MAIL_TRANSPORT', self::DEFAULT_TRANSPORT);
		$ConfigFile->set('AM_MAIL_FROM', '');
		$ConfigFile->set('AM_MAIL_SMTP_SERVER', '');
		$ConfigFile->set('AM_MAIL_SMTP_USERNAME', '');
		$ConfigFile->set('AM_MAIL_SMTP_PASSWORD', '');
		$ConfigFile->set('AM_MAIL_SMTP_PORT', self::DEFAULT_PORT);

		return $ConfigFile->write();
	}

	/**
	 * Save the config instance.
	 *
	 * @return bool
	 */
	public function save(): bool {
		$ConfigFile = new ConfigFile(MailConfig::CONFIG_NAME);

		$ConfigFile->set('AM_MAIL_TRANSPORT', $this->transport);
		$ConfigFile->set('AM_MAIL_FROM', $this->from);
		$ConfigFile->set('AM_MAIL_SMTP_SERVER', $this->smtpServer);
		$ConfigFile->set('AM_MAIL_SMTP_USERNAME', $this->smtpUsername);
		$ConfigFile->set('AM_MAIL_SMTP_PASSWORD', $this->smtpPassword);
		$ConfigFile->set('AM_MAIL_SMTP_PORT', $this->smtpPort);

		return $ConfigFile->write();
	}
}
