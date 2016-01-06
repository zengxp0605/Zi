<!DOCTYPE html>
<html>
    <head>
        <meta content="text/html; charset=UTF-8" http-equiv="content-type">
        <title>Retwis - Example Twitter clone based on the Redis Key-Value DB</title>
        <link href="<?= $this->__PUBLIC__ ?>css/weibo.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        <div id="page">
            <div id="header">
                <a href="/"><img style="border:none" src="<?= $this->__PUBLIC__ ?>images/weibo-logo.png" width="192" height="85" alt="Retwis"></a>
                <div id="navbar">
                    <a href="index.php">主页</a>
                    | <a href="timeline.php">热点</a>
                    | <a href="logout.php">退出</a>
                </div>
            </div>