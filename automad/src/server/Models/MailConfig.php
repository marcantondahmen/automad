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
	/**
	 * The config file location.
	 */
	const FILE = AM_BASE_DIR . '/config/mail.php';

	/**
	 * The from address that is used when no from address is passed to the Mail::send() method.
	 */
	public string $from = '';

	/**
	 * The SMTP password.
	 */
	public string $smtpPassword = '';

	/**
	 * The SMTP port.
	 */
	public int $smtpPort = 25;

	/**
	 * The SMTP server.
	 */
	public string $smtpServer = '';

	/**
	 * The SMTP username.
	 */
	public string $smtpUsername = '';

	/**
	 * The transport method.
	 */
	public string $transport = '';

	/**
	 * The constructor.
	 */
	public function __construct() {
		$this->load();
	}

	/**
	 * Return a default from address.
	 *
	 * @return string
	 */
	public function getDefaultFrom(): string {
		return 'noreply@' . ($_SERVER['SERVER_NAME'] ?? '');
	}

	/**
	 * Save the config instance.
	 *
	 * @return bool
	 */
	public function save(): bool {
		$config = array(
			'transport' => $this->transport,
			'from' => $this->from,
			'smtpServer' => $this->smtpServer,
			'smtpUsername' => $this->smtpUsername,
			'smtpPassword' => $this->smtpPassword,
			'smtpPort' => $this->smtpPort,
		);

		$serialized = serialize($config);
		$php = "<?php return unserialize('$serialized');";

		$success = FileSystem::write(MailConfig::FILE, $php);

		if ($success && function_exists('opcache_invalidate')) {
			opcache_invalidate(MailConfig::FILE, true);
		}

		return $success;
	}

	/**
	 * Load the config from file.
	 */
	private function load(): void {
		$config = array();

		if (is_readable(MailConfig::FILE)) {
			$config = require MailConfig::FILE;
		}

		$this->transport = $config['transport'] ?? 'sendmail';
		$this->from = $config['from'] ? $config['from'] : $this->getDefaultFrom();
		$this->smtpServer = $config['smtpServer'] ?? '';
		$this->smtpUsername = $config['smtpUsername'] ?? '';
		$this->smtpPassword = $config['smtpPassword'] ?? '';
		$this->smtpPort = $config['smtpPort'] ?? 25;
	}
}
