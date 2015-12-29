<?php

namespace App\Controllers;

use Zi\Config;
use Zi\Log;

/**
 * IndexController
 */
class IndexController extends BaseController {

    public function __construct() {
        
    }

    public function index() {
        Config::setConfigPath(BASE_PATH . 'config/');
        //Config::setFileFormat('json');
        $t = Config::get('test.tt1.tt2.tt3');
        var_dump($t);
        //Log::debug($t);
        // echo $this->id;
        echo $this->__CONTROLLER__ . '----' . $this->__ACTION__;
        return false;
    }

    public function test() {
        //echo  'index/test';
      
    }

}
