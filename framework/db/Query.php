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

use jinxing\framework\lib\Helper;

/**
 * Class Query 查询类
 *
 * @package jinxing\framework\db
 */
class Query
{
    /**
     * @var null|DB 查询的db
     */
    private $db = null;

    /**
     * @var string 执行最后的sql
     */
    private $sql = '';

    /**
     * @var string 查询的字段
     */
    private $select = '*';

    /**
     * @var string 查询的条件
     */
    private $where = [];

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
     * Query constructor.
     *
     * @param DB $db
     */
    public function __construct(DB $db)
    {
        return $this->db = $db;
    }

    /**
     * 查询单条数据
     *
     * @return mixed
     * @throws \Exception
     */
    public function one()
    {
        $this->limit = ' LIMIT 1';
        return $this->buildQuery()->db->queryRow($this->sql, $this->bind);
    }

    /**
     * 查询多条数据
     *
     * @return mixed
     * @throws \Exception
     */
    public function all()
    {
        return $this->buildQuery()->db->query($this->sql, $this->bind);
    }

    /**
     * 修改数据
     *
     * @param array $update
     *
     * @return int
     * @throws \Exception
     */
    public function update(array $update)
    {
        $set = [];
        foreach ($update as $column => $value) {
            $set[] = '`' . $column . '` = ' . $this->bind($column, $value);
        }

        $this->buildWhere();
        $set       = implode(', ', $set);
        $this->sql = "UPDATE {$this->table} SET {$set}{$this->where}{$this->limit}";
        return $this->db->execute($this->sql, $this->bind)->rowCount();
    }

    /**
     * 删除数据
     *
     * @return int
     * @throws \Exception
     */
    public function delete()
    {
        $this->buildWhere();
        $this->sql = "DELETE FROM {$this->table}{$this->where}{$this->limit}";
        return $this->db->execute($this->sql, $this->bind)->rowCount();
    }

    /**
     * 查询的字段
     *
     * @param string|array $columns 查找的字段
     *
     * @return $this
     */
    public function select($columns = '*')
    {
        if ($columns === '*') {
            $this->select = '*';
            return $this;
        }

        $columns = is_string($columns) ? explode(',', $columns) : (array)$columns;
        foreach ($columns as &$value) {
            $value = '`' . str_replace([' as ', ' AS '], '` as `', trim($value)) . '`';
        }

        unset($value);
        $this->select = implode(', ', $columns);
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
                $this->where[] = $this->buildOperateCondition($column, $operator, $value);
            } else {
                $this->where[] = $column;
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
            $this->where[] = $this->buildArrayCondition($column);
            return $this;
        }

        // hash format: 'column1' => 'value1', 'column2' => 'value'
        $this->where[] = $this->buildHasCondition($column);
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
        $this->limit = $start == 0 ? ' LIMIT ' . intval($length) : ' LIMIT ' . intval($start) . ', ' . intval($length);
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
        $this->limit     = '';
        $this->where     = [];
        $this->bind      = [];
        $this->bindCount = [];
        return $this;
    }

    /**
     * 获取最后执行的SQL
     *
     * @return string
     */
    public function getSql()
    {
        $search = [];
        foreach ($this->bind as $column => $bind_value) {
            $search[$column] = is_numeric($bind_value) ? $bind_value : "'{$bind_value}'";
        }

        krsort($search);
        return str_replace(array_keys($search), array_values($search), $this->sql);
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
     * @return $this
     */
    private function buildQuery()
    {
        $this->buildWhere();
        $this->sql = "SELECT {$this->select} FROM {$this->table}{$this->where}{$this->limit}";
        return $this;
    }

    /**
     * @return $this
     */
    private function buildWhere()
    {
        $this->where = $this->where ? ' WHERE ' . implode(' AND ', $this->where) : '';
        return $this;
    }
}