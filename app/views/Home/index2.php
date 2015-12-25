<?php require($this->__VAP__ . '_header.php'); ?>

<link rel="stylesheet" href="<?= $this->__PUBLIC__ ?>css/zi.css" />
<script type="text/javascript">
    (function () {
		console.log('id=<?= $this->id ?>');
    })();
</script>

    <div class="main_bg">
        <div class="main">
			<h1><?php echo $article->title?></h1>
            <div class="content"><?php echo $article->content?></div>
        </div>
    </div>
    <?php require($this->__VAP__ . '_footer.php'); ?>
