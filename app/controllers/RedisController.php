<?php

namespace app\controllers;

class RedisController
{

    /**
     * @throws \RedisClusterException
     */
    public function actionIndex()
    {
        $cluster = new \RedisCluster('redis-cluster', [
            '127.0.0.1:6372',
            '127.0.0.1:6373',
            '127.0.0.1:6374',
        ], null, null, null, '6aHjkErH');

        $keys = $cluster->keys('*');
        success($keys);
    }
}