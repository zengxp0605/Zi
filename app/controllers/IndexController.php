<?php namespace App\Controllers;

/**
* IndexController
*/
class IndexController extends BaseController
{

  public function __construct()
  {
	
  }

  public function index(){
	 // echo $this->id;
	 echo $this->__CONTROLLER__ . '----'.$this->__ACTION__;
	
  }

  public function test(){
	
  }
}