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
 * Copyright (c) 2014-2023 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Core;

use Automad\System\Server;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Config class.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2014-2023 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Config {
	/**
	 * The configuration file.
	 */
	public static string $file = AM_BASE_DIR . '/config/config.php';

	/**
	 * The legacy .json file.
	 */
	private static string $legacy = AM_BASE_DIR . '/config/config.json';

	/**
	 * Define default values for all constants that are not overriden.
	 */
	public static function defaults(): void {
		// Define debugging already here to be available when parsing the request.
		self::set('AM_DEBUG_ENABLED', false);

		// The server protocol, port and name.
		self::set('AM_SERVER', Server::getHost());

		// Set base URL for all URLs relative to the root.
		// Change this only if needed, for example when running behind a proxy.
		self::set('AM_BASE_URL', Server::getBaseUrl());

		// Change this only if needed, for example when running behind a proxy and the automatic configuration is not working.
		// Example: https://domain.com:8000/site-2 is forwarded to https://domain.com:3000/path/site-2 using a reverse proxy.
		// Both constants would then be configured as follows:
		// AM_BASE_URL = '/site-2' (the base URL that is visible to the internet outside)
		// AM_BASE_PROXY = '/path/site-2' (the base URL behind the proxy)
		self::set('AM_BASE_PROXY', Server::getProxyBaseUrl());

		// Check whether pretty URLs are enabled.
		self::set('AM_INDEX', self::getIndex());

		// Define AM_BASE_INDEX as the prefix for all page URLs.
		self::set('AM_BASE_INDEX', AM_BASE_URL . AM_INDEX);

		// An optional base protocol/domain combination for the sitemap.xml in case of being behind a proxy.
		self::set('AM_BASE_SITEMAP', '');

		// Define all constants which are not defined yet by the config file.
		// DIR
		self::set('AM_DIR_PAGES', '/pages');
		self::set('AM_DIR_SHARED', '/shared');
		self::set('AM_DIR_PACKAGES', '/packages');
		self::set('AM_DIR_CACHE', '/cache');
		self::set('AM_DIRNAME_MAX_LEN', 60); // Max dirname length when creating/moving pages with the UI.

		// FILE
		self::set('AM_FILE_UI_TRANSLATION', ''); // Base dir will be added automatically to enable external configuration.
		self::set(
			'AM_ALLOWED_FILE_TYPES',
			// Archives
			'dmg, iso, rar, tar, zip, ' .
			// Audio
			'aiff, m4a, mp3, ogg, wav, ' .
			// Graphics
			'ai, dxf, eps, gif, ico, jpg, jpeg, png, psd, svg, tga, tiff, webp, ' .
			// Video
			'avi, flv, mov, mp4, mpeg, ' .
			// Other
			'css, js, md, pdf'
		);

		// PAGE
		self::set('AM_PAGE_DASHBOARD', '/dashboard');

		// FEED
		self::set('AM_FEED_ENABLED', true);
		self::set('AM_FEED_URL', '/feed');
		self::set('AM_FEED_FIELDS', '+hero, +main');

		// PERMISSIONS
		self::set('AM_PERM_DIR', 0755);
		self::set('AM_PERM_FILE', 0644);

		// CACHE
		self::set('AM_CACHE_ENABLED', true);
		self::set('AM_CACHE_MONITOR_DELAY', 120);
		self::set('AM_CACHE_LIFETIME', 43200);

		// IMAGE
		self::set('AM_IMG_JPG_QUALITY', 90);

		// UPDATE
		self::set('AM_UPDATE_ITEMS', '/automad, /lib, /index.php, /packages/standard');
		self::set('AM_UPDATE_BRANCH', 'master');
		self::set('AM_UPDATE_REPO_DOWNLOAD_URL', 'https://github.com/marcantondahmen/automad/archive');
		self::set('AM_UPDATE_REPO_RAW_URL', 'https://raw.githubusercontent.com/marcantondahmen/automad');
		self::set('AM_UPDATE_REPO_VERSION_FILE', '/automad/version.php');
		self::set('AM_UPDATE_TEMP', AM_DIR_CACHE . '/update');

		// Packagist
		self::set('AM_PACKAGE_REPO', 'https://packagist.org/search.json?&type=automad-package&per_page=100');

		// Version number
		include AM_BASE_DIR . '/automad/version.php';
	}

	/**
	 * Define constants based on the configuration array.
	 */
	public static function overrides(): void {
		foreach (self::read() as $name => $value) {
			self::set($name, $value);
		}
	}

	/**
	 * Read configuration overrides as JSON string form PHP or JSON file
	 * and decode the returned string. Note that now the configuration is stored in
	 * PHP files instead of JSON files to make it less easy to access from outside.
	 *
	 * @return array The configuration array
	 */
	public static function read(): array {
		$json = false;
		$config = array();

		if (is_readable(self::$file)) {
			$json = require self::$file;
		} elseif (is_readable(self::$legacy)) {
			// Support legacy configuration files.
			$json = file_get_contents(self::$legacy);
		}

		if ($json) {
			$config = json_decode($json, true);
		}

		return $config;
	}

	/**
	 * Define constant, if not defined already.
	 *
	 * @param string $name
	 * @param mixed $value
	 */
	public static function set(string $name, mixed $value): void {
		if (!defined($name)) {
			define($name, $value);
		}
	}

	/**
	 * Write the configuration file.
	 *
	 * @param array $config
	 * @return bool True on success
	 */
	public static function write(array $config): bool {
		$json = json_encode($config, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
		$content = "<?php return <<< JSON\r\n$json\r\nJSON;\r\n";
		$success = FileSystem::write(self::$file, $content);

		if ($success && is_writable(self::$legacy)) {
			@unlink(self::$legacy);
		}

		if ($success && function_exists('opcache_invalidate')) {
			opcache_invalidate(self::$file, true);
		}

		return $success;
	}

	/**
	 * Get the index filename in case pretty URLs are disabled.
	 *
	 * @return string
	 */
	private static function getIndex(): string {
		$serverSoftware = $_SERVER['SERVER_SOFTWARE'] ?? '';

		Debug::log($serverSoftware, 'Server Software');

		$hasPrettyUrls = (
			(
				strpos(strtolower($serverSoftware), 'apache') !== false &&
				file_exists(AM_BASE_DIR . '/.htaccess')
			) ||
			strpos(strtolower($serverSoftware), 'nginx') !== false
		);

		Debug::log('Pretty URLs are ' . ($hasPrettyUrls ? 'enabled' : 'disbaled'));

		return $hasPrettyUrls ? '' : '/index.php';
	}
}
