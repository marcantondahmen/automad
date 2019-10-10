<?php 

define('AUTOMAD', true);
define('AM_BASE_DIR', __DIR__ . '/../..');
define('AM_HEADLESS_TEMPLATE', '/automad/tests/headless/json.php');
define('AM_HEADLESS_TEMPLATE_CUSTOM', AM_HEADLESS_TEMPLATE);
require AM_BASE_DIR . '/automad/autoload.php';
require AM_BASE_DIR . '/automad/const.php';