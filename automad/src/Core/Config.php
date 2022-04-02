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
 * Copyright (c) 2014-2021 by Marc Anton Dahmen
 * https://marcdahmen.de
 *
 * Licensed under the MIT license.
 * https://automad.org/license
 */

namespace Automad\Core;

defined('AUTOMAD') or die('Direct access not permitted!');

/**
 * The Config class.
 *
 * @author Marc Anton Dahmen
 * @copyright Copyright (c) 2014-2021 by Marc Anton Dahmen - https://marcdahmen.de
 * @license MIT license - https://automad.org/license
 */
class Config {
	/**
	 * The configuration file.
	 */
	public static $file = AM_BASE_DIR . '/config/config.php';

	/**
	 * The legacy .json file.
	 */
	private static $legacy = AM_BASE_DIR . '/config/config.json';

	/**
	 * Define default values for all constants that are not overriden.
	 */
	public static function defaults() {
		// Define debugging already here to be available when parsing the request.
		self::set('AM_DEBUG_ENABLED', false);

		// Set base URL for all URLs relative to the root.
		if (getenv('HTTP_X_FORWARDED_HOST') || getenv('HTTP_X_FORWARDED_SERVER')) {
			// In case the site is behind a proxy, set AM_BASE_URL to AM_BASE_PROXY.
			// AM_BASE_PROXY can be separately defined to enable running a site with and without a proxy in parallel.
			self::set('AM_BASE_PROXY', '');
			self::set('AM_BASE_URL', AM_BASE_PROXY);
			Debug::log(getenv('HTTP_X_FORWARDED_SERVER'), 'Proxy');
		} else {
			// In case the site is not running behind a proxy server, just get the base URL from the script name.
			self::set('AM_BASE_URL', str_replace('/index.php', '', getenv('SCRIPT_NAME')));
		}

		Debug::log(getenv('SERVER_SOFTWARE'), 'Server Software');

		// Check whether pretty URLs are enabled.
		if ((strpos(strtolower(getenv('SERVER_SOFTWARE')), 'apache') !== false && file_exists(AM_BASE_DIR . '/.htaccess')) || strpos(strtolower(getenv('SERVER_SOFTWARE')), 'nginx') !== false) {
			// If .htaccess exists on Apache or the server software is Nginx, assume that pretty URLs are enabled and AM_INDEX is empty.
			self::set('AM_INDEX', '');
			Debug::log('Pretty URLs are enabled.');
		} else {
			// For all other environments, AM_INDEX will be defined as fallback and pretty URLs are disabled.
			self::set('AM_INDEX', '/index.php');
			Debug::log('Pretty URLs are disabled');
		}

		// Custom override for Server::url() method.
		self::set('AM_SERVER', '');

		// Define AM_BASE_INDEX as the prefix for all page URLs.
		self::set('AM_BASE_INDEX', AM_BASE_URL . AM_INDEX);

		// An optional base protocol/domain combination for the sitemap.xml in case of being behind a proxy.
		self::set('AM_BASE_SITEMAP', '');

		// Get the requested URL.
		define('AM_REQUEST', Request::page());

		// Define all constants which are not defined yet by the config file.
		// DIR
		self::set('AM_DIR_PAGES', '/pages');
		self::set('AM_DIR_SHARED', '/shared');
		self::set('AM_DIR_PACKAGES', '/packages');
		self::set('AM_DIR_CACHE', '/cache');
		self::set('AM_DIR_CACHE_PAGES', AM_DIR_CACHE . '/pages');
		self::set('AM_DIR_CACHE_IMAGES', AM_DIR_CACHE . '/images');
		self::set('AM_DIR_TRASH', AM_DIR_CACHE . '/trash');
		self::set('AM_DIRNAME_MAX_LEN', 60); // Max dirname length when creating/moving pages with the UI.

		// FILE
		self::set('AM_FILE_EXT_DATA', 'txt'); // Changing that constant will also require updating the .htaccess file! (for blocking direct access)
		self::set('AM_FILE_EXT_CAPTION', 'caption');
		self::set('AM_FILE_PREFIX_CACHE', 'cached'); // Changing that constant will also require updating the .htaccess file! (for blocking direct access)
		self::set('AM_FILE_EXT_PAGE_CACHE', 'html');
		self::set('AM_FILE_EXT_HEADLESS_CACHE', 'json');
		self::set('AM_FILE_SHARED_DATA', AM_BASE_DIR . AM_DIR_SHARED . '/data.' . AM_FILE_EXT_DATA);
		self::set('AM_FILE_SITE_MTIME', AM_BASE_DIR . AM_DIR_CACHE . '/' . AM_FILE_PREFIX_CACHE . '_site_mtime');
		self::set('AM_FILE_OBJECT_CACHE', AM_BASE_DIR . AM_DIR_CACHE . '/' . AM_FILE_PREFIX_CACHE . '_automad_object');
		self::set('AM_FILE_OBJECT_USER_CACHE', AM_BASE_DIR . AM_DIR_CACHE . '/' . AM_FILE_PREFIX_CACHE . '_automad_object_user');
		self::set('AM_FILE_ACCOUNTS', AM_BASE_DIR . '/config/accounts.php');
		self::set('AM_FILE_GUI_TEXT_MODULES', AM_BASE_DIR . '/automad/ui/lang/english.txt');
		self::set('AM_FILE_GUI_TRANSLATION', ''); // Base dir will be added automatically to enable external configuration.
		self::set(
			'AM_ALLOWED_FILE_TYPES',
			// Archives
			'dmg, iso, rar, tar, zip, ' .
			// Audio
			'aiff, m4a, mp3, ogg, wav, ' .
			// Graphics
			'ai, dxf, eps, gif, ico, jpg, jpeg, png, psd, svg, tga, tiff, ' .
			// Video
			'avi, flv, mov, mp4, mpeg, ' .
			// Other
			'css, js, md, pdf'
		);

		// PAGE
		self::set('AM_PAGE_NOT_FOUND_TEMPLATE', 'page_not_found');
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

		// TEMPLATE DELIMITERS
		self::set('AM_DEL_VAR_OPEN', '@{');
		self::set('AM_DEL_VAR_CLOSE', '}');
		self::set('AM_DEL_STATEMENT_OPEN', '<@');
		self::set('AM_DEL_STATEMENT_CLOSE', '@>');
		self::set('AM_DEL_COMMENT_OPEN', '<#');
		self::set('AM_DEL_COMMENT_CLOSE', '#>');
		self::set('AM_DEL_INPAGE_BUTTON_OPEN', '{{@@');
		self::set('AM_DEL_INPAGE_BUTTON_CLOSE', '@@}}');

		// PARSE
		// Block separator - separates all key/value pairs
		// Must be used as the only string in a line within the template files.
		self::set('AM_PARSE_BLOCK_SEPARATOR', '-');
		// Pair separator - separates the key from the value
		self::set('AM_PARSE_PAIR_SEPARATOR', ':');
		// Tags/String separator
		self::set('AM_PARSE_STR_SEPARATOR', ',');

		// KEYS
		// Variables used in txt files in /pages or /shared
		self::set('AM_KEY_DATE', 'date');
		self::set('AM_KEY_HIDDEN', 'hidden');
		self::set('AM_KEY_PRIVATE', 'private');
		self::set('AM_KEY_TAGS', 'tags');
		self::set('AM_KEY_THEME', 'theme');
		self::set('AM_KEY_TITLE', 'title');
		self::set('AM_KEY_SITENAME', 'sitename');
		self::set('AM_KEY_URL', 'url');

		// System variables depending on a context.
		self::set('AM_KEY_ORIG_URL', ':origUrl');
		self::set('AM_KEY_PATH', ':path');
		self::set('AM_KEY_BASENAME', ':basename');
		self::set('AM_KEY_PARENT', ':parent');
		self::set('AM_KEY_LEVEL', ':level');
		self::set('AM_KEY_TEMPLATE', ':template');
		self::set('AM_KEY_CURRENT_PAGE', ':current');
		self::set('AM_KEY_CURRENT_PATH', ':currentPath');
		self::set('AM_KEY_MTIME', ':mtime');

		// Runtime variables Generated by template constructs.
		self::set('AM_KEY_FILTER', ':filter');
		self::set('AM_KEY_TAG', ':tag');
		self::set('AM_KEY_FILE', ':file');
		self::set('AM_KEY_WIDTH', ':width');
		self::set('AM_KEY_HEIGHT', ':height');
		self::set('AM_KEY_FILE_RESIZED', ':fileResized');
		self::set('AM_KEY_WIDTH_RESIZED', ':widthResized');
		self::set('AM_KEY_HEIGHT_RESIZED', ':heightResized');
		self::set('AM_KEY_CAPTION', ':caption');
		self::set('AM_KEY_INDEX', ':i');
		self::set('AM_KEY_FILELIST_COUNT', ':filelistCount');
		self::set('AM_KEY_PAGELIST_COUNT', ':pagelistCount');
		self::set('AM_KEY_PAGELIST_DISPLAY_COUNT', ':pagelistDisplayCount');
		self::set('AM_KEY_PAGINATION_COUNT', ':paginationCount');
		self::set('AM_KEY_NOW', ':now');

		// HEADLESS
		self::set('AM_HEADLESS_ENABLED', false);
		self::set('AM_HEADLESS_TEMPLATE', '/automad/headless/json.php');

		// For security reasons, the custom template should not have the .php extension.
		self::set('AM_HEADLESS_TEMPLATE_CUSTOM', '/config/headless.json');

		// UPDATE
		self::set('AM_UPDATE_ITEMS', '/automad, /lib, /index.php, /packages/standard, /packages/tutorial');
		self::set('AM_UPDATE_BRANCH', 'master');
		self::set('AM_UPDATE_REPO_DOWNLOAD_URL', 'https://github.com/marcantondahmen/automad/archive');
		self::set('AM_UPDATE_REPO_RAW_URL', 'https://raw.githubusercontent.com/marcantondahmen/automad');
		self::set('AM_UPDATE_REPO_VERSION_FILE', '/automad/version.php');
		self::set('AM_UPDATE_TEMP', AM_DIR_CACHE . '/update');

		// Version number
		include AM_BASE_DIR . '/automad/version.php';
	}

	/**
	 * Define constants based on the configuration array.
	 */
	public static function overrides() {
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
	public static function read() {
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
	 * @param string $value
	 */
	public static function set(string $name, string $value) {
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
	public static function write(array $config) {
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
}
