<?php

namespace jinxing\framework\db;

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
     */
    public function insert($table, array $insert)
    {
        $keys       = array_keys($insert);
        $bindParams = array_pad([], count($keys), '?');
        // 执行的SQL
        $this->lastSql = 'INSERT INTO `' . $table . '` (' . implode(', ', $keys) . ') VALUES (' . implode(', ', $bindParams) . ')';
        $smt           = self::$pdo->prepare($this->lastSql);
        if ($mixReturn = $smt->execute(array_values($insert))) {
            $mixReturn = self::$pdo->lastInsertId();
        }

        return $mixReturn;
    }

    /**
     * 修改数据
     *
     * @param string      $table  修改的表
     * @param array|mixed $where  修改的添加
     * @param array       $update 修改的数据
     *
     * @return bool|int
     */
    public function update($table, $where, array $update)
    {
        $this->reset()->buildQuery($where);
        $update_bind = [];
        foreach ($update as $key => $value) {
            $bindName      = $this->bind($key, $value);
            $update_bind[] = "`{$key}` = {$bindName}";
        }

        $this->lastSql = 'UPDATE `' . $table . '` SET ' . implode($update_bind, ', ') . $this->where;
        $smt           = self::$pdo->prepare($this->lastSql);
        if ($mixed = $smt->execute($this->bind)) {
            $mixed = $smt->rowCount();
        }

        return $mixed;
    }

    /**
     * 删除数据
     *
     * @param string      $table 删除的表
     * @param array|mixed $where 删除的条件
     *
     * @return boolean|int
     */
    public function delete($table, $where)
    {
        $this->reset()->buildQuery($where);
        $this->lastSql = 'DELETE FROM `' . $table . '`' . $this->where;
        $smt           = self::$pdo->prepare($this->lastSql);
        if ($mixed = $smt->execute($this->bind)) {
            $mixed = $smt->rowCount();
        }

        return $mixed;
    }

    /**
     * 查询数据全部数据
     *
     * @param string $table  查询的表格
     * @param array  $where  查询条件
     * @param string $fields 查询的字段
     *
     * @return array
     */
    public function findAll($table, $where = [], $fields = '*')
    {
        $this->bind = $this->condition = [];
        $this->select($fields);
        $this->buildQuery($where);
        $this->lastSql = 'SELECT ' . $this->select . ' FROM `' . $table . '` ' . $this->where;
        $smt           = self::$pdo->prepare($this->lastSql);
        $smt->execute($this->bind);
        return $smt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * 查询多条的单个字段数组
     *
     * @param string $table  查询的表
     * @param array  $where  查询的条件
     * @param string $column 查询的字段
     *
     * @return void
     */
    public function findAllBy($table, $where = [], $column)
    {
        if ($all = $this->findAll($table, $where, $column)) {
            return array_column($all, $column);
        }

        return [];
    }

    /**
     * 查询数据一条数据
     *
     * @param string $table  查询的表格
     * @param array  $where  查询条件
     * @param string $fields 查询的字段
     *
     * @return array
     */
    public function findOne($table, $where = [], $fields = '*')
    {
        $this->bind = $this->condition = [];
        $this->select($fields);
        $this->buildQuery($where);
        $this->lastSql = 'SELECT ' . $this->select . ' FROM `' . $table . '` ' . $this->where . ' LIMIT 1';
        $smt           = self::$pdo->prepare($this->lastSql);
        $smt->execute($this->bind);
        return $smt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     *
     * 查询单个字段信息
     *
     * @param string $table  查询的表
     * @param array  $where  查询的条件
     * @param string $column 查询的字段
     *
     * @return bool|mixed
     */
    public function findBy($table, $where, $column)
    {
        if ($one = $this->findOne($table, $where, [$column])) {
            return isset($one[$column]) ? $one[$column] : false;
        }

        return false;
    }

    public function query($sql, $bind)
    {
        $this->sql = $sql;
        $smt       = self::$pdo->prepare($this->sql);
        $smt->execute($bind);
        return $this->all ? $smt->fetchAll(\PDO::FETCH_ASSOC) : $smt->fetch(\PDO::FETCH_ASSOC);
    }
}