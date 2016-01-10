<?php require_once $this->__VAP__ . 'Weibo/__header.php'; ?>
<h2 class="username"><?= $this->proUsername ?></h2>
<a href="/Weibo/follow?userid=<?= $this->proUserid ?>&isFollowed=<?= intval($this->isFollowed) ?>" class="button">
    <?= $this->isFollowed ? '取消关注' : '关注ta' ?>
</a>
<?php foreach ($this->myPostids as $k => $postid): 
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