<?php
/**
 * Created by PhpStorm.
 * User: zb
 * Date: 2021/4/29
 * Time: 16:46
 */

namespace baoxu1993\Tools;


class Arr
{
    /**
     * @param $data
     * @throws \Exception
     */
    public static function isArray($data)
    {
        if (!is_array($data)) {
            throw new \Exception("不是数组");
        }
    }

    /**
     * 获取删除的ID
     * @param array $newIds
     * @param array $oldIds
     * @return array
     */
    public static function getDelIds($newIds = [], $oldIds = [])
    {
        return array_diff($oldIds, $newIds);
    }

    /**
     * 返回数组一对多的值
     * @param array  $data
     * @param string $key
     * @param string $returnKey
     * @return array
     */
    function arrayGroupOneToMany(array $data, string $key, string $returnKey = '')
    {
        $tmp = [];
        foreach ($data as $k => $v) {
            $tmp[$v[$key]][] = empty($returnKey) ? $v : $v[$returnKey];
        }

        return $tmp;
    }
}