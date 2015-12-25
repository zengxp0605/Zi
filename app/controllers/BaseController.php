<?php

namespace App\Controllers;

use Zi\Controller;
use Zi\View;
/**
 * BaseController
 */
class BaseController extends Controller {

    protected $view;

    public function __construct() {
        
    }

    public function __destruct() {

        $view = $this->view;
       // var_dump($$view->data);exit;
        if ($view instanceof View) {

            extract($view->data);
            //die(var_dump($view->data['article']->id));
            require $view->view;
        }
    }

}
