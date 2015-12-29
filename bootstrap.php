<?php

use Illuminate\Database\Capsule\Manager as Capsule;


define('BASE_PATH', __DIR__ . '/');
define('CACHE_PATH', __DIR__ . '/data/runtime/cache/');

// Autoload 自动载入
require BASE_PATH . 'vendor/autoload.php';

// Eloquent ORM
//$capsule = new Capsule;
//$capsule->addConnection(require '../config/database.php');
//$capsule->bootEloquent();


// 错误提示包
//$whoops = new \Whoops\Run;
//$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
//$whoops->register();


