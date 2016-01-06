<?php

namespace App\Controllers;

use Zi\Config;
use Zi\Log;
use Zi\View;
use Predis\Client;

/**
 * WeiboController
 * redis 完成的仿微博项目
 */
class WeiboController extends BaseController {

    public function __construct() {

        $this->redis = new Client(Config::get('redis'));
        //die(var_dump($this->redis));
    }

    public function home() {
        if (!$this->_chkLogin())
            $this->redirect('Weibo/index');
    }

    public function index() {
        if ($this->_chkLogin())
            $this->redirect('Weibo/home');
    }

    public function profile() {
        
    }

    public function timeline() {
        
    }

    /**
     *  注册新用户：
     * 1.验证信息完整
     * 2.验证用户名是否已被注册
     * 3.注册并登陆
     * redis 用户表
     * user:username:admin:userid:  1
     * 
     * user:userid:1:username:  admin
     * user:userid:1:password:  pwd
     */
    public function register($username, $password, $password2) {
        if (!$username || !$password || !$password2) {
            $this->error('注册信息请填写完整！');
        }
        if ($password != $password2) {
            $this->error('两次密码不一致！');
        }
        if ($this->redis->get("user:username:{$username}:userid")) {
            $this->error('该用户名已被注册！');
        }
        //获取全局userid
        $userid = $this->redis->incr('global:userid');
        $this->redis->set("user:userid:{$userid}:username", $username);
        $this->redis->set("user:userid:{$userid}:password", md5($password));
        $this->redis->set("user:username:{$username}:userid", $userid);

        if ($this->_login($userid, $username)) {
            $this->redirect('Weibo/home');
        }

        var_dump($username, $password, $password2);
        exit;
    }

    public function login($username, $password) {
        if (!$username || !$password) {
            $this->error('登陆信息请填写完整！');
        }
        $userid = $this->redis->get("user:username:{$username}:userid");
        if (!$userid)
            $this->error('该用户不存在！');
        $pwd = $this->redis->get("user:userid:{$userid}:password");
        //die(var_dump($pwd,$password));
        if ($pwd != md5($password))
            $this->error('密码不正确！');
        $username = $this->redis->get("user:userid:{$userid}:username");
        if ($this->_login($userid, $username)) {
            $this->redirect('Weibo/home');
        }
        $this->error('登陆出错！');
    }

    private function _login($userid, $username) {
        if ($this->_chkLogin())
            return true;
        $_token = uniqid();
        $this->redis->set("userToken:{$userid}", $_token);
        setcookie('token', $_token, null, '/');
        setcookie('userid', $userid, null, '/');
        setcookie('username', $username, null, '/');
        return true;
    }

    private function _chkLogin() {
        $_userid = isset($_COOKIE['userid']) ? $_COOKIE['userid'] : null;
        if (!$_userid)
            return false;
        $_token = isset($_COOKIE['token']) ? $_COOKIE['token'] : null;
        $_dbToken = $this->redis->get("userToken:{$_userid}");
        // die(var_dump($_token, $_dbToken));
        if ($_token != $_dbToken)
            return false;
        $this->username = $_COOKIE['username'];
        $this->userid = $_COOKIE['userid'];
        return true;
    }

}
