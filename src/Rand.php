<?php
/**
 * Created by PhpStorm.
 * User: zb
 * Date: 2021/4/29
 * Time: 16:57
 */

namespace baoxu1993\Tools;


class Rand
{
    /**
     * 生成随机数方法
     * @param int    $length
     * @param string $chars
     * @return string
     */
    public static function getRand($length = 4, $chars = NULL)
    {
        if ($length < 1) {
            return '';
        }

        if ($chars === NULL) {
            $chars = 'ahjE2F7GfIJqLMiKopDrPgVstTw8xAy9zB3G6OI4l5JCbHdNcQvRuUkmHnWeX0YSZ1';
        }

        $code = '';
        for ($i = 1; $i <= $length; $i++) {
            $code .= substr($chars, rand(0, strlen($chars) - 1), 1);
        }

        return $code;
    }

    /**
     * 获取uniqid
     * @param string $prefix
     * @param bool   $entropy
     * @return string
     */
    public static function getUniqid($prefix = "", $entropy = true)
    {
        $seed = strtoupper(uniqid('', $entropy));
        if ($entropy == true) {
            $seed = str_replace('.', '', $seed);
        }
        return $prefix . $seed;
    }
}