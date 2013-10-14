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
 *	(c) 2013 by Marc Anton Dahmen
 *	http://marcdahmen.de
 *
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
define('SITE_PAGES_DIR', 'pages');
define('SITE_THEMES_DIR', 'themes');


define('TEMPLATE_DEFAULT_DIR', 'automad/templates');
define('TEMPLATE_DEFAULT_NAME', 'default');
define('TEMPLATE_VAR_DELIMITER_LEFT', '$(');
define('TEMPLATE_VAR_DELIMITER_RIGHT', ')');
define('TEMPLATE_FN_DELIMITER_LEFT', '$[');
define('TEMPLATE_FN_DELIMITER_RIGHT', ']');


define('DATA_FILE_EXTENSION', 'txt');
define('DATA_BLOCK_SEPARATOR', '---');
define('DATA_PAIR_SEPARATOR', ':');
define('DATA_TAG_SEPARATOR', ',');
define('DATA_TAGS_KEY', 'tags');

 
include(BASE_DIR . '/automad/version.php');
 
 
?>
