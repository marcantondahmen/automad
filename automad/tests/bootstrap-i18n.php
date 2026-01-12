<?php

use Automad\Autoload;
use Automad\Core\Config;

define('AUTOMAD', true);
define('AM_BASE_DIR', realpath(__DIR__ . '/../..'));
define('AM_BASE_URL', '');
define('AM_DIR_PACKAGES', '/automad/tests/i18n/packages');
define('AM_DIR_PAGES', '/automad/tests/i18n/pages');
define('AM_DIR_SHARED', '/automad/tests/i18n/shared');
define('AM_FILE_UI_TRANSLATION', '');
define('AM_FEED_ENABLED', false);
define('AM_I18N_ENABLED', true);
define('AM_DEBUG_ENABLED', false);
define('AM_REQUEST', '/');

require_once AM_BASE_DIR . '/automad/src/server/Autoload.php';
Autoload::init();
Config::init();
