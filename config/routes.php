<?php

use NoahBuscher\Macaw\Macaw;

//Macaw::error(function(){
//	echo 'This routes is undefined.';exit;
//});
//Macaw::get('fuck', function() {
//  echo "Match on fuck success!";
//});
//Macaw::get('', 'HomeController@home');
//Macaw::get('Home/help', 'HomeController@help');

Macaw::get('(:all)', function($fu) {
    echo 'Not Mactch<br>' . $fu;
});

Macaw::$useAutoRoute = true;

Macaw::$error_callback = function() {

    throw new Exception("路由无匹配项 404 Not Found");
};


Macaw::dispatch();
