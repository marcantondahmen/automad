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
 * Copyright (c) 2023 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Models;

use Automad\Core\Automad;
use Automad\Core\Config;
use Automad\Core\Debug;
use Automad\Core\FileSystem;
use Automad\Core\Text;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The mail config class.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2023 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class MailConfig {
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
		return 'noreply@' . ($_SERVER['SERVER_NAME'] ?? '');
	}

	/**
	 * Reset the config to defaults.
	 *
	 * @return bool
	 */
	public static function reset(): bool {
		$config = Config::read();

		$config['AM_MAIL_TRANSPORT'] = self::DEFAULT_TRANSPORT;
		$config['AM_MAIL_FROM'] = '';
		$config['AM_MAIL_SMTP_SERVER'] = '';
		$config['AM_MAIL_SMTP_USERNAME'] = '';
		$config['AM_MAIL_SMTP_PASSWORD'] = '';
		$config['AM_MAIL_SMTP_PORT'] = self::DEFAULT_PORT;

		return Config::write($config);
	}

	/**
	 * Save the config instance.
	 *
	 * @return bool
	 */
	public function save(): bool {
		$config = Config::read();

		$config['AM_MAIL_TRANSPORT'] = $this->transport;
		$config['AM_MAIL_FROM'] = $this->from;
		$config['AM_MAIL_SMTP_SERVER'] = $this->smtpServer;
		$config['AM_MAIL_SMTP_USERNAME'] = $this->smtpUsername;
		$config['AM_MAIL_SMTP_PASSWORD'] = $this->smtpPassword;
		$config['AM_MAIL_SMTP_PORT'] = $this->smtpPort;

		return Config::write($config);
	}
}
