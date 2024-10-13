<?php

const ROOT = '/var/www/html';
define('NOREDIRECTBYMAINTOLOGIN', 1);
define('TEST_ENV_SETUP', 1);
require_once ROOT . '/main.inc.php';

require_once ROOT . '/core/lib/admin.lib.php';

// Require tested class
const MODULE_ROOT = ROOT . '/custom/albatross';