<?php

use Automad\Autoload;
use Automad\Core\Config;

define('AUTOMAD', true);
define('AM_BASE_DIR', realpath(__DIR__ . '/../..'));
define('AM_DIR_PACKAGES', '/automad/tests/packages');
define('AM_FEED_ENABLED', false);
define('AM_REQUEST', '/');

require_once AM_BASE_DIR . '/automad/src/server/Autoload.php';
Autoload::init();
Config::overrides();
Config::defaults();
