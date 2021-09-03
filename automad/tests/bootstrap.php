<?php

namespace Automad;

define('AUTOMAD', true);
define('AM_BASE_DIR', __DIR__ . '/../..');
define('AM_HEADLESS_TEMPLATE', '/automad/tests/templates/headless/json.php');
define('AM_HEADLESS_TEMPLATE_CUSTOM', AM_HEADLESS_TEMPLATE);
require_once AM_BASE_DIR . '/automad/src/Autoload.php';
Autoload::init();
require AM_BASE_DIR . '/automad/const.php';
