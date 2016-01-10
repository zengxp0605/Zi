<?php require_once $this->__VAP__ . 'Weibo/__header.php'; ?>
<h2>热点</h2>
<i>最新注册用户(redis中的sort用法)</i><br>
<div>
    <?php
    foreach ($this->latestUserids as $k => $id):
        if (isset($this->latestUsernames[$k])):
            ?>
            <a class="username" href="profile.php?userid=<?= $id ?>"><?= $this->latestUsernames[$k] ?></a>
            <?php
        endif;
    endforeach;
    ?>
</div>

<br><i>最新的<?= count($this->latestPostids)?>条微博!</i><br>
<?php foreach ($this->latestPostids as $k => $postid): 
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