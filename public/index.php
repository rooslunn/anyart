<?php

$APP_ROOT = dirname(__DIR__);

require_once $APP_ROOT .'/vendor/autoload.php';

use Rkladko\Anyart\Lib\Core;

$db_credo = require $APP_ROOT . '/config/db.php';
$app = new Core($db_credo);