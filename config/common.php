<?php

return array(
    'sessionType' => 'database', //database or memcache
    'cacheType' => 'redis', //file or memcache or redis
    'memSalt' => 'test1', // 避免在memcache中不同项目key一致
    'memServers' => array(
//            'aliocs' => array('***.m.cnhzaliqshpub001.ocs.aliyuncs.com', 11211, '***', '***')
        'memcache' => array('127.0.0.1', 11211)
//            'memcached' => array(
//                array('127.0.0.1', 11211, 1)
//            )
    )
);
