<?php
/**
 *
 * Query.php
 *
 * Author: jinxing.liu
 * Create: 2019/11/25 18:23
 * Editor: created by PhpStorm
 */

namespace jinxing\framework\db;

/**
 * Class Query 查询类
 *
 * @package jinxing\framework\db
 */
class Query
{
    /**
     * @var string 执行最后的sql
     */
    private $lastSql = '';

    /**
     * @var string 查询的字段
     */
    private $select = '*';

    /**
     * @var string 查询的条件
     */
    private $where = '';

    /**
     * 查询的表
     *
     * @var string
     */
    private $table = '';

    /**
     * 查询的limit
     *
     * @var string
     */
    private $limit = '';

    /**
     * @var array 绑定的参数
     */
    private $bind = [];

    /**
     * @var array 绑定参数次数
     */
    private $bindCount = [];

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

    public function query()
    {
        $this->buildQuery();
        $this->lastSql = "SELECT {$this->select} FROM {$this->table}{$this->where}{$this->limit}";
        $smt           = self::$pdo->prepare($this->lastSql);
        $smt->execute($this->bind);
        return $this->all ? $smt->fetchAll(\PDO::FETCH_ASSOC) : $smt->fetch(\PDO::FETCH_ASSOC);
    }

    public function one()
    {
        $this->all   = false;
        $this->limit = ' LIMIT 1';
        return $this->query();
    }

    public function all()
    {
        $this->all = true;
        return $this->query();
    }

    /**
     * 查询的字段
     *
     * @param string|array $field 查找的字段
     *
     * @return $this
     */
    public function select($field = '*')
    {
        $field = is_string($field) ? explode(',', $field) : (array)$field;
        foreach ($field as &$value) {
            if ($value !== '*') {
                $value = '`' . str_replace([' as ', ' AS '], '` as `', trim($value)) . '`';
            }
        }

        unset($value);
        $this->select = implode(', ', $field);
        return $this;
    }

    /**
     * 查询表
     *
     * @param $table
     *
     * @return $this
     */
    public function from($table)
    {
        $this->table = '`' . $table . '`';
        return $this;
    }

    /**
     * 查询条件
     * where('id', '=', 1)
     * where(['id' => 2])
     * where([''])
     *
     * @param string|array $column   查询字段
     * @param mixed        $operator 查询表达式
     * @param mixed        $value    查询的值
     *
     * @return $this
     */
    public function where($column, $operator = null, $value = null)
    {
        // 没有传递查询条件
        if (empty($column)) {
            return $this;
        }

        // 传递的字符串 where('id', '=', 1)
        if (is_string($column)) {
            // operator 是字符串、那么是表达式
            if (is_string($operator)) {
                $this->condition[] = $this->buildOperateCondition($column, $operator, $value);
            } else {
                $this->condition[] = $column;
                foreach ($operator as $k => $v) {
                    $this->bind[':' . $k] = $v;
                }
            }

            return $this;
        }

        // 不是数组、直接返回
        if (!is_array($column)) {
            return $this;
        }


        // 数组处理 ['and', ['user', '=', 2], ['user', '!=', 3]]
        if (isset($column[0])) {
            $this->condition[] = $this->buildArrayCondition($column);
            return $this;
        }

        // hash format: 'column1' => 'value1', 'column2' => 'value'
        $this->condition[] = $this->buildHasCondition($column);
        return $this;
    }

    /**
     * 添加绑定
     *
     * @param string $column 绑定字段
     * @param mixed  $value
     *
     * @return string
     */
    public function bind($column, $value)
    {
        $index = ":{$column}";

        // 判断存在
        if (!isset($this->bindCount[$index])) {
            $this->bindCount[$index] = 0;
        }

        // 添加累计
        $this->bindCount[$index] += 1;

        $index              .= '_' . $this->bindCount[$index];
        $this->bind[$index] = $value;
        return $index;
    }

    /**
     * 查询数据条数
     *
     * @param int $length 查询数据条数
     * @param int $start  查询开始位置
     *
     * @return $this
     */
    public function limit($length, $start = 0)
    {
        $this->limit = ' LIMIT ' . intval($start) . ', ' . intval($length);
        return $this;
    }

    /**
     * 重置类
     *
     * @return $this
     */
    public function reset()
    {
        $this->select    = '*';
        $this->where     = [];
        $this->bind      = [];
        $this->bindCount = [];
        $this->limit     = '';
        return $this;
    }

    /**
     * 获取最后执行的SQL
     *
     * @return string
     */
    public function getLastSql()
    {
        $search = [];
        foreach ($this->bind as $column => $bind_value) {
            $search[$column] = is_numeric($bind_value) ? $bind_value : "'{$bind_value}'";
        }

        krsort($search);

        var_dump($this->bind, $search, array_keys($search), array_values($search));
        return str_replace(array_keys($search), array_values($search), $this->lastSql);
    }

    private function buildArrayCondition($columns, $and = 'AND')
    {
        $firstWhere = array_shift($columns);
        if (is_string($firstWhere)) {
            return $this->buildArrayCondition($columns, $firstWhere);
        }

        $and   = strtoupper($and);
        $where = [];
        array_unshift($columns, $firstWhere);
        foreach ($columns as $value) {

            // 关联数组处理 ['name' => 2, 'age' => 1] or ['name:like' => 'test', 'age' => 2]
            if (Helper::isAssociative($value)) {
                $where[] = $this->buildHasCondition($value, $and);
                continue;
            }

            // ['and', ['name' => 1], ['age' => 2]] or [['name' => 1], ['age' => 2]] 循环处理
            list($column) = $value;
            if (is_array($column) || (is_string($column) && in_array(strtolower($column), ['or', 'and']))) {
                $where[] = $this->buildArrayCondition($value, $and);
                continue;
            }

            // 只有 ['name', '=', 1] 才处理
            if (count($value) === 3) {
                // 处理表达式查询
                $where[] = $this->buildOperateCondition($value[0], $value[1], $value[2]);
            }
        }

        return '(' . implode(" {$and} ", $where) . ')';
    }

    /**
     * 处理数组查询
     *
     * @param string $column
     * @param array  $value
     *
     * @return string
     */
    private function buildInCondition($column, array $value)
    {
        $arrayColumn = [];
        foreach ($value as $key => $bindValue) {
            $arrayColumn[] = $this->bind($column, $bindValue);
        }

        return '`' . $column . '` IN (' . implode(', ', $arrayColumn) . ')';
    }

    /**
     * 数组查询
     *
     * ['column' => value1, 'column1' => value2]
     *
     * @param array  $condition 查询条件
     * @param string $and       连接方式
     *
     * @return mixed|string
     */
    private function buildHasCondition($condition, $and = 'AND')
    {
        $parts = [];
        foreach ($condition as $column => $value) {
            $parts[] = $this->buildOperateCondition($column, '=', $value);
        }

        return count($parts) === 1 ? $parts[0] : '(' . implode(' ' . $and . ' ', $parts) . ')';
    }

    /**
     * 处理表达式查询
     *
     * @param string $column  查询的字段信息
     * @param string $operate 查询的表达式
     * @param mixed  $value   查询的值
     *
     * @return string
     */
    private function buildOperateCondition($column, $operate, $value)
    {
        // between 查询
        if (in_array($operate, ['between', 'BETWEEN'], true) && is_array($value)) {
            $bindName1 = $this->bind($column, $value[0]);
            $bindName2 = $this->bind($column, $value[1]);
            return "`{$column}` BETWEEN {$bindName1} AND {$bindName2}";
        }

        // 通过in处理
        if (in_array($operate, ['in', 'IN'], 'true') || is_array($value)) {
            return $this->buildInCondition($column, (array)$value);
        }

        // null
        if ($value === null) {
            return "`{$column}` IS NULL";
        }

        $bindName = $this->bind($column, $value);
        return "`{$column}` {$operate} {$bindName}";
    }

    /**
     * 处理查询条件
     *
     * @param array $where
     *
     * @return void
     */
    private function buildQuery($where = [])
    {
        if ($where) {
            $this->where($where);
        }

        if (empty($this->condition)) {
            $this->where = '';
            $this->bind  = [];
        } else {
            $this->where = ' WHERE ' . implode(' AND ', $this->condition);
        }
    }
}