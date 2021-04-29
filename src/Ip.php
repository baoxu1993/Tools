<?php
/**
 * Created by PhpStorm.
 * User: zb
 * Date: 2021/4/29
 * Time: 16:47
 */

namespace baoxu1993\Tools;


class Ip
{
    /**
     * 获取客户端IP
     * @return string
     */
    public static function getClientIp()
    {
        $ip = '';
        $header = \Hyperf\Utils\Context::get('Header');
        foreach ($header as $k => $v) {
            if (strtolower($k) == 'x-forwarded-for') {
                $ip = isset($header[$k][0]) ? $header[$k][0] : '';
                !empty($ip) && $ip = explode(',', $ip)[0];
                break;
            }
        }

        if (empty($ip)) {
            $serverParams = \Hyperf\Utils\Context::get('ServerParams');
            $ip = isset($serverParams['remote_addr']) ? $serverParams['remote_addr'] : '';
        }
        return $ip;
    }

    /**
     * 获取IP所在的地区
     * @param string $ip
     * @return string
     * @throws \Exception
     */
    public
    static function getClientIpRegion($ip = '')
    {
        if (empty($ip)) {
            $ip = self::getClientIp();
        }

        $region = (new \Ip2Region())->btreeSearch($ip);
        return isset($region['region']) ? $region['region'] : '未知';
    }

}