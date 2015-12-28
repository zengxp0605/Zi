<?php
use Zi\Route;
// 定义 PUBLIC_PATH
define('PUBLIC_PATH', __DIR__);

define('DEBUG',TRUE);

// 启动器
require PUBLIC_PATH . '/../bootstrap.php';

// 路由
Route::dispatch();
