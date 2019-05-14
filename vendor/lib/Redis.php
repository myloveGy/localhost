<?php

namespace lib;


class Redis
{
    private static $redis = null;

    private static $options = [
        'host' => '127.0.0.1',
        'port' => 6379
    ];

    private function __construct()
    {

    }

    /**
     * getInstance() 获取redis信息
     * @param array $options
     * @return null|\Redis
     */
    public static function getInstance($options = [])
    {
        if (self::$redis == null) {
            // 处理配置信息
            if ($options) self::$options = array_merge(self::$options, $options);

            // 实例化对象
            self::$redis = new \Redis();
            self::$redis->connect(self::$options['host'], self::$options['port']);

            // 需要授权
            if (isset(self::$options['auth']) && ! empty(self::$options['auth'])) {
                self::$redis->auth(self::$options['auth']);
            }
        }

        return self::$redis;
    }
}