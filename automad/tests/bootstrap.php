<?php

use Automad\App;
use Automad\Autoload;
use Automad\Core\Config;

define('AUTOMAD', true);
define('AM_BASE_DIR', realpath(__DIR__ . '/../..'));
define('AM_BASE_URL', '');
define('AM_DIR_PACKAGES', '/automad/tests/packages');
define('AM_DIR_PAGES', '/automad/tests/data');
define('AM_DIR_SHARED', '/automad/tests/shared');
define('AM_FEED_ENABLED', false);
define('AM_I18N_ENABLED', false);
define('AM_REQUEST', '/page');

require_once AM_BASE_DIR . '/automad/src/server/Autoload.php';
Autoload::init();
Config::overrides();
Config::defaults();
