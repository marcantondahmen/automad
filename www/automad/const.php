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
 *	AUTOMAD CMS
 *
 *	Copyright (c) 2014 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 */


namespace Core;


defined('AUTOMAD') or die('Direct access not permitted!');


require AM_BASE_DIR . '/automad/core/config.php';


// Set config file
Config::set('AM_CONFIG', AM_BASE_DIR . '/config/config.json');

// Parse AM_CONFIG to set user overrides for the below defined constants.
Config::json(AM_CONFIG);

// Base URL for all URLs relative to the root
Config::set('AM_BASE_URL', str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']));

// Pretty URLs
if (file_exists(AM_BASE_DIR . '/.htaccess')) {
	// If .htaccess exists, assume that pretty URLs are enabled and AM_INDEX is empty
	Config::set('AM_INDEX', '');
} else {
	// If not, AM_INDEX will be defined
	Config::set('AM_INDEX', '/index.php');
}

// DEBUG
Config::set('AM_DEBUG_ENABLED', false);

// DIR
Config::set('AM_DIR_PAGES', '/pages');
Config::set('AM_DIR_SHARED', '/shared');
Config::set('AM_DIR_THEMES', '/themes');
Config::set('AM_DIR_CACHE', '/cache');
Config::set('AM_DIR_CACHE_PAGES', AM_DIR_CACHE . '/pages');
Config::set('AM_DIR_CACHE_IMAGES', AM_DIR_CACHE . '/images');
Config::set('AM_DIR_TRASH', AM_DIR_CACHE . '/trash');

// FILE
Config::set('AM_FILE_EXT_DATA', 'txt'); // Changing that constant will also require updating the .htaccess file! (for blocking direct access)	
Config::set('AM_FILE_PREFIX_CACHE', 'cached'); // Changing that constant will also require updating the .htaccess file! (for blocking direct access)
Config::set('AM_FILE_EXT_PAGE_CACHE', 'html');
Config::set('AM_FILE_SITE_SETTINGS', AM_BASE_DIR . AM_DIR_SHARED . '/site.' . AM_FILE_EXT_DATA); 
Config::set('AM_FILE_SITE_MTIME', AM_BASE_DIR . AM_DIR_CACHE . '/' . AM_FILE_PREFIX_CACHE . '_site_mtime');
Config::set('AM_FILE_SITE_OBJECT_CACHE', AM_BASE_DIR . AM_DIR_CACHE . '/' . AM_FILE_PREFIX_CACHE . '_site_object');
Config::set('AM_FILE_ACCOUNTS', AM_BASE_DIR . '/config/accounts.php');
Config::set('AM_ALLOWED_FILE_TYPES', 'css, jpg, zip, png, svg, js, pdf, mp3, gif');

// PAGE
Config::set('AM_PAGE_ERROR_TEMPLATE', 'error');
Config::set('AM_PAGE_ERROR_TITLE', '404');
Config::set('AM_PAGE_RESULTS_TEMPLATE', 'search_results');
Config::set('AM_PAGE_RESULTS_TITLE', 'Search Results');
Config::set('AM_PAGE_RESULTS_URL', '/search_results');
Config::set('AM_PAGE_GUI', '/gui');

// CACHE
Config::set('AM_CACHE_ENABLED', true);
Config::set('AM_CACHE_MONITOR_DELAY', 60);

// IMAGE
Config::set('AM_IMG_JPG_QUALITY', 90);

// LISTING DEFAULTS
Config::set('AM_LIST_DEFAULT_SORT_ORDER', 'desc');

// TEMPLATE DELIMITERS
// Includes
Config::set('AM_TMPLT_DEL_INC_L', 'i(');
Config::set('AM_TMPLT_DEL_INC_R', ')');
// Page Variables
Config::set('AM_TMPLT_DEL_PAGE_VAR_L', 'p(');
Config::set('AM_TMPLT_DEL_PAGE_VAR_R', ')');
// Site Variables
Config::set('AM_TMPLT_DEL_SITE_VAR_L', 's(');
Config::set('AM_TMPLT_DEL_SITE_VAR_R', ')');
// Toolbox
Config::set('AM_TMPLT_DEL_TOOL_L', 't(');
Config::set('AM_TMPLT_DEL_TOOL_R', ')');
// Extensions
Config::set('AM_TMPLT_DEL_XTNSN_L', 'x(');
Config::set('AM_TMPLT_DEL_XTNSN_R', ')');

// EXTENDER
Config::set('AM_NAMESPACE_EXTENSIONS', '\\Extensions');

// HTML
Config::set('AM_HTML_CLASS_NAV', 'nav');
Config::set('AM_HTML_CLASS_PREV', 'prev');
Config::set('AM_HTML_CLASS_NEXT', 'next');
Config::set('AM_HTML_CLASS_FILTER', 'filter');
Config::set('AM_HTML_CLASS_TREE', 'tree');
Config::set('AM_HTML_CLASS_LIST', 'list');
Config::set('AM_HTML_CLASS_IMAGESET', 'imageset');
Config::set('AM_HTML_CLASS_SORT', 'sort');
Config::set('AM_HTML_CLASS_HOME', 'home');
Config::set('AM_HTML_CLASS_CURRENT', 'current');
Config::set('AM_HTML_CLASS_CURRENT_PATH', 'currentPath');
Config::set('AM_HTML_CLASS_BREADCRUMBS', 'breadcrumbs');
Config::set('AM_HTML_CLASS_SEARCH', 'search');
Config::set('AM_HTML_STR_BREADCRUMB_SEPARATOR', '<span class="separator"> &gt; </span>');
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