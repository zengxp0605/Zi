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
        $this->fansCount = $this->redis->sCard("following:userid:{$this->userid}");
        $this->followerCount = $this->redis->sCard("follower:userid:{$this->userid}");
        $this->postids = array();
        /**
         *  拉模型获取自己的动态, 遍历自己的关注,获取上次拉取的最大的postid,从关注用户的微博中
         * 拿到比这个postid大的id
         */
        $this->_pullMyPostids();
        /**
         * ---------------------------------------------------------------------
         *    // 推模型时直接获取自己的信息
         *      // 获取自己的动态,包括自己的和自己关注的用户的,按照时间顺序排序,或者说按照postid大小排序  
         * ---------------------------------------------------------------------
         */
        $this->postids = $this->redis->sort("latest_post:userid:{$this->userid}", ['sort' => 'desc']);

        //var_dump($this->postids);
    }

    /**
     * 拉取自己的动态
     */
    private function _pullMyPostids() {
        $lastPullid = $this->redis->get("user:userid:{$this->userid}:last_pull_postid");
        $lastPullTime = $this->redis->get("user:userid:{$this->userid}:last_pull_time");
        if ($lastPullTime && $lastPullTime > (time() - 60)) {
            echo '<br/>---------1分钟内不重复刷新----------<hr/>';
            return;
        }
        if (!$lastPullid)
            $lastPullid = 0;
        $largestPostid = $this->redis->get("global:postid");
        $postids = array();
        $followerIds = $this->redis->sMembers("follower:userid:{$this->userid}");
        $followerIds[] = $this->userid; // 同时获取自己的
        foreach ($followerIds as $k => $uid) {
            // 获取该用户的微博,postid 需要大于 $lastpullid , 小于 global:postid
            $postids = array_merge($postids, $this->redis->zRangeByScore("user_post:{$uid}", $lastPullid, $largestPostid, ['limit' => [0, 19]]));
        }

//        $test = array_combine($postids, $postids);
//         $this->redis->zAdd("test3", $test);
//        var_dump($test);
//         echo '<br />-----------------<hr />';
        if (!empty($postids)) {
            //保存微博id到自己的微博动态 有序集合,并更新当前用户 last_pull_postid
            $tmp = array_combine($postids, $postids); // 将数组键值置为postid
            $this->redis->zAdd("latest_post:userid:{$this->userid}", $tmp);
            // 保证长度最长不超过1000条,这里模拟使用10条
            if (($count = ($this->redis->zCard("latest_post:userid:{$this->userid}") - 10)) > 0) {
                $this->redis->zRemRangeByRank("latest_post:userid:{$this->userid}", 0, $count - 1);
            }
            $this->redis->set("user:userid:{$this->userid}:last_pull_postid", max($postids) + 1);
            var_dump($postids);
            echo '<br />---------以上是新拉取的postid-------<hr />';
        } else {
            echo '<br />---------没有最新的动态-------<hr />';
        }
        //更新拉取的最新时间
        $this->redis->set("user:userid:{$this->userid}:last_pull_time", time());
    }

    public function test() {
//        $redis = new \Redis();
//        $redis->connect('127.0.0.1', 6379);
//        $redis->zadd('myset',11,'one');
//                $redis->zadd('myset',22,'two');
//        $redis->zadd('myset',33,'three');
//         var_dump($redis->zrange('myset2',0,-1));
        for ($i = 10; $i <= 15; $i++) {
            $test[$i] = $i;
        }

        $this->redis->zadd('myset2', $test);

        var_dump($this->redis->zrange('myset2', 0, -1));
        return false;
    }

    public function index() {
        if ($this->_chkLogin())
            $this->redirect('Weibo/home');
    }

    public function profile($userid, $page = 1, $pageSize = 10) {
        $this->proUsername = $this->redis->get("user:userid:{$userid}:username");
        $this->proUserid = $userid;
        //判断是否关注过
        $this->isFollowed = false;
        if ($this->_chkLogin()) {
            $this->isFollowed = $this->redis->sIsMember("follower:userid:{$this->userid}", $this->proUserid);
        }

        $start = ($page - 1) * $pageSize;
        // 获取当前用户自己发布的微博的 id
        $this->myPostids = $this->redis->sort("user_post:{$this->proUserid}", array('sort' => 'desc', 'limit' => [$start, $pageSize]));

        var_dump($this->myPostids);
    }

    public function fansList($userid) {
        $this->tips = '粉丝列表';
        $this->ids = $this->redis->sMembers("following:userid:{$userid}");

        //var_dump($this->ids);
        $this->display('Weibo/memberList');
    }

    public function followerList($userid) {
        $this->tips = '关注列表';
        $this->ids = $this->redis->sMembers("follower:userid:{$userid}");

        var_dump($this->ids);
        $this->display('Weibo/memberList');
    }

    /**
     * 发布微博,微博存放于Hash posts表中
     */
    public function post($content = '') {
        if (!$this->_chkLogin())
            $this->redirect('Weibo/index');
        $postid = $this->redis->incr('global:postid');

        $this->redis->hMset("posts:{$postid}", array('time' => time(), 'userid' => $this->userid, 'username' => $this->username, 'content' => $content));
        //维护用户自己的微博  有序集合,仅保存最新的20条,需要获取更多时读取mysql数据库
        $this->redis->zAdd("user_post:{$this->userid}", $postid, $postid);
        if ($this->redis->zCard("user_post:{$this->userid}") > 19) {
            // 删除id最小的那条微博
            $this->redis->zRemRangeByRank("user_post:{$this->userid}", 0, 0);
        }
        //刷新最新拉取的时间,否则无法实时看到自己最新发布的微博
        $this->redis->del("user:userid:{$this->userid}:last_pull_time");

        // 向自己的粉丝们推送这条微博
        //$this->_pushPost($postid);
        // 维护最新的微博,全员可见,热点
        $this->redis->lPush("latest_post", $postid);
        if ($this->redis->lLen("latest_post") > 50)
            $this->redis->rPop("latest_post");

//        var_dump($r, $this->redis->hGetAll("posts:{$postid}"));
        //$this->success('微博发布成功', '/Weibo/home');
        $this->redirect('Weibo/home');
    }

    /**
     *  使用推模型时,向所有自己的粉丝推送最新发送的那条微博
     * @param type $postid
     */
    private function _pushPost($postid) {
        $fans = $this->redis->sMembers("following:userid:{$this->userid}");
        $fans[] = $this->userid; // 同时向自己推送一条
        foreach ($fans as $userid) {
            // 维护粉丝们看到的最新微博链表,保证长度最长为1000 List latest_post:userid
            $this->redis->lPush("latest_post:userid:{$userid}", $postid);
            if ($this->redis->lLen("latest_post:userid:{$userid}") > 1000)
                $this->redis->rPop("latest_post:userid:{$userid}");
        }
    }

    /**
     * 关注用户 使用集合 Redis Set
     * following:我的粉丝列表
     * follower:我关注的用户列表
     */
    public function follow($userid, $isFollowed) {
        if ($this->_chkLogin()) {
            $proUserid = $userid;
            if ($this->userid == $proUserid)
                $this->error('不能关注自己');

            $refUrl = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
            if (!$isFollowed) {
                //我关注他,在我的关注里加入一人,在他的粉丝里加入一人
                //我最多关注1000人
                if ($this->redis->sCard("follower:userid:{$this->userid}") > 1000) {
                    $this->error('最多关注1000人');
                }
                $this->redis->sAdd("follower:userid:{$this->userid}", $proUserid);
                $this->redis->sAdd("following:userid:{$proUserid}", $this->userid);
                $this->success('关注成功', $refUrl);
            } else { //取消关注
                $this->redis->sRem("follower:userid:{$this->userid}", $proUserid);
                $this->redis->sRem("following:userid:{$proUserid}", $this->userid);
                $this->success('取消关注成功', $refUrl);
            }
        }
        $this->error('请先登陆', '/Weibo/index');
    }

    public function timeline() {
        //获取最新注册用户
        $this->latestUserids = $this->redis->lRange('latest_user', 0, 9);
        $this->latestUsernames = $this->redis->sort('latest_user', array('get' => 'user:userid:*:username', 'sort' => 'desc', 'limit' => array(0, 10)));

        // 最新的50条微博
        $this->latestPostids = $this->redis->sort('latest_post', array('sort' => 'desc'));


        var_dump($this->latestUserids, $this->latestPostids);
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

        // 维护最新用户链表,保证长度为10人 List latest_user
        $this->redis->lPush("latest_user", $userid);
        if ($this->redis->lLen("latest_user") > 10)
            $this->redis->rPop("latest_user");

        if ($this->_login($userid, $username)) {
            $this->redirect('Weibo/home');
        }

        var_dump($username, $password, $password2);
        exit;
    }

    public function logout() {
        $userid = isset($_COOKIE['userid']) ? $_COOKIE['userid'] : null;
        $r = $this->redis->del("user_token:{$userid}");
        setcookie('token', '', time() - 100, '/');
        setcookie('userid', '', time() - 100, '/');
        setcookie('username', '', time() - 100, '/');
        $this->redirect('Weibo/index');
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
//        if ($this->_chkLogin())
//            return true;
        $_token = uniqid();
        $this->redis->set("user_token:{$userid}", $_token);
        setcookie('token', $_token, null, '/');
        setcookie('userid', $userid, null, '/');
        setcookie('username', $username, null, '/');
        return true;
    }

    private function _chkLogin() {
        $userid = isset($_COOKIE['userid']) ? $_COOKIE['userid'] : null;
        if (!$userid)
            return false;
        $_token = isset($_COOKIE['token']) ? $_COOKIE['token'] : null;
        $_dbToken = $this->redis->get("user_token:{$userid}");
        // die(var_dump($_token, $_dbToken));
        if ($_token != $_dbToken) { // 其他地方登陆
            return false;
        }
        $this->username = $_COOKIE['username'];
        $this->userid = $_COOKIE['userid'];
        return true;
    }

}
