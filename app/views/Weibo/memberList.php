<?php require_once $this->__VAP__ . 'Weibo/__header.php'; ?>
<div>
    <h2><?= $this->tips ?></h2>
    <?php foreach ($this->ids as $k => $userid) : ?>
        <p><a href="profile.php?userid=<?= $userid ?>"><?= $this->redis->get("user:userid:{$userid}:username") ?></a></p>
    <?php endforeach; ?>


</div>

<?php require_once $this->__VAP__ . 'Weibo/__footer.php'; ?>
