<?php
/**
 * Created by PhpStorm.
 * User: zb
 * Date: 2021/4/29
 * Time: 16:44
 */

namespace baoxu1993\Tools;


class Excel
{
    /**
     * 获取excel列名
     * @param int $pColumnIndex
     * @return mixed
     */
    function getEnglishSerialNumber(int $pColumnIndex)
    {
        $_indexCache = array();
        if (!isset($_indexCache[$pColumnIndex])) {
            if ($pColumnIndex < 26) {
                $_indexCache[$pColumnIndex] = chr(65 + $pColumnIndex);
            } elseif ($pColumnIndex < 702) {
                $_indexCache[$pColumnIndex] = chr(64 + ($pColumnIndex / 26)) . chr(65 + $pColumnIndex % 26);
            } else {
                $_indexCache[$pColumnIndex] = chr(64 + (($pColumnIndex - 26) / 676)) . chr(65 + ((($pColumnIndex - 26) % 676) / 26)) . chr(65 + $pColumnIndex % 26);
            }
        }
        return $_indexCache[$pColumnIndex];

    }
}