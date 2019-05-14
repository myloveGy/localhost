<?php
/**
 * Created by PhpStorm.
 * User: love
 * Date: 2016/11/5
 * Time: 15:16
 */
namespace session;

abstract class Main implements \SessionHandlerInterface
{

    protected $resource = null; // 资源

    // 其他配置
    protected $options = [
        'prefix' => 'session', // session前缀
        'expire' => 1440, // 保存时间
    ];

    /**
     * run() 执行 session 设置
     */
    public function run()
    {

        // 设置 session存储方式 为用户自定义
        session_module_name('user');

        //调用回调函数
        session_set_save_handler(
            array(&$this, 'open'),
            array(&$this, 'close'),
            array(&$this, 'read'),
            array(&$this, 'write'),
            array(&$this, 'destroy'),
            array(&$this, 'gc')
        );

        // 开启session
        session_start();
    }
}
