<?php

namespace jinxing\framework\session;

class Session
{
    private static $session = null;
    private static $options = [];
    
    private function __construct()
    {
        
    }

    /**
     * getInstance() 获取session 驱动对象
     * @param string $type      类型
     * @param array  $options   其他配置信息
     * @return null
     */
    public static function getInstance($type, $options = [])
    {
        if (self::$session == null) {
            // 处理配置信息
            if ($options) self::$options = array_merge(self::$options, $options);
            $className = '\\session\\'.ucfirst(trim($type));
            if (class_exists($className)) {
                self::$session = new $className($options);
                self::$session->run();
            }
        }

        return self::$session;
    }
}