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
            '127.0.0.1:6371',
            '127.0.0.1:6372',
            '127.0.0.1:6373',
            '127.0.0.1:6374',
            '127.0.0.1:6375',
            '127.0.0.1:6376',
        ]);

        for ($i = 0; $i < 100000; $i++) {
            $cluster->set('key_' . $i, $i);
        }

        success([
            // 'count' => count($keys),
            'value' => $cluster->get('key_80477'),
        ]);
    }
}
