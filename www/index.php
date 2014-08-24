<?php

if (version_compare(PHP_VERSION, '5.3.3') >= 0) {

	define('AUTOMAD', true);
	define('AM_BASE_DIR', __DIR__);
	require AM_BASE_DIR . '/automad/init.php';

} else {
	
	die('Please update your PHP version to 5.3.3 or higher! (Your current version is ' . PHP_VERSION . ')');
	
}

?>
