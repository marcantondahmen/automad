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
if (isset($_SERVER['HTTP_X_FORWARDED_HOST']) || isset($_SERVER['HTTP_X_FORWARDED_SERVER'])) {
	// Add domain name in case the site is behind a proxy. 
	define('AM_BASE_URL', '/' . $_SERVER['HTTP_HOST'] . str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']));
} else {
	define('AM_BASE_URL', str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']));
}




@Debug::log('Server Software: ' . $_SERVER['SERVER_SOFTWARE']);

// Check whether pretty URLs are enabled.
if ((strpos(@strtolower($_SERVER['SERVER_SOFTWARE']), 'apache') !== false && file_exists(AM_BASE_DIR . '/.htaccess')) || strpos(@strtolower($_SERVER['SERVER_SOFTWARE']), 'nginx') !== false) {
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
Config::set('AM_FILE_PREFIX_CACHE', 'cached'); // Changing that constant will also require updating the .htaccess file! (for blocking direct access)
Config::set('AM_FILE_EXT_PAGE_CACHE', 'html');
Config::set('AM_FILE_SITE_SETTINGS', AM_BASE_DIR . AM_DIR_SHARED . '/site.' . AM_FILE_EXT_DATA); 
Config::set('AM_FILE_SITE_MTIME', AM_BASE_DIR . AM_DIR_CACHE . '/' . AM_FILE_PREFIX_CACHE . '_site_mtime');
Config::set('AM_FILE_OBJECT_CACHE', AM_BASE_DIR . AM_DIR_CACHE . '/' . AM_FILE_PREFIX_CACHE . '_automad_object');
Config::set('AM_FILE_ACCOUNTS', AM_BASE_DIR . '/config/accounts.php');
Config::set('AM_ALLOWED_FILE_TYPES', 'css, jpg, zip, png, ico, svg, js, pdf, mp3, gif');

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

// PLACEHOLDER TYPE IDENTIFIERS
Config::set('AM_PLACEHOLDER_PREFIX', 	'@');
Config::set('AM_PLACEHOLDER_INC',      	'i');
Config::set('AM_PLACEHOLDER_PAGE_VAR', 	'p');
Config::set('AM_PLACEHOLDER_SITE_VAR', 	's');
Config::set('AM_PLACEHOLDER_TOOL', 	't');
Config::set('AM_PLACEHOLDER_XTNSN', 	'x');

// REGEX
// There is no single regex for only matching tools. Instead, Tools get matched together with Extensions to maintain a correct order when parsing.
// The regex for Extensions only is used when scanning a template for .css/.js files.
Config::set('AM_REGEX_METHODS',  '/' . AM_PLACEHOLDER_PREFIX . '(' . AM_PLACEHOLDER_TOOL . '|' . AM_PLACEHOLDER_XTNSN . ')\(\s*([\w\-]+)\s*(\{.*?\})?\s*\)/s'); 	// @(t|x)((...)({...})) Tools & Extensions
Config::set('AM_REGEX_XTNSN',    '/' . AM_PLACEHOLDER_PREFIX . AM_PLACEHOLDER_XTNSN . 	 '\(\s*([\w\-]+)\s*(\{.*?\})?\s*\)/s');					// @x((...)({...})) Extensions Only
Config::set('AM_REGEX_INC'     , '/' . AM_PLACEHOLDER_PREFIX . AM_PLACEHOLDER_INC . 	 '\(\s*([\w\.\/\-]+)\s*\)/');						// @i((...))
Config::set('AM_REGEX_PAGE_VAR', '/' . AM_PLACEHOLDER_PREFIX . AM_PLACEHOLDER_PAGE_VAR . '\(\s*([\w\.\-]+)\s*\)/');						// @p((...))
Config::set('AM_REGEX_SITE_VAR', '/' . AM_PLACEHOLDER_PREFIX . AM_PLACEHOLDER_SITE_VAR . '\(\s*([\w\.\-]+)\s*\)/');						// @s((...))

// EXTENDER
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
// Hidden key (to identify the visibility status of a page in its txt file)
Config::set('AM_KEY_HIDDEN', 'hidden');
// Tags key (to identify tags in the page's txt file)
Config::set('AM_KEY_TAGS', 'tags');
// Theme key (to identify a theme in the page's txt file)
Config::set('AM_KEY_THEME', 'theme');
// Title key (to identify a title in the page's txt file)
Config::set('AM_KEY_TITLE', 'title');
// Sitename key (to identify the sitename in the site's txt file)
Config::set('AM_KEY_SITENAME', 'sitename');
// URL key (to identify an URL in the page's txt file)
Config::set('AM_KEY_URL', 'url');

// Version number 
include AM_BASE_DIR . '/automad/version.php';

// License key
Config::set('AM_LIC_KEY', '');

 
?>