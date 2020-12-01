<?php 
/*
 *	                  ....
 *	                .:   '':.
 *	                ::::     ':..
 *	                ::.         ''..
 *	     .:'.. ..':.:::'    . :.   '':.
 *	    :.   ''     ''     '. ::::.. ..:
 *	    ::::.        ..':.. .''':::::  .
 *	    :::::::..    '..::::  :. ::::  :
 *	    ::'':::::::.    ':::.'':.::::  :
 *	    :..   ''::::::....':     ''::  :
 *	    :::::.    ':::::   :     .. '' .
 *	 .''::::::::... ':::.''   ..''  :.''''.
 *	 :..:::'':::::  :::::...:''        :..:
 *	 ::::::. '::::  ::::::::  ..::        .
 *	 ::::::::.::::  ::::::::  :'':.::   .''
 *	 ::: '::::::::.' '':::::  :.' '':  :
 *	 :::   :::::::::..' ::::  ::...'   .
 *	 :::  .::::::::::   ::::  ::::  .:'
 *	  '::'  '':::::::   ::::  : ::  :
 *	            '::::   ::::  :''  .:
 *	             ::::   ::::    ..''
 *	             :::: ..:::: .:''
 *	               ''''  '''''
 *	
 *
 *	AUTOMAD
 *
 *	Copyright (c) 2013-2020 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


namespace Automad\Core;


defined('AUTOMAD') or die('Direct access not permitted!');




// Set config file.
define('AM_CONFIG', AM_BASE_DIR . '/config/config.php');

// Parse AM_CONFIG to set user overrides for the below defined constants.
Config::overrides();

// Define debugging already here to be available when parsing the request.
Config::set('AM_DEBUG_ENABLED', false);




// Set base URL for all URLs relative to the root.
if (getenv('HTTP_X_FORWARDED_HOST') || getenv('HTTP_X_FORWARDED_SERVER')) {
	// In case the site is behind a proxy, set AM_BASE_URL to AM_BASE_PROXY.
	// AM_BASE_PROXY can be separately defined to enable running a site with and without a proxy in parallel. 
	Config::set('AM_BASE_PROXY', '');
	Config::set('AM_BASE_URL', AM_BASE_PROXY);
	Debug::log(getenv('HTTP_X_FORWARDED_SERVER'), 'Proxy');
} else {
	// In case the site is not running behind a proxy server, just get the base URL from the script name.
	Config::set('AM_BASE_URL', str_replace('/index.php', '', getenv('SCRIPT_NAME')));
}




Debug::log(getenv('SERVER_SOFTWARE'), 'Server Software');

// Check whether pretty URLs are enabled.
if ((strpos(strtolower(getenv('SERVER_SOFTWARE')), 'apache') !== false && file_exists(AM_BASE_DIR . '/.htaccess')) || strpos(strtolower(getenv('SERVER_SOFTWARE')), 'nginx') !== false) {
	// If .htaccess exists on Apache or the server software is Nginx, assume that pretty URLs are enabled and AM_INDEX is empty.
	Config::set('AM_INDEX', '');
	Debug::log('Pretty URLs are enabled.');
} else {
	// For all other environments, AM_INDEX will be defined as fallback and pretty URLs are disabled.
	Config::set('AM_INDEX', '/index.php');
	Debug::log('Pretty URLs are disabled');
}

// Define AM_BASE_INDEX as the prefix for all page URLs.
Config::set('AM_BASE_INDEX', AM_BASE_URL . AM_INDEX);




// Get the requested URL.
define('AM_REQUEST', Request::page());




// Define all constants which are not defined yet by the config file.
// DIR
Config::set('AM_DIR_PAGES', '/pages');
Config::set('AM_DIR_SHARED', '/shared');
Config::set('AM_DIR_PACKAGES', '/packages');
Config::set('AM_DIR_CACHE', '/cache');
Config::set('AM_DIR_CACHE_PAGES', AM_DIR_CACHE . '/pages');
Config::set('AM_DIR_CACHE_IMAGES', AM_DIR_CACHE . '/images');
Config::set('AM_DIR_TRASH', AM_DIR_CACHE . '/trash');
Config::set('AM_DIR_GUI_INC', '/automad/gui/inc');
Config::set('AM_DIRNAME_MAX_LEN', 60); // Max dirname length when creating/moving pages with the GUI.

// FILE
Config::set('AM_FILE_EXT_DATA', 'txt'); // Changing that constant will also require updating the .htaccess file! (for blocking direct access)	
Config::set('AM_FILE_EXT_CAPTION', 'caption');
Config::set('AM_FILE_PREFIX_CACHE', 'cached'); // Changing that constant will also require updating the .htaccess file! (for blocking direct access)
Config::set('AM_FILE_EXT_PAGE_CACHE', 'html');
Config::set('AM_FILE_EXT_HEADLESS_CACHE', 'json');
Config::set('AM_FILE_SHARED_DATA', AM_BASE_DIR . AM_DIR_SHARED . '/data.' . AM_FILE_EXT_DATA); 
Config::set('AM_FILE_SITE_MTIME', AM_BASE_DIR . AM_DIR_CACHE . '/' . AM_FILE_PREFIX_CACHE . '_site_mtime');
Config::set('AM_FILE_OBJECT_CACHE', AM_BASE_DIR . AM_DIR_CACHE . '/' . AM_FILE_PREFIX_CACHE . '_automad_object');
Config::set('AM_FILE_ACCOUNTS', AM_BASE_DIR . '/config/accounts.php');
Config::set('AM_FILE_GUI_TEXT_MODULES', AM_BASE_DIR . '/automad/gui/lang/english.txt');
Config::set('AM_FILE_GUI_TRANSLATION', ''); // Base dir will be added automatically to enable external configuration.
Config::set('AM_ALLOWED_FILE_TYPES', 
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
Config::set('AM_PAGE_NOT_FOUND_TEMPLATE', 'page_not_found');
Config::set('AM_PAGE_DASHBOARD', '/dashboard');

// PERMISSIONS
Config::set('AM_PERM_DIR', 0755);
Config::set('AM_PERM_FILE', 0644);

// CACHE
Config::set('AM_CACHE_ENABLED', true);
Config::set('AM_CACHE_MONITOR_DELAY', 120);
Config::set('AM_CACHE_LIFETIME', 43200);

// IMAGE
Config::set('AM_IMG_JPG_QUALITY', 90);

// TEMPLATE DELIMITERS
Config::set('AM_DEL_VAR_OPEN', '@{');
Config::set('AM_DEL_VAR_CLOSE', '}');
Config::set('AM_DEL_STATEMENT_OPEN', '<@');
Config::set('AM_DEL_STATEMENT_CLOSE', '@>');
Config::set('AM_DEL_COMMENT_OPEN', '<#');
Config::set('AM_DEL_COMMENT_CLOSE', '#>');
Config::set('AM_DEL_INPAGE_BUTTON_OPEN', '{{@@');
Config::set('AM_DEL_INPAGE_BUTTON_CLOSE', '@@}}');

// PARSE
// Block separator - separates all key/value pairs
// Must be used as the only string in a line within the template files.
Config::set('AM_PARSE_BLOCK_SEPARATOR', '-');
// Pair separator - separates the key from the value
Config::set('AM_PARSE_PAIR_SEPARATOR', ':');
// Tags/String separator
Config::set('AM_PARSE_STR_SEPARATOR', ',');

// KEYS
// Variables used in txt files in /pages or /shared
Config::set('AM_KEY_DATE', 'date');
Config::set('AM_KEY_HIDDEN', 'hidden');
Config::set('AM_KEY_PRIVATE', 'private');
Config::set('AM_KEY_TAGS', 'tags');
Config::set('AM_KEY_THEME', 'theme');
Config::set('AM_KEY_TITLE', 'title');
Config::set('AM_KEY_SITENAME', 'sitename');
Config::set('AM_KEY_URL', 'url');
// System variables depending on a context.
Config::set('AM_KEY_ORIG_URL', ':origUrl');
Config::set('AM_KEY_PATH', ':path');
Config::set('AM_KEY_BASENAME', ':basename');
Config::set('AM_KEY_PARENT', ':parent');
Config::set('AM_KEY_LEVEL', ':level');
Config::set('AM_KEY_TEMPLATE', ':template');
Config::set('AM_KEY_CURRENT_PAGE', ':current');
Config::set('AM_KEY_CURRENT_PATH', ':currentPath');
Config::set('AM_KEY_MTIME', ':mtime');
// Runtime variables Generated by template constructs.
Config::set('AM_KEY_FILTER', ':filter');
Config::set('AM_KEY_TAG', ':tag');
Config::set('AM_KEY_FILE', ':file');
Config::set('AM_KEY_WIDTH', ':width');
Config::set('AM_KEY_HEIGHT', ':height');
Config::set('AM_KEY_FILE_RESIZED', ':fileResized');
Config::set('AM_KEY_WIDTH_RESIZED', ':widthResized');
Config::set('AM_KEY_HEIGHT_RESIZED', ':heightResized');
Config::set('AM_KEY_CAPTION', ':caption');
Config::set('AM_KEY_INDEX', ':i');
Config::set('AM_KEY_FILELIST_COUNT', ':filelistCount');
Config::set('AM_KEY_PAGELIST_COUNT', ':pagelistCount');
Config::set('AM_KEY_PAGELIST_DISPLAY_COUNT', ':pagelistDisplayCount');
Config::set('AM_KEY_PAGINATION_COUNT', ':paginationCount');
Config::set('AM_KEY_NOW', ':now');

// HEADLESS
Config::set('AM_HEADLESS_ENABLED', false);
Config::set('AM_HEADLESS_TEMPLATE', '/automad/headless/json.php');
// For security reasons, the custom template should not have the .php extension.
Config::set('AM_HEADLESS_TEMPLATE_CUSTOM', '/config/headless.json');

// UPDATE
Config::set('AM_UPDATE_ITEMS', '/automad, /lib, /index.php, /packages/standard, /packages/tutorial');
Config::set('AM_UPDATE_BRANCH', 'master');
Config::set('AM_UPDATE_REPO_DOWNLOAD_URL', 'https://github.com/marcantondahmen/automad/archive');
Config::set('AM_UPDATE_REPO_RAW_URL', 'https://raw.githubusercontent.com/marcantondahmen/automad');
Config::set('AM_UPDATE_REPO_VERSION_FILE', '/automad/version.php');
Config::set('AM_UPDATE_TEMP', AM_DIR_CACHE . '/update');

// Version number 
include AM_BASE_DIR . '/automad/version.php';
