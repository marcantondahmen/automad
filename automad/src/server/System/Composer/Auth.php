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
 * Copyright (c) 2025-2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

namespace Automad\System\Composer;

use Automad\Core\Config;
use Automad\System\SerializedFile;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Composer auth model.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2025-2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class Auth {
	const FILE = AM_BASE_DIR . '/config/composer.auth.php';

	/**
	 * The GitHub token.
	 */
	public string $githubToken;

	/**
	 * The Gitlab token.
	 */
	public string $gitlabToken;

	/**
	 * The Gitlab url.
	 */
	public string $gitlabUrl;

	/**
	 * The constructor.
	 */
	public function __construct() {
		$this->githubToken = '';
		$this->gitlabToken = '';
		$this->gitlabUrl = 'gitlab.com';
	}

	/**
	 * Read the config file and create a fresh instance.
	 */
	public static function get(): Auth {
		$Auth = self::getFile()->read();

		return $Auth instanceof self ? $Auth : new Auth();
	}

	/**
	 * Return a save array of auth information.
	 *
	 * @return array
	 */
	public function getSafeValues(): array {
		return array(
			'githubTokenIsSet' => strlen($this->githubToken) > 0,
			'gitlabTokenIsSet' => strlen($this->gitlabToken) > 0,
			'gitlabUrl' => $this->gitlabUrl
		);
	}

	/**
	 * Reset the config to defaults.
	 *
	 * @return bool
	 */
	public static function reset(): bool {
		return self::getFile()->delete();
	}

	/**
	 * Save the config instance.
	 *
	 * @return bool
	 */
	public function save(): bool {
		return self::getFile()->write($this);
	}

	/**
	 * Set the COMPOSER_AUTH environment variable.
	 */
	public function setEnv(): void {
		$data = array();

		if ($this->githubToken) {
			$data['github-oauth'] = array('github.com' => $this->githubToken);
		}

		if ($this->gitlabUrl && $this->gitlabToken) {
			$data['gitlab-token'] = array($this->gitlabUrl => $this->gitlabToken);
		}

		if (empty($data)) {
			return;
		}

		$json = strval(json_encode($data));

		putenv("COMPOSER_AUTH=$json");
	}

	/**
	 * Get the encrypted serialized config file.
	 *
	 * @see SerializedFile
	 * @return SerializedFile
	 */
	private static function getFile(): SerializedFile {
		return new SerializedFile(self::FILE, self::class, true);
	}
}
