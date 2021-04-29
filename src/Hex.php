<?php
/**
 * Created by PhpStorm.
 * User: zb
 * Date: 2021/4/29
 * Time: 16:37
 */

namespace baoxu1993\Tools;


class Hex
{
    /**
     * 10进制转62进制
     * @param $dec
     * @return string
     */
    function from10to62($dec)
    {
        $dict = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $result = '';
        do {
            $result = $dict[$dec % 62] . $result;
            $dec = intval($dec / 62);
        } while ($dec != 0);
        return $result;
    }

    /**
     * 进制转10进制
     * @param $str
     * @return float|int
     */
    function from62to10($str)
    {
        $dict = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $len = strlen($str);
        $dec = 0;
        for ($i = 0; $i < $len; $i++) {
            //找到对应字典的下标
            $pos = strpos($dict, $str[$i]);
            $dec += $pos * pow(62, $len - $i - 1);
        }
        return $dec;
    }


    /**
     * 数字62进制加密
     * @param int $id 数字应小于999999999
     * @return string
     * @throws \Exception
     */
    function encId(int $id)
    {
        if ($id > 999999999) {
            autoException(10045);
        }
        $id = (int)(rand(1, 9) . "000000000") + $id;
        $aesId = strrev(self::from10to62((int)$id));
        $rand = rand(1, strlen($aesId) - 2);
        $lo = substr($aesId, $rand, 1);
        $aesId = substr($aesId, 0, $rand) . substr($aesId, $rand + 1);
        $loArray = ['', '', '', '', '', '', '', '', '', '', '', 'a', 'x', 'G', 'y', 'D', 'E', '1', 'B', '', '', 'Y', 'z', '0', '3', 'w', 'F', 'Q', 's', '', '', 'H', 'd', 'J', 'b', '4', 'M', 'C', 'e', '', '', 'f', 'P', '8', 'V', 'K', 'L', 't', 'r', '', '', 'R', 'k', 'l', '2', '5', 'A', 'm', 'n', '', '', 'I', 'u', 'p', 'q', 'Z', 'v', 'c', 'N', '', '', 'O', 'X', 'T', '6', '7', 'W', 'g', 'h', '', '', 'i', 'j', '9', 'S', 'U', 'o'];
        $in = array_search($lo, $loArray);
        $inStr = strrev(self::from10to62((int)strrev((string)($rand . $in))));
        $lastStr = strrev($aesId . $inStr);
        return $lastStr;
    }

    /**
     * 数字62进制解密
     * @param string $encId
     * @return string
     */
    function decId(string $encId)
    {
        $encId = strrev($encId);
        //取出加权因子
        $inStr = strrev((string)self::from62to10((string)strrev(substr($encId, -2))));

        $rand = substr($inStr, 0, 1);
        $loArray = ['', '', '', '', '', '', '', '', '', '', '', 'a', 'x', 'G', 'y', 'D', 'E', '1', 'B', '', '', 'Y', 'z', '0', '3', 'w', 'F', 'Q', 's', '', '', 'H', 'd', 'J', 'b', '4', 'M', 'C', 'e', '', '', 'f', 'P', '8', 'V', 'K', 'L', 't', 'r', '', '', 'R', 'k', 'l', '2', '5', 'A', 'm', 'n', '', '', 'I', 'u', 'p', 'q', 'Z', 'v', 'c', 'N', '', '', 'O', 'X', 'T', '6', '7', 'W', 'g', 'h', '', '', 'i', 'j', '9', 'S', 'U', 'o'];
        $lo = $loArray[(int)substr($inStr, 1)];

        $aesId = substr($encId, 0, -2);
        $aesId = substr($aesId, 0, $rand) . $lo . substr($aesId, $rand);
        return (int)substr(self::from62to10(strrev($aesId)), 1);
    }
}