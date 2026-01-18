<?php

use Automad\Autoload;
use Automad\Core\Config;

define('AUTOMAD', true);
define('AUTOMAD_CONSOLE', true);
define('AM_BASE_DIR', realpath(__DIR__ . '/../..'));
define('AM_BASE_URL', '');
define('AM_DIR_PACKAGES', '/automad/tests/main/packages');
define('AM_DIR_PAGES', '/automad/tests/main/data');
define('AM_DIR_SHARED', '/automad/tests/main/shared');
define('AM_FILE_UI_TRANSLATION', '');
define('AM_FEED_ENABLED', false);
define('AM_I18N_ENABLED', false);
define('AM_REQUEST', '/page');
define('AM_SERVER', 'http://localhost');
define('AM_DEBUG_ENABLED', false);
define('AM_VERSION', '0.0.0');

require_once AM_BASE_DIR . '/automad/src/server/Autoload.php';
Autoload::init();
Config::init();
