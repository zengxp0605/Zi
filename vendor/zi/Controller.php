<?php namespace Zi;
/**
* Controller
*/
class Controller
{
  public $uriParams = '';
  public $__VAP__ = '';  //__VIEW_ABSOLUTE_PATH__


  public function __construct()
  {
	  
  }
	
  public function __init__(){
	$this->__VAP__ = ROOT_PATH . 'app/views/';
	$this->__PUBLIC__ = '/';
  }

  public function redirect($uri){
	 $this->uriParams = $this->uriParams ? '/' .implode($this->uriParams,'/') : '';
	 header('Location: /'.$uri . $this->uriParams);
  }

  public function display($tpl = null){ 
	switch($tpl){
		//case false:  // ���� null �� false �ȼ�?? �޷��жϲ�ʹ��ģ������...
		//	exit;
		case true:
		case null:	
		case '':
			$tpl = "{$this->__CONTROLLER__}/{$this->__ACTION__}";
			break;
		case 404:
			header("Location: /40x.html");exit;
		
	}
	$path =  "app/views/{$tpl}.php";
	//var_dump($path);
	if(file_exists(ROOT_PATH . $path)){
		include ROOT_PATH . $path;exit;
	}else{
		exit($path .' ģ���ļ�������!');
	}

  }

  public function __get($name){
	return property_exists($this,$name) ?  $this->$name : null;
  }
}