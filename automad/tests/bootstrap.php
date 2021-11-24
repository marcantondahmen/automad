<?php

use Automad\Autoload;
use Automad\Core\Config;

define('AUTOMAD', true);
define('AM_BASE_DIR', realpath(__DIR__ . '/../..'));
define('AM_DIR_PACKAGES', '/automad/tests/packages');
define('AM_HEADLESS_TEMPLATE', '/automad/tests/packages/templates/headless/json.php');
define('AM_HEADLESS_TEMPLATE_CUSTOM', AM_HEADLESS_TEMPLATE);
define('AM_FEED_ENABLED', false);

require_once AM_BASE_DIR . '/automad/src/Autoload.php';
Autoload::init();
Config::overrides();
Config::defaults();
