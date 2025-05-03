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
 * Copyright (c) 2025 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\System\Composer;

use Automad\Core\Automad;
use Automad\Core\Config;
use Automad\Core\FileSystem;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Composer auth model.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2025 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
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
		if (!is_readable(self::FILE)) {
			return new Auth();
		}

		$unserialized = unserialize(strval(file_get_contents(self::FILE)));

		if ($unserialized instanceof Auth) {
			return $unserialized;
		}

		return new Auth();
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
		if (is_readable(self::FILE)) {
			return unlink(self::FILE);
		}

		return true;
	}

	/**
	 * Save the config instance.
	 *
	 * @return bool
	 */
	public function save(): bool {
		return FileSystem::write(self::FILE, serialize($this));
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

		$json = strval(json_encode($data));

		putenv("COMPOSER_AUTH=$json");
	}
}
