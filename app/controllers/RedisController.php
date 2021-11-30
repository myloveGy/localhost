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
            '127.0.0.1:6375',
            '127.0.0.1:6376',
            '127.0.0.1:6377',
        ], null, null, null, '6aHjkErH');

        $keys = $cluster->keys('*');
        success([
            'count' => count($keys),
            'value' => $cluster->get('key_80477'),
        ]);
    }
}