<?php

if (file_exists(__DIR__ . '/cache/lock')) {
	exit('<h1>This site is currently under maintenance.</h1>');
}

define('AUTOMAD', true);
define('AM_BASE_DIR', __DIR__);
require AM_BASE_DIR . '/automad/init.php';

?>
