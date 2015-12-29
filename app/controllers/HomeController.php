<?php

namespace App\Controllers;

use App\Model\Article;
use Zi\View;
use Zi\Db\Redis;
use Zi\Cache;

/**
 * HomeController
 */
class HomeController extends BaseController {

    public function index() {
        //Redis::set('key1','test111',10,'s');
        //echo Redis::get('test');
        echo "<h1>Home index</h1>";
        $this->detail = Article::first();
    }

    public function index2() {
        $this->view = View::make('Home.index2')
                ->with('article', Article::first())
                ->withTitle('MFFC :-D')
                ->withFuckMe('OK!');
    }

    public function help($id, $test = 'ttt', $ng = 'ngDefault', $_controller = null) {
        echo "<h1>Home -- help.</h1>";
        var_dump($id, $test, $ng);
        echo '<hr/>';
        var_dump($_controller, $this->__CONTROLLER__, $this->__ACTION__);
        //$this->redirect('test');
        return 'Index/test';
    }

    public function test($id, $tmp1 = '', $tmp2 = 'tmp2') {

        //Cache::set('test', '222','30');
        echo Cache::get('test'),'<hr/>';
        var_dump($id, $tmp1, $tmp2);
        
        //$this->redirect('test');
    }

}
