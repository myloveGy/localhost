<?php
/**
 * Created by PhpStorm.
 * User: love
 * Date: 2016/11/5
 * Time: 15:16
 */
namespace session;

class Db extends Main
{
    /**
     * Redis constructor.
     * @param array $options
     */
    public function __construct($options = [])
    {
        if ($options) $this->options = array_merge($this->options, $options);
        $this->resource = \lib\Pdo::getInstance();
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
        $objStmt = $this->resource->prepare('SELECT * FROM `'.$this->options['table'].'` WHERE `session_id` = ?');
        $objStmt->execute([$strSessionId]);
        $mixReturn = null;
        if ($objStmt->rowCount() > 0) {
            $objStmt->bindColumn('session_data', $mixReturn);
            $objStmt->fetch();
        }

        return (string)$mixReturn;
    }

    /**
     * write() 写入session
     * @param  string $strSessionId session Id
     * @param  mixed  $mixSession   session 数据
     * @return mixed
     */
    public function write($strSessionId, $mixSession)
    {
        $objStmt = $this->resource->prepare('SELECT * FROM `'.$this->options['table'].'` WHERE `session_id` = ?');
        $objStmt->execute([$strSessionId]);
        if ($objStmt->rowCount() > 0) {
            $sql = 'UPDATE `'.$this->options['table'].'` SET `session_data` = :data, `session_expire` = UNIX_TIMESTAMP() WHERE `session_id` = :id';
        } else {
            $sql = 'INSERT INTO `'.$this->options['table'].'` (`session_id`, `session_data`, `session_expire`) VALUES (:id, :data, UNIX_TIMESTAMP())';
        }

        return $this->resource->prepare($sql)->execute([
            ':id' => $strSessionId,
            ':data' => $mixSession,
        ]);

    }

    /**
     * destroy() 删除session
     * @param  string $strSessionId session id
     * @return mixed
     */
    public function destroy($strSessionId)
    {
        return $this->resource->prepare('DELETE FROM `'.$this->options['table'].'` WHERE `session_id` = ?')->execute([$strSessionId]);
    }

    /**
     * gc() 回收session
     * @param  int $intMaxLifeTime 过期时间
     * @return mixed
     */
    public function gc($intMaxLifeTime)
    {
        return $this->resource->prepare('DELETE FROM `'.$this->options['table'].'` WHERE `session_expire` < ?')->execute([$intMaxLifeTime]);
    }
}