<?php
/**
 * Created by PhpStorm.
 * User: zb
 * Date: 2021/4/29
 * Time: 16:53
 */

namespace baoxu1993\Tools;


class Url
{
    /**
     * @param $url
     * @param $params
     * @return string
     */
    public static function joinUrl($url, $params)
    {
        return $url . (strpos('?', $url) === false ? '?' : '&') . (is_array($params) ? http_build_query($params) : (string)$params);
    }
}