<?php namespace App\Controllers;
/**
* HomeController
*/
class HomeController extends BaseController
{
	
	public function index()
  {
    echo "<h1>Home index</h1>";
	
  }
  public function home2()
  {
    echo "<h1>Home control success.</h1>";
  }
  public function help()
  {

    echo "<h1>Home -- help.</h1>";
	$this->redirect('test');
  }
}