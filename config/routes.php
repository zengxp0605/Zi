<?php

use Zi\Controller;
use Zi\Route;
 $c = new Controller();

//Macaw::error(function(){
//	echo 'This routes is undefined.';exit;
//});
//Macaw::get('fuck', function() {
//  echo "Match on fuck success!";
//});
//Macaw::get('', 'HomeController@home');
//Macaw::get('Home/help', 'HomeController@help');

Route::get('(:all)', function($fu) {
    echo 'Not Mactch<br>' . $fu;
});

Route::$useAutoRoute = true;

Route::$error_callback = function() {

    throw new Exception("路由无匹配项 404 Not Found");
};


Route::dispatch();
