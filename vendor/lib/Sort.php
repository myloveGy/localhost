<?php

namespace lib;

/**
 * Class Sort 排序处理类
 * @package lib
 */
class Sort
{
    /**
     * 冒泡排序
     * @param array $array
     * @return array
     */
    public static function bubble($array)
    {
        $length = count($array);
        if ($length > 1) {
            for ($i = 0; $i < $length; $i++) {
                for ($x = 0; $x < ($length - $i - 1); $x++) {
                    if ($array[$x] > $array[$x + 1]) {
                        $tmp = $array[$x + 1];
                        $array[$x + 1] = $array[$x];
                        $array[$x] = $tmp;
                    }
                }
            }
        }

        return $array;
    }

    /**
     * 快速排序
     * @param array $array
     * @return array
     */
    public static function quick(array $array)
    {
        $length = count($array);
        if ($length > 1) {
            $left = $right = [];
            for ($i = 1; $i < $length; $i++) {
                // 判断当前元素的大小和第一个元素比较
                if ($array[$i] < $array[0]) {
                    $left[] = $array[$i];
                } else {
                    $right[] = $array[$i];
                }
            }

            // 递归调用
            if ($left) {
                $left = self::quick($left);
            }

            if ($right) {
                $right = self::quick($right);
            }

            $array = array_merge($left, [$array[0]], $right);
        }

        return $array;
    }


    /**
     * 选择排序
     * 在要排序的一组数中，选出最小的一个数与第一个位置的数交换。然后在剩下的数当中再找最小的与第二个位置的数交换，如此循环到倒数第二个数和最后一个数比较为止。
     * @param array $array
     * @return array
     */
    public static function select(array $array)
    {
        $length = count($array);
        if ($length > 1) {

            for ($i = 0; $i < $length - 1; $i ++) {
                $index = $i;
                for ($x = $i + 1; $x < $length; $x ++) {
                    // 默认$array[$index] 为最小值
                    if ($array[$index] > $array[$x]) {
                        // 记录最小值
                        $index = $x;
                    }
                }

                // 已经确定最小值的位置，保持到$index中;如果发现最小值的位置与当前假设的位置$i不同，则位置互换即可。
                if ($index != $i) {
                    $tmp = $array[$index];
                    $array[$index] = $array[$i];
                    $array[$i] = $tmp;
                }
            }
        }

        return $array;
    }

    /**
     * 插入排序
     * 在要排序的一组数中，假设前面的数已经是排好顺序的，现在要把第n个数插到前面的有序数中，使得这n个数也是排好顺序的。如此反复循环，直到全部排好顺序。
     * @param array $array
     * @return array
     */
    public static function insert(array $array)
    {
        $length = count($array);
        if ($length > 0) {
            for ($i = 1; $i < $length; $i ++) {
                $tmp = $array[$i];

                // 内层循环，比较插入
                for ($x = $i - 1; $x >= 0; $x --) {
                    if ($tmp < $array[$x]) {
                        // 发现插入的元素要小，交换位置，将后边的元素与前面的元素互换
                        $array[$x + 1] = $array[$x];
                        $array[$x] = $tmp;
                    } else {
                        break;
                    }
                }
            }
        }

        return $array;
    }
}