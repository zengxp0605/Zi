<?php
	define('ROOT_PATH',__DIR__ .'/../');

	// Autoload 自动载入
	$globalClassLoader = require '../vendor/autoload.php';
//var_dump($autoload);
	// 路由配置
	require '../config/routes.php';