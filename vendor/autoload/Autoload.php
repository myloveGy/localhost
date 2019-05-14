<?php

define('DIR_PATH', __DIR__ . '/../');

class Autoload
{
    /**
     * @param $className
     */
    public static function loadClass($className)
    {
        $className = str_replace('\\', DIRECTORY_SEPARATOR, $className) . '.php';
        if (file_exists(DIR_PATH . $className)) {
            $className = DIR_PATH . $className;
        } else if (file_exists(DIR_PATH . '../' . $className)) {
            $className = DIR_PATH . '../' . $className;
        } else {
            return;
        }

        include_once($className);
    }
}

spl_autoload_register(['Autoload', 'loadClass']);

// 引入两个公共文件
include DIR_PATH . 'common/functions.php';
include DIR_PATH . 'common/validate.php';
