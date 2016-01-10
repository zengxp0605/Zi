<?php require_once $this->__VAP__ . 'Weibo/__header.php'; ?>
<div id="postform">
    <form method="POST" action="post.php">
        <?= $this->username ?>, 有啥感想?
        <br>
        <table>
            <tr><td><textarea cols="70" rows="3" name="content"></textarea></td></tr>
            <tr><td align="right"><input type="submit" name="doit" value="Update"></td></tr>
        </table>
    </form>
    <div id="homeinfobox">
        <a href="fansList.php?userid=<?=$this->userid?>"><?= $this->fansCount ?> 粉丝</a><br>
        <a href="followerList.php?userid=<?=$this->userid?>"><?= $this->followerCount ?> 关注</a><br>
    </div>
</div>
<?php foreach ($this->postids as $k => $postid): 
    $post = $this->redis->hGetAll("posts:{$postid}");
    ?>
    <div class="post">
        <a class="username" href="profile.php?userid=<?=$post['userid']?>"><?=  isset($post['username']) ? $post['username'] : 'not set'?></a> 
        <?=$post['content']?>
        <br>
        <i><?=$post['time']?> 分钟前 通过 web发布</i>
    </div>
<?php endforeach; ?>
<?php require_once $this->__VAP__ . 'Weibo/__footer.php'; ?>

