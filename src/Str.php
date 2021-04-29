<?php
/**
 * Created by PhpStorm.
 * User: zb
 * Date: 2021/4/29
 * Time: 16:45
 */

namespace baoxu1993\Tools;


class Str
{
    /**
     * 过滤标点符号
     * @param        $str
     * @param array  $characters
     * @param string $replace
     * @return mixed|string|string[]
     */
    public static function FilterSymbol($str, $characters = [], $replace = '')
    {
        $characters = $characters ? $characters : [' ', '\\', '~', '`', '!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '_', '-', '=', '+', '{', '}', '[', ']', ':', ';', '\'', '"', '<', '>', '?', ',', '.', '/', '·', '~', '！', '@', '#', '￥', '%', '…', '&', '*', '（', '）', '-', '—', '=', '+', '{', '}', '【', '】', '；', '’', '：', '“', '，', '。', '/', '《', '》', '？', '、', '|', '\r', '\n', '\r\n'];
        $replace = ($replace === '') ? '' : $replace;
        return str_replace($characters, $replace, $str);
    }
}