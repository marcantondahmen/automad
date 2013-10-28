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
 *	Copyright (c) 2013 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
 *	Licensed under the MIT license.
 */


if (file_exists(BASE_DIR . '/.htaccess')) {
	// If .htaccess exists, assume that pretty URLs are enabled and remove /index.php from SCRIPT_NAME
	define('BASE_URL', str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']));
} else {
	// If not, use SCRIPT_NAME as base url
	define('BASE_URL', $_SERVER['SCRIPT_NAME']);
}


define('SITE_CONTENT_DIR', BASE_DIR . '/content');
define('SITE_SETTINGS_FILE', SITE_CONTENT_DIR . '/settings.txt'); 
define('SITE_PAGES_DIR', SITE_CONTENT_DIR . '/pages');
define('SITE_THEMES_DIR', BASE_DIR . '/themes');
define('SITE_DEFAULT_NAME', 'Automad');
define('SITE_DEFAULT_THEME', 'standard');
define('SITE_ERROR_PAGE_TITLE', '404');
define('SITE_RESULTS_PAGE_TITLE', 'Search Results');
define('SITE_RESULTS_PAGE_URL', '/results');


define('TEMPLATE_DEFAULT_DIR', BASE_DIR . '/automad/templates');
define('TEMPLATE_DEFAULT_NAME', 'default');
define('TEMPLATE_VAR_DELIMITER_LEFT', '[');
define('TEMPLATE_VAR_DELIMITER_RIGHT', ']');
define('TEMPLATE_FN_DELIMITER_LEFT', '$[');
define('TEMPLATE_FN_DELIMITER_RIGHT', ']');


define('HTML_CLASS_NAV', 'nav');
define('HTML_CLASS_PREV', 'prev');
define('HTML_CLASS_NEXT', 'next');
define('HTML_CLASS_FILTER', 'filter');
define('HTML_CLASS_TREE', 'tree');
define('HTML_CLASS_LIST', 'list');
define('HTML_CLASS_SORT', 'sort');
define('HTML_CLASS_HOME', 'home');
define('HTML_CLASS_CURRENT', 'current');
define('HTML_CLASS_CURRENT_PATH', 'currentPath');
define('HTML_CLASS_BREADCRUMBS', 'breadcrumbs');
define('HTML_CLASS_SEARCH', 'search');
define('HTML_BREADCRUMB_SEPARATOR', ' &gt; ');
define('HTML_SEARCH_PLACEHOLDER', 'Search ...');
define('HTML_FILTERS_ALL', 'All');
define('HTML_SORT_ASC', 'ascending');
define('HTML_SORT_DESC', 'descending');
define('HTML_LIST_MAX_STR_LENGTH', 150);
define('HTML_DEFAULT_SORT_DIR', 'sort_asc');
define('HTML_DEFAULT_SORT_TYPES', 'Original Order, title: By Title');


define('DATA_FILE_EXTENSION', 'txt');
define('DATA_BLOCK_SEPARATOR', '---');
define('DATA_PAIR_SEPARATOR', ':');
define('DATA_OPTION_SEPARATOR', ',');
define('DATA_TAG_SEPARATOR', ',');
define('DATA_TAGS_KEY', 'tags');

 
include(BASE_DIR . '/automad/version.php');
 
 
?>
