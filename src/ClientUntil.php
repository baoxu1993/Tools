<?php
/**
 * Created by PhpStorm.
 * User: zb
 * Date: 2020/11/3
 * Time: 11:20
 */

namespace baoxu1993\Tools;


class ClientUntil
{
    public static function getClientType($agent)
    {
        if (strpos($agent, 'MicroMessenger') !== false) {
            return 'WeChat';
        }
        $agent = strtolower($agent);
        if (strpos($agent, 'windows nt') || strpos($agent, 'mac os')) {
            return 'PC';
        }

        if (strpos($agent, 'iphone') || strpos($agent, 'android') || strpos($agent, 'ipad')) {
            return 'WAP';
        }

        return "----";
    }

    /**
     * 浏览器正则
     * @var array
     */
    protected static $browsers = array(
        'Edge' => 'Edge',
        'IE' => 'MSIE|IEMobile|MSIEMobile|Trident/[.0-9]+',
        'Chrome' => '(?:\bCrMo\b|CriOS|Android)?.*Chrome/[.0-9]*(Mobile)?',
        'Opera' => 'Opera.*Mini|Opera.*Mobi|Android.*Opera|Mobile.*OPR/[0-9.]+|Coast/[0-9.]+',
        'Firefox' => 'fennec|firefox.*maemo|(Mobile|Tablet).*Firefox|Firefox.*Mobile',
        'Safari' => 'Version.*Mobile.*Safari|Safari.*Mobile|MobileSafari',
        'UCBrowser' => 'UC.*Browser|UCWEB',//UC游览器
        'QQBrowser' => 'MQQBrowser|TencentTraveler',//QQ游览器
        'Theworld' => 'Theworld',//世界之窗游览器
        'Maxthon' => 'Maxthon',//遨游游览器
        'baiduboxapp' => 'baiduboxapp',
        'baidubrowser' => 'baidubrowser',
        'NokiaBrowser' => 'Nokia',
    );

    /**
     * 操作系统正则
     *  note:移动设备的系统需优先匹配
     *  故正则需要放在电脑系统前面
     * @var array
     */
    protected static $platforms = array(
        'iOS' => '\biPhone.*Mobile|\biPod|\biPad',
        'WindowsMobileOS' => 'WindowsCE.*(PPC|Smartphone|Mobile|[0-9]{3}x[0-9]{3})|WindowMobile|WindowsPhone[0-9.]+|WCE;',
        'WindowsPhoneOS' => 'WindowsPhone10.0|WindowsPhone8.1|WindowsPhone8.0|WindowsPhoneOS|XBLWP7|ZuneWP7|WindowsNT6.[23];ARM;',
        'Android' => 'Android',
        'BlackBerryOS' => 'blackberry|\bBB10\b|rimtabletos',//黑莓
        'SymbianOS' => 'Symbian|SymbOS|Series60|Series40|SYB-[0-9]+|\bS60\b',//塞班
        'webOS' => 'webOS|hpwOS',
        'MicroMessenger' => 'MicroMessenger',//微信
        'Windows' => 'Windows',
        'WindowsNT' => 'Windows NT',
        'MacOSX' => 'MacOSX',
        'Ubuntu' => 'Ubuntu',
        'Linux' => 'Linux',
        'ChromeOS' => 'CrOS',
    );

    /**
     * 版本号匹配正则（浏览器+操作系统）
     * @var array
     */
    protected static $versionRegexs = array(

        //Browser
        'Maxthon' => 'Maxthon[VER]',//遨游
        'Chrome' => array('Chrome/[VER]', 'CriOS/[VER]', 'CrMo/[VER]'),//谷歌
        'Firefox' => 'Firefox/[VER]',//火狐
        'Fennec' => 'Fennec/[VER]',//火狐
        'IE' => array('IEMobile/[VER];', 'IEMobile[VER]', 'MSIE[VER];', 'rv:[VER]'),
        'Opera' => array('OPR/[VER]', 'OperaMini/[VER]', 'Version/[VER]', 'Opera[VER]'),
        'UCBrowser' => 'UCBrowser[VER]',//UC
        'QQBrowser' => array('MQQBrowser/[VER]', 'TencentTraveler/[VER]'),//QQ
        'MicroMessenger' => 'MicroMessenger/[VER]',//微信
        'baiduboxapp' => 'baiduboxapp/[VER]',//百度盒子
        'baidubrowser' => 'baidubrowser/[VER]',//百度
        'Safari' => array('Version/[VER]', 'Safari/[VER]'),//MacOSX中的浏览器
        'NokiaBrowser' => 'NokiaBrowser/[VER]',//诺基亚

        //OS
        'iOS' => '\bi?OS\b[VER][;]{1}',
        'BlackBerry' => array('BlackBerry[\w]+/[VER]', 'BlackBerry.*Version/[VER]', 'Version/[VER]'),//黑莓手机
        'WindowsPhoneOS' => array('WindowsPhoneOS[VER]', 'WindowsPhone[VER]'),
        'WindowsPhone' => 'WindowsPhone[VER]',
        'WindowsNT' => 'Windows NT [VER]',
        'Windows' => 'Windows NT [VER]',
        'SymbianOS' => array('SymbianOS/[VER]', 'Symbian/[VER]'),//塞班系统
        'webOS' => array('webOS/[VER]', 'hpwOS/[VER];'),//LG
        'MacOSX' => 'MACOSX[VER]',//苹果系统
        'BlackBerryOS' => array('BlackBerry[\w]+/[VER]', 'BlackBerry.*Version/[VER]', 'Version/[VER]'),
        'Android' => 'Android[VER]',
        'ChromeOS' => 'CrOSx86_64[VER]',
    );


    /**
     * 获取客户端浏览器
     * @param      $userAgent
     * @param bool $isReTurnVersion
     * @return int|string
     */
    public static function getBrowser($userAgent, $isReTurnVersion = false)
    {
        if (empty($userAgent)) {
            return '';
        }
        $clientBrowser = '';
        foreach ((array)self::$browsers as $key => $browser) {
            if (self::match($browser, $userAgent)) {
                $clientBrowser = $key;
                break;
            }
        }
        if ($isReTurnVersion && $clientBrowser) {
            $clientBrowser .= '' . self::getVersion($clientBrowser, $userAgent);
        }
        return $clientBrowser;
    }

    /**
     * 获取客户端操作系统
     * @param      $userAgent
     * @param bool $isReTurnVersion
     * @return int|string
     */
    public static function getPlatForm($userAgent, $isReTurnVersion = false)
    {
        if (empty($userAgent)) {
            return '';
        }
        $clientPlatform = '';
        foreach ((array)self::$platforms as $key => $platform) {
            if (self::match($platform, $userAgent)) {
                $clientPlatform = $key;
                break;
            }
        }
        if ($isReTurnVersion && $clientPlatform) {
            $clientPlatform .= '' . self::getVersion($clientPlatform, $userAgent);
        }
        return $clientPlatform;
    }

    /**
     * 查询版本号
     * @param $propertyName
     * @param $userAgent
     * @return string
     */
    public static function getVersion($propertyName, $userAgent)
    {
        $verRegex = array_key_exists($propertyName, self::$versionRegexs)
            ? self::$versionRegexs[$propertyName] : null;

        if (!$verRegex) {
            return '';
        } else {
            $verRegex = (array)$verRegex;
        }

        $match = self::matchVersion($verRegex, $userAgent);//开始匹配
        if ($match && stripos($propertyName, 'window') !== false) {//windown系统版本号需要转换
            return self::getWinVersion($match);
        } else {
            return str_replace('_', '.', $match);
        }
    }

    /**
     * 根据匹配结果转换window系统版本号
     * @param $match
     * @return string
     */
    protected static function getWinVersion($match)
    {
        if ($match == '6.0') {
            return 'Vista';
        } elseif ($match == '6.1') {
            return '7';
        } elseif ($match == '6.2') {
            return '8';
        } elseif ($match == '5.1') {
            return 'XP';
        } elseif ($match == '10.0') {
            return '10';
        } else {
            return $match;
        }
    }

    /**
     * 正则匹配
     * @param $regex
     * @param $userAgent
     * @return bool
     */
    protected static function match($regex, $userAgent)
    {
        return (bool)preg_match(sprintf('#%s#is', $regex), $userAgent, $matches);
    }

    /**
     * 版本号正则匹配
     * @param $regexs
     * @param $userAgent
     * @return string
     */
    protected static function matchVersion($regexs, $userAgent)
    {
        foreach ((array)$regexs as $regex) {
            $regex = str_replace('[VER]', '([\w\.]+)', $regex);
            $match = (bool)preg_match(sprintf('#%s#is', $regex), $userAgent, $matches);
            if ($match) {
                return $matches[1];
            }
        }
        return '';
    }
}