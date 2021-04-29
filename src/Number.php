<?php
/**
 * Created by PhpStorm.
 * User: zb
 * Date: 2021/4/29
 * Time: 16:31
 */

namespace baoxu1993\Tools;


class Number
{
    /**
     * 数字小数点格式化
     * @param $value
     * @param $decimals
     * @return string
     */
    public static function asDecimal($value, $decimals)
    {
        $value = floatval($value);
        $pow = pow(10, $decimals);
        return intval($value * $pow) / $pow;
    }

    /**
     * 高精度加减乘除
     * @param string $a
     * @param string $op
     * @param string $b
     * @param int    $scale
     * @return
     */
    public static function bc($a, $op, $b, $scale = 2)
    {
        $a = (string)$a;
        $b = (string)$b;
        switch ($op) {
            case '+':
                return bcadd($a, $b, $scale);
                break;
            case '-':
                return bcsub($a, $b, $scale);
                break;
            case '*':
                return bcmul($a, $b, $scale);
                break;
            case '/':
                return bcdiv($a, $b, $scale);
                break;
        }
    }

    /**
     * 比较数字范围
     * @param        $value
     * @param        $min
     * @param        $max
     * @param string $op
     * @return bool
     */
    public static function between($value, $min, $max, $op = '()')
    {
        $flag = false;
        switch ($op) {
            case '()':
                if ($value > $min && $value < $max) {
                    $flag = true;
                }
                break;
            case '[)':
                if ($value >= $min && $value < $max) {
                    $flag = true;
                }
                break;
            case '[]':
                if ($value >= $min && $value <= $max) {
                    $flag = true;
                }
                break;
            case '(]':
                if ($value > $min && $value <= $max) {
                    $flag = true;
                }
                break;
        }

        return $flag;
    }
}