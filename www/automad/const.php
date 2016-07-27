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
 *	Copyright (c) 2014 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 *	http://automad.org/license
 */


namespace Automad\Core;


defined('AUTOMAD') or die('Direct access not permitted!');




// Set config file.
define('AM_CONFIG', AM_BASE_DIR . '/config/config.json');

// Parse AM_CONFIG to set user overrides for the below defined constants.
Config::json(AM_CONFIG);

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




// Get the requested URL.
define('AM_REQUEST', Parse::request());




// Define all constants which are not defined yet by the config file.
// DIR
Config::set('AM_DIR_PAGES', '/pages');
Config::set('AM_DIR_SHARED', '/shared');
Config::set('AM_DIR_THEMES', '/themes');
Config::set('AM_DIR_CACHE', '/cache');
Config::set('AM_DIR_CACHE_PAGES', AM_DIR_CACHE . '/pages');
Config::set('AM_DIR_CACHE_IMAGES', AM_DIR_CACHE . '/images');
Config::set('AM_DIR_TRASH', AM_DIR_CACHE . '/trash');
Config::set('AM_DIR_GUI_INC', '/automad/gui/inc');

// FILE
Config::set('AM_FILE_EXT_DATA', 'txt'); // Changing that constant will also require updating the .htaccess file! (for blocking direct access)	
Config::set('AM_FILE_EXT_CAPTION', 'caption');
Config::set('AM_FILE_PREFIX_CACHE', 'cached'); // Changing that constant will also require updating the .htaccess file! (for blocking direct access)
Config::set('AM_FILE_EXT_PAGE_CACHE', 'html');
Config::set('AM_FILE_SHARED_DATA', AM_BASE_DIR . AM_DIR_SHARED . '/data.' . AM_FILE_EXT_DATA); 
Config::set('AM_FILE_SITE_MTIME', AM_BASE_DIR . AM_DIR_CACHE . '/' . AM_FILE_PREFIX_CACHE . '_site_mtime');
Config::set('AM_FILE_OBJECT_CACHE', AM_BASE_DIR . AM_DIR_CACHE . '/' . AM_FILE_PREFIX_CACHE . '_automad_object');
Config::set('AM_FILE_ACCOUNTS', AM_BASE_DIR . '/config/accounts.php');
Config::set('AM_FILE_GUI_TEXT_MODULES', AM_BASE_DIR . '/automad/gui/lang/en.txt');
Config::set('AM_ALLOWED_FILE_TYPES', 			'css, jpg, zip, png, ico, svg, js, pdf, mp3, gif');
Config::set('AM_ALLOWED_FILE_TYPES_DEFAULT_GUI', 	'css, jpg, zip, png, ico, svg, js, pdf, mp3, gif'); // To be used in case a user overrides the values above and wants to restore the original settings.

// PAGE
Config::set('AM_PAGE_NOT_FOUND_TEMPLATE', 'page_not_found');
Config::set('AM_PAGE_NOT_FOUND_TITLE', 'Page Not Found!');
Config::set('AM_PAGE_RESULTS_TEMPLATE', 'search_results');
Config::set('AM_PAGE_RESULTS_TITLE', 'Search Results');
Config::set('AM_PAGE_RESULTS_URL', '/search-results');
Config::set('AM_PAGE_GUI', '/gui');

// CACHE
Config::set('AM_CACHE_ENABLED', true);
Config::set('AM_CACHE_MONITOR_DELAY', 120);
Config::set('AM_CACHE_LIFETIME', 3600);

// IMAGE
Config::set('AM_IMG_JPG_QUALITY', 90);

// LISTING DEFAULTS
Config::set('AM_LIST_DEFAULT_SORT_ORDER', 'desc');

// TEMPLATE DELIMITERS
Config::set('AM_DEL_VAR_OPEN', '@{');
Config::set('AM_DEL_VAR_CLOSE', '}');
Config::set('AM_DEL_STATEMENT_OPEN', '<@');
Config::set('AM_DEL_STATEMENT_CLOSE', '@>');
Config::set('AM_DEL_COMMENT_OPEN', '<#');
Config::set('AM_DEL_COMMENT_CLOSE', '#>');

// EXTENSIONS
Config::set('AM_NAMESPACE_EXTENSIONS', '\\Extensions');

// HTML
Config::set('AM_HTML_CLASS_NAV', 'nav');
Config::set('AM_HTML_CLASS_PREV', 'prev');
Config::set('AM_HTML_CLASS_NEXT', 'next');
Config::set('AM_HTML_CLASS_FILTER', 'filter');
Config::set('AM_HTML_CLASS_TREE', 'tree');
Config::set('AM_HTML_CLASS_IMAGE_WRAPPER', 'img-wrapper');
Config::set('AM_HTML_CLASS_LIST', 'list');
Config::set('AM_HTML_CLASS_LIST_HEADER', 'list-header');
Config::set('AM_HTML_CLASS_LIST_ITEM', 'item');
Config::set('AM_HTML_CLASS_LIST_ITEM_IMG', 'img-responsive');
Config::set('AM_HTML_CLASS_LIST_ITEM_DATA', 'data');
Config::set('AM_HTML_CLASS_SORT', 'sort');
Config::set('AM_HTML_CLASS_HOME', 'home');
Config::set('AM_HTML_CLASS_CURRENT', 'current');
Config::set('AM_HTML_CLASS_CURRENT_PATH', 'currentPath');
Config::set('AM_HTML_CLASS_BREADCRUMBS', 'breadcrumbs');
Config::set('AM_HTML_CLASS_SEARCH', 'search');
Config::set('AM_HTML_CLASS_SEARCH_INPUT', 'search-input');
Config::set('AM_HTML_CLASS_SEARCH_BUTTON', 'search-button');
Config::set('AM_HTML_STR_BREADCRUMB_SEPARATOR', '&gt;');
Config::set('AM_HTML_TEXT_FILTER_ALL', 'All');
Config::set('AM_HTML_LIST_MAX_CHARS', 150);

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
Config::set('AM_KEY_HIDDEN', 'hidden');
Config::set('AM_KEY_TAGS', 'tags');
Config::set('AM_KEY_THEME', 'theme');
Config::set('AM_KEY_TITLE', 'title');
Config::set('AM_KEY_SITENAME', 'sitename');
Config::set('AM_KEY_URL', 'url');
// System variables depending on a context.
Config::set('AM_KEY_PATH', ':path');
Config::set('AM_KEY_BASENAME', ':basename');
Config::set('AM_KEY_PARENT', ':parent');
Config::set('AM_KEY_LEVEL', ':level');
Config::set('AM_KEY_TEMPLATE', ':template');
Config::set('AM_KEY_CURRENT_PAGE', ':current');
Config::set('AM_KEY_CURRENT_PATH', ':current-path');
Config::set('AM_KEY_MTIME', ':mtime');
// Independent system variables Generated by template constructs.
Config::set('AM_KEY_FILTER', ':filter');
Config::set('AM_KEY_TAG', ':tag');
Config::set('AM_KEY_FILE', ':file');
Config::set('AM_KEY_CAPTION', ':caption');
Config::set('AM_KEY_INDEX', ':i');
Config::set('AM_KEY_FILELIST_COUNT', ':filelist-count');
Config::set('AM_KEY_PAGELIST_COUNT', ':pagelist-count');

// Version number 
include AM_BASE_DIR . '/automad/version.php';

 
?>