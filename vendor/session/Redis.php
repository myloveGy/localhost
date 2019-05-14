<?php
/**
 * Created by PhpStorm.
 * User: love
 * Date: 2016/11/5
 * Time: 15:16
 */
namespace session;

class Redis extends Main
{

    /**
     * Redis constructor.
     * @param array $options
     */
    public function __construct($options = [])
    {
        if ($options) {
            $this->options = array_merge($this->options, $options);
        }

        $this->options['prefix'] .= ':';
        $this->resource = \lib\Redis::getInstance();
    }

    /**
     * open() 开启session
     * @param  string $strSessionPath session 保存路径
     * @param  string $strSessionName session 名称
     * @return mixed
     */
    public function open($strSessionPath, $strSessionName)
    {
        return true;
    }

    /**
     * close() 关闭session
     * @return mixed
     */
    public function close()
    {
        return true;
    }

    /**
     * read() 读取session
     * @param  string $strSessionId sessionId
     * @return mixed
     */
    public function read($strSessionId)
    {
        return (string) $this->resource->get($this->options['prefix'] . $strSessionId);
    }

    /**
     * write() 写入session
     * @param  string $strSessionId session Id
     * @param  mixed  $mixSession   session 数据
     * @return mixed
     */
    public function write($strSessionId, $mixSession)
    {
        return $this->resource->set($this->options['prefix'] . $strSessionId, $mixSession, $this->options['expire']);
    }

    /**
     * destroy() 删除session
     * @param  string $strSessionId session id
     * @return mixed
     */
    public function destroy($strSessionId)
    {
        return $this->resource->delete($this->options['prefix'] . $strSessionId);
    }

    /**
     * gc() 回收session
     * @param  int $intMaxLifeTime 过期时间
     * @return mixed
     */
    public function gc($intMaxLifeTime)
    {
        return true;
    }
}
