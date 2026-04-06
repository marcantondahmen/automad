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
 * Copyright (c) 2014-2026 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * See LICENSE.md for license information.
 */

namespace Automad\Core;

use Automad\Models\MailConfig;
use Automad\System\ConfigFile;
use Automad\System\Server;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Config class.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2014-2026 by Marc Anton Dahmen - https://marcdahmen.de
 * @license See LICENSE.md for license information
 */
class Config {
	/**
	 * All keys that can be define as environment variable on the server.
	 */
	const ENV_VARS = array(
		'AM_ALLOWED_FILE_TYPES',
		'AM_DEBUG_ENABLED',
		'AM_DEBUG_BROWSER',
		'AM_DEBUG_LOG_PATH',
		'AM_DEBUG_LOG_MAX_SIZE',
		'AM_CACHE_ENABLED',
		'AM_CACHE_MONITOR_DELAY',
		'AM_CACHE_LIFETIME',
		'AM_OPEN_BASEDIR_ENABLED',
		'AM_MAINTENANCE_MODE_ENABLED',
		'AM_MAINTENANCE_MODE_TEXT',
		'AM_CLOUD_MODE_ENABLED',
		'AM_MAIL_TRANSPORT',
		'AM_MAIL_FROM',
		'AM_MAIL_SMTP_PORT',
		'AM_MAIL_SMTP_SERVER',
		'AM_MAIL_SMTP_USERNAME',
		'AM_MAIL_SMTP_PASSWORD',
		'AM_PASSWORD_REQUIRED_CHARS',
		'AM_PASSWORD_MIN_LENGTH',
		'AM_DISK_QUOTA'
	);

	/**
	 * The configuration file.
	 */
	const FILE = AM_BASE_DIR . '/config/config.php';

	/**
	 * All config keys that are defined outside the config.php files
	 * in the server's environment are tracked here.
	 */
	public static array $envKeys = array();

	/**
	 * Initialize the main config by merging all available sources.
	 */
	public static function init(): void {
		self::fromEnv();
		self::fromFile();
		self::fromDefaults();
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
	 * Define default values for all constants that are not overriden.
	 */
	private static function fromDefaults(): void {
		// First define permsissions and temp directory since they are required for logging.
		self::set('AM_PERM_DIR', 0755);
		self::set('AM_PERM_FILE', 0644);
		self::set('AM_DIR_TMP', FileSystem::getTmpDir());

		// Define debugging already here to be available when parsing the request.
		self::set('AM_DEBUG_ENABLED', false);
		self::set('AM_DEBUG_BROWSER', false);
		self::set('AM_DEBUG_LOG_PATH', AM_DIR_TMP . '/debug_log');
		self::set('AM_DEBUG_LOG_MAX_SIZE', 10000);

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

		// Session cookie name salt that can be used to trigger a sign-out-all.
		self::set('AM_SESSION_COOKIE_SALT', '');

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
			'avi, flv, mov, mp4, mpeg, webm, ' .
			// Other
			'css, js, md, pdf'
		);

		// PAGE
		self::set('AM_PAGE_DASHBOARD', '/dashboard');

		// FEED
		self::set('AM_FEED_ENABLED', true);
		self::set('AM_FEED_URL', '/feed');
		self::set('AM_FEED_FIELDS', '+hero, +main');

		// CACHE
		self::set('AM_CACHE_ENABLED', true);
		self::set('AM_CACHE_MONITOR_DELAY', 120);
		self::set('AM_CACHE_LIFETIME', 43200);

		// IMAGE
		// https://www.php.net/manual/en/function.imagejpeg.php
		self::set('AM_IMG_JPG_QUALITY', 80);
		// https://www.php.net/manual/en/function.imagepng.php
		self::set('AM_IMG_PNG_QUALITY', 9);
		// https://www.php.net/manual/en/function.imagewebp.php
		self::set('AM_IMG_WEBP_QUALITY', 80);

		// UPDATE
		self::set('AM_UPDATE_ITEMS', '/automad, /lib, /index.php');
		self::set('AM_UPDATE_BRANCH', 'v2');
		self::set('AM_UPDATE_REPO', 'automadcms/automad-dist');
		self::set('AM_UPDATE_TEMP', AM_BASE_DIR . AM_DIR_CACHE . '/update');

		// Packagist
		self::set('AM_PACKAGE_REGISTRY', 'https://registry.automad.org/2.0.0-alpha.43.json');
		self::set('AM_PACKAGE_FILTER_REGEX', '.');

		// I18n
		self::set('AM_I18N_ENABLED', false);

		// Mail address obfuscation
		self::set('AM_MAIL_OBFUSCATION_ENABLED', true);

		// Enable open_basedir restriction
		self::set('AM_OPEN_BASEDIR_ENABLED', true);

		// Enable maintenance mode
		// During maintenance, all content is accessible for visitors but read-only.
		// This allows for server maintenance or moving a site to another server in a safe way.
		self::set('AM_MAINTENANCE_MODE_ENABLED', false);
		self::set('AM_MAINTENANCE_MODE_TEXT', Text::get('maintenanceModeText'));

		// Mail
		self::set('AM_MAIL_TRANSPORT', MailConfig::DEFAULT_TRANSPORT);
		self::set('AM_MAIL_FROM', MailConfig::getDefaultFrom());
		self::set('AM_MAIL_SMTP_SERVER', '');
		self::set('AM_MAIL_SMTP_USERNAME', '');
		self::set('AM_MAIL_SMTP_PASSWORD', '');
		self::set('AM_MAIL_SMTP_PORT', MailConfig::DEFAULT_PORT);

		// Password requirements
		self::set('AM_PASSWORD_REQUIRED_CHARS', '@#%^~+=*$&! A-Z a-z 0-9');
		self::set('AM_PASSWORD_MIN_LENGTH', '8');

		// Disk quota in MB
		self::set('AM_DISK_QUOTA', 0);

		// Disable cookie consent banner.
		self::set('AM_CONSENT_CHECK_ENABLED', true);
	}

	/**
	 * Merge settings that can be defined as environment variables with config.
	 */
	private static function fromEnv(): void {
		$envFile = AM_BASE_DIR . '/.env';

		if (is_readable($envFile)) {
			$env = parse_ini_file($envFile);

			if (!empty($env)) {
				foreach ($env as $key => $value) {
					putenv("$key=$value");
				}
			}
		}

		foreach (self::ENV_VARS as $key) {
			$value = getenv($key);

			if (strlen(strval($value))) {
				if (is_numeric($value)) {
					$value = floatval($value);
				}

				self::set($key, $value);
				self::$envKeys[] = $key;
			}
		}
	}

	/**
	 * Merge default constants with overrides that are saved in config files
	 * such as "config.php" and "config.mail.php".
	 */
	private static function fromFile(): void {
		$files = FileSystem::glob(AM_BASE_DIR . '/config/config.*');

		foreach ($files as $file) {
			// Strip filename until it is an empty string (main config)
			// or the name of the config such as "mail" (config.mail.php).
			$configName = Str::stripStart($file, AM_BASE_DIR . '/config/config');
			$configName = Str::stripEnd($configName, 'php');
			$configName = trim($configName, '.');

			foreach (ConfigFile::read($configName) as $name => $value) {
				self::set($name, $value);
			}
		}
	}

	/**
	 * Get the index filename in case pretty URLs are disabled.
	 *
	 * @return string
	 */
	private static function getIndex(): string {
		$serverSoftware = strtolower($_SERVER['SERVER_SOFTWARE'] ?? '');
		$htaccess = file_exists(AM_BASE_DIR . '/.htaccess');

		$hasPrettyUrls = (
			(strpos($serverSoftware, 'apache') !== false && $htaccess) ||
			(strpos($serverSoftware, 'litespeed') !== false && $htaccess) ||
			strpos($serverSoftware, 'nginx') !== false ||
			php_sapi_name() === 'cli-server'
		);

		return $hasPrettyUrls ? '' : '/index.php';
	}
}
