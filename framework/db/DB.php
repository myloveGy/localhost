<?php

namespace jinxing\framework\db;

/**
 * Class DB
 *
 * @package jinxing\framework\db
 */
class DB
{
    /**
     * @var null|\PDO
     */
    private static $pdo = null;

    /**
     * @var string 执行的SQL
     */
    private $sql = '';

    /**
     * @var array 默认配置信息
     */
    private static $options = [
        'dns'      => 'mysql:host=127.0.0.1;port=3306;charset=utf8',
        'username' => 'root',
        'password' => '',
    ];

    /**
     * Pdo constructor. 私有构造方法
     */
    private function __construct()
    {
    }

    /**
     *
     */
    private function __clone()
    {

    }

    /**
     * getInstance() 获取 db 信息
     *
     * @param array $options
     *
     * @return DB
     */
    public static function getInstance($options = [])
    {
        if (self::$pdo == null) {
            // 处理配置信息
            if ($options) {
                self::$options = array_merge(self::$options, $options);
            }

            // 实例化对象
            self::$pdo = new \PDO(self::$options['dns'], self::$options['username'], self::$options['password']);
        }

        return new self;
    }

    /**
     * 执行新增数据
     *
     * @param string $table  新增数据的表
     * @param array  $insert 新增的数组[字段 => 值]
     *
     * @return bool|string
     * @throws \Exception
     */
    public function insert($table, array $insert)
    {
        $keys = $bindKeys = $bind = [];
        foreach ($insert as $key => $value) {
            $keys[]          = "`{$key}`";
            $bindKeys[]      = ":{$key}";
            $bind[":{$key}"] = $value;
        }

        $keys = implode(', ', $keys);
        // 执行的SQL
        $sql = 'INSERT INTO `' . $table . '` (' . $keys . ') VALUES (' . implode(', ', $bindKeys) . ')';
        $this->execute($sql, $bind);
        return self::$pdo->lastInsertId();
    }

    /**
     * 修改数据
     *
     * @param string      $table  修改的表
     * @param array|mixed $where  修改的添加
     * @param array       $update 修改的数据
     *
     * @param array       $bind
     *
     * @return bool|int
     * @throws \Exception
     */
    public function update($table, $where, array $update, $bind = [])
    {
        $attributes = [];
        foreach ($update as $key => $value) {
            $bind[":{$key}"] = $value;
            $attributes[]    = "`{$key}` = :{$key}";
        }

        $where = $where ? ' WHERE ' . $where : '';
        $sql   = 'UPDATE `' . $table . '` SET ' . implode($attributes, ', ') . $where;
        return $this->execute($sql, $bind)->rowCount();
    }

    /**
     * 删除数据
     *
     * @param string $table 删除的表
     * @param string $where 删除的条件
     * @param array  $bind
     *
     * @return boolean|int
     * @throws \Exception
     */
    public function delete($table, $where, $bind = [])
    {
        $where = $where ? ' WHERE ' . $where : '';
        return $this->execute('DELETE FROM `' . $table . '`' . $where, $bind)->rowCount();
    }

    /**
     * 查询多条数据
     *
     * @param string $sql  查询SQL
     * @param array  $bind 绑定的参数
     *
     * @return array
     * @throws \Exception
     */
    public function query($sql, $bind = [])
    {
        return $this->execute($sql, $bind)->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * 查询一条数据
     *
     * @param string $sql  执行的SQL
     * @param array  $bind 绑定的参数
     *
     * @return mixed
     * @throws \Exception
     */
    public function queryRow($sql, $bind = [])
    {
        return $this->execute($sql, $bind)->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * 执行SQL
     *
     * @param string $sql  执行的SQL
     * @param array  $bind 绑定的参数
     *
     * @return bool|\PDOStatement
     * @throws \Exception
     */
    public function execute($sql, $bind = [])
    {
        $this->sql = $sql;
        $smt       = self::$pdo->prepare($this->sql);
        if (!$smt->execute($bind)) {
            $error = $smt->errorInfo();
            throw new \Exception($error[2]);
        }

        return $smt;
    }

    /**
     * @return string 获取执行的SQL
     */
    public function getSql()
    {
        return $this->sql;
    }
}