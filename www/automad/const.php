<?php defined('AUTOMAD') or die('Direct access not permitted!');
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
 *	AUTOMAD CMS
 *
 *	Copyright (c) 2013 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 */


// Base URL for all URLs relative to the root
define('AM_BASE_URL', str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']));


// Pretty URLs
if (file_exists(AM_BASE_DIR . '/.htaccess')) {
	// If .htaccess exists, assume that pretty URLs are enabled and AM_INDEX is empty
	define('AM_INDEX', '');
} else {
	// If not, AM_INDEX will be defined
	define('AM_INDEX', '/index.php');
}


// DEBUG
if (!defined('AM_DEBUG_ENABLED')) {
	define('AM_DEBUG_ENABLED', false);
}
if (!defined('AM_DEBUG_CONSOLE')) {
	define('AM_DEBUG_CONSOLE', false);
}


// DIR
// Pages
if (!defined('AM_DIR_PAGES')) {
	define('AM_DIR_PAGES', '/pages');
}
// Shared
if (!defined('AM_DIR_SHARED')) {
	define('AM_DIR_SHARED', '/shared');
}
// Themes
if (!defined('AM_DIR_THEMES')) {
	define('AM_DIR_THEMES', '/themes');
}
// Cache
if (!defined('AM_DIR_CACHE')) {
	define('AM_DIR_CACHE', '/cache');
}
// Default template directory
if (!defined('AM_DIR_DEFAULT_TEMPLATES')) {
	define('AM_DIR_DEFAULT_TEMPLATES', '/automad/templates');
}


// FILE
// Sidewide settings/variable
if (!defined('AM_FILE_SITE_SETTINGS')) {
	define('AM_FILE_SITE_SETTINGS', AM_BASE_DIR . AM_DIR_SHARED . '/site.txt'); 
}
// Site modification time
if (!defined('AM_FILE_SITE_MTIME')) {
	define('AM_FILE_SITE_MTIME', AM_BASE_DIR . AM_DIR_CACHE . '/cached_site_mtime');
}
// Site object cache
if (!defined('AM_FILE_SITE_OBJECT_CACHE')) {
	define('AM_FILE_SITE_OBJECT_CACHE', AM_BASE_DIR . AM_DIR_CACHE . '/cached_site_object');
}
// Default template
if (!defined('AM_FILE_DEFAULT_TEMPLATE')) {
	define('AM_FILE_DEFAULT_TEMPLATE', AM_BASE_DIR . AM_DIR_DEFAULT_TEMPLATES . '/default.php');
}
// Cache file prefix
if (!defined('AM_FILE_PREFIX_CACHE')) {
	define('AM_FILE_PREFIX_CACHE', 'cached');
}
// Cache file extension
if (!defined('AM_FILE_EXT_PAGE_CACHE')) {
	define('AM_FILE_EXT_PAGE_CACHE', 'html');
}
// Data file extension
if (!defined('AM_FILE_EXT_DATA')) {
	define('AM_FILE_EXT_DATA', 'txt');
}


// PAGE
// Title for 404 page
if (!defined('AM_PAGE_ERROR_TITLE')) {
	define('AM_PAGE_ERROR_TITLE', '404');
}
// Title for search results page
if (!defined('AM_PAGE_RESULTS_TITLE')) {
	define('AM_PAGE_RESULTS_TITLE', 'Search Results');
}
// URL of search results page
if (!defined('AM_PAGE_RESULTS_URL')) {
	define('AM_PAGE_RESULTS_URL', '/results');
}


// CACHE
// Enable cache
if (!defined('AM_CACHE_ENABLED')) {
	define('AM_CACHE_ENABLED', true);
}
// Site modification time check delay (seconds)
if (!defined('AM_CACHE_MONITOR_DELAY')) {
	define('AM_CACHE_MONITOR_DELAY', 60);
}


// IMAGE
// Default jpg quality
if (!defined('AM_IMG_JPG_QUALITY')) {
	define('AM_IMG_JPG_QUALITY', 90);
}


// TOOL KEYS
// String to be used within the options to define a filename/filepath
if (!defined('AM_TOOL_OPTION_KEY_FILENAME')) {
	define('AM_TOOL_OPTION_KEY_FILENAME', 'file');
}
// String to be used within the options to define a width
if (!defined('AM_TOOL_OPTION_KEY_WIDTH')) {
	define('AM_TOOL_OPTION_KEY_WIDTH', 'width');
}
// String to be used within the options to define a height
if (!defined('AM_TOOL_OPTION_KEY_HEIGHT')) {
	define('AM_TOOL_OPTION_KEY_HEIGHT', 'height');
}
// String to be used within the options to define the crop parameter
if (!defined('AM_TOOL_OPTION_KEY_CROP')) {
	define('AM_TOOL_OPTION_KEY_CROP', 'crop');
}
// String to be used within the options to define a link
if (!defined('AM_TOOL_OPTION_KEY_LINK')) {
	define('AM_TOOL_OPTION_KEY_LINK', 'link');
}
// String to be used within the options to define a link target
if (!defined('AM_TOOL_OPTION_KEY_TARGET')) {
	define('AM_TOOL_OPTION_KEY_TARGET', 'target');
}


// TOOL OPTIONS
// Default Tool options
if (!defined('AM_TOOL_OPTIONS_IMG')) {
	define('AM_TOOL_OPTIONS_IMG', AM_TOOL_OPTION_KEY_FILENAME . ': , ' . AM_TOOL_OPTION_KEY_WIDTH . ': , ' . AM_TOOL_OPTION_KEY_HEIGHT . ': , ' . AM_TOOL_OPTION_KEY_CROP . ': 0, ' . AM_TOOL_OPTION_KEY_LINK . ': , ' . AM_TOOL_OPTION_KEY_TARGET . ': ');
}
// Placeholder text for search field
if (!defined('AM_TOOL_OPTIONS_SEARCH')) {
	define('AM_TOOL_OPTIONS_SEARCH', 'Search ...');
}
// Default sort types
if (!defined('AM_TOOL_OPTIONS_SORT_TYPE')) {
	define('AM_TOOL_OPTIONS_SORT_TYPE', 'Original Order, title: By Title');
}
// Default sort directions text
if (!defined('AM_TOOL_OPTIONS_SORT_DIR')) {
	define('AM_TOOL_OPTIONS_SORT_DIR', 'SORT_ASC: Sort Ascending, SORT_DESC: Sort Descending');
}


// TOOL DEFAULTS
// Default sort direction
if (!defined('AM_TOOL_DEFAULT_SORT_DIR')) {
	define('AM_TOOL_DEFAULT_SORT_DIR', 'sort_asc');
}


// TEMPLATE DEFAULTS
// Left delimiter for page variables
if (!defined('AM_TMPLT_DEL_VAR_L')) {
	define('AM_TMPLT_DEL_VAR_L', 'p[');
}
// Right delimiter for page variables
if (!defined('AM_TMPLT_DEL_VAR_R')) {
	define('AM_TMPLT_DEL_VAR_R', ']');
}
// Left delimiter for toolbox functions
if (!defined('AM_TMPLT_DEL_TOOL_L')) {
	define('AM_TMPLT_DEL_TOOL_L', 't[');
}
// Right delimiter for toolbox functions
if (!defined('AM_TMPLT_DEL_TOOL_R')) {
	define('AM_TMPLT_DEL_TOOL_R', ']');
}


// HTML
// Navigation class
if (!defined('AM_HTML_CLASS_NAV')) {
	define('AM_HTML_CLASS_NAV', 'nav');
}
// Previous page link class
if (!defined('AM_HTML_CLASS_PREV')) {
	define('AM_HTML_CLASS_PREV', 'prev');
}
// Next page link class
if (!defined('AM_HTML_CLASS_NEXT')) {
	define('AM_HTML_CLASS_NEXT', 'next');
}
// Filter menu class
if (!defined('AM_HTML_CLASS_FILTER')) {
	define('AM_HTML_CLASS_FILTER', 'filter');
}
// Navigation tree class
if (!defined('AM_HTML_CLASS_TREE')) {
	define('AM_HTML_CLASS_TREE', 'tree');
}
// Page list class
if (!defined('AM_HTML_CLASS_LIST')) {
	define('AM_HTML_CLASS_LIST', 'list');
}
// Sort menu class
if (!defined('AM_HTML_CLASS_SORT')) {
	define('AM_HTML_CLASS_SORT', 'sort');
}
// Class for link to Home page in navigation 
if (!defined('AM_HTML_CLASS_HOME')) {
	define('AM_HTML_CLASS_HOME', 'home');
}
// Class for current page in navigation
if (!defined('AM_HTML_CLASS_CURRENT')) {
	define('AM_HTML_CLASS_CURRENT', 'current');
}
// Class for a page within the path of the current page in the navigation
if (!defined('AM_HTML_CLASS_CURRENT_PATH')) {
	define('AM_HTML_CLASS_CURRENT_PATH', 'currentPath');
}
// Breadcrumbs class
if (!defined('AM_HTML_CLASS_BREADCRUMBS')) {
	define('AM_HTML_CLASS_BREADCRUMBS', 'breadcrumbs');
}
// Search form class
if (!defined('AM_HTML_CLASS_SEARCH')) {
	define('AM_HTML_CLASS_SEARCH', 'search');
}
// Breadcrumbs items separator
if (!defined('AM_HTML_STR_BREADCRUMB_SEPARATOR')) {
	define('AM_HTML_STR_BREADCRUMB_SEPARATOR', ' &gt; ');
}
// Filter menu text for "all items"
if (!defined('AM_HTML_TEXT_FILTER_ALL')) {
	define('AM_HTML_TEXT_FILTER_ALL', 'All');
}
// Max characters in list output
if (!defined('AM_HTML_LIST_MAX_CHARS')) {
	define('AM_HTML_LIST_MAX_CHARS', 150);
}


// PARSE
// Block separator - separates all key/value pairs
if (!defined('AM_PARSE_BLOCK_SEPARATOR')) {
	define('AM_PARSE_BLOCK_SEPARATOR', '---');
}
// Pair separator - separates the key from the value
if (!defined('AM_PARSE_PAIR_SEPARATOR')) {
	define('AM_PARSE_PAIR_SEPARATOR', ':');
}
// Tool options separator
if (!defined('AM_PARSE_OPTION_SEPARATOR')) {
	define('AM_PARSE_OPTION_SEPARATOR', ',');
}
// Tags separator
if (!defined('AM_PARSE_TAG_SEPARATOR')) {
	define('AM_PARSE_TAG_SEPARATOR', ',');
}
// Tags key (to identify tags in the page's txt file)
if (!defined('AM_PARSE_TAGS_KEY')) {
	define('AM_PARSE_TAGS_KEY', 'tags');
}
// List of file extensions to identify file in URL
if (!defined('AM_PARSE_REGISTERED_FILE_EXTENSIONS')) {
	define('AM_PARSE_REGISTERED_FILE_EXTENSIONS', serialize(array('css', 'jpg', 'zip', 'png', 'svg', 'js', 'pdf', 'mp3')));
}

 
include(AM_BASE_DIR . '/automad/version.php');
 
 
?>
