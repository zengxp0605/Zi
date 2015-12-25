<?php

use Illuminate\Database\Capsule\Manager as Capsule;

define('ROOT_PATH', __DIR__ . '/');
define('BASE_PATH', __DIR__ . '/');
  
$globalClassLoader = array('app', 'admin');

// Autoload 自动载入
$globalClassLoader = require ROOT_PATH . 'vendor/autoload.php';
//var_dump($autoload);
//
header('Content-type:text/html;charset=utf-8;');

// Eloquent ORM

$capsule = new Capsule;

$capsule->addConnection(require '../config/database.php');

$capsule->bootEloquent();


// 错误提示包
$whoops = new \Whoops\Run;

$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);

$whoops->register();