<?php
/**
 * Created by PhpStorm.
 * ShopAdminUser: zb
 * Date: 2020/7/29
 * Time: 13:44
 */

namespace baoxu1993\Tools;


class Tree
{
    private $data;
    private $key;
    private $parentKey;
    private $treeList = [];
    private $childIds = [];

    public function __construct($data, $key = 'id', $parentKey = 'parent_id')
    {
        $this->data = array_column($data, NULL, $key);
        $this->key = $key;
        $this->parentKey = $parentKey;
    }

    /**
     * 获取树状结构
     * @return array
     */
    public function getTree()
    {
        $this->MakeTree();
        return $this->treeList;
    }

    /**
     * 生成树状结构
     */
    private function MakeTree()
    {
        $this->tree();
        foreach ($this->childIds as $v) {
            unset($this->treeList[$v]);
        }
        $this->treeList = array_values($this->treeList);
    }

    /**
     * 解析树状结构
     * @param array $list
     * @return array
     */
    private function tree($list = [])
    {
        if (empty($list)) {
            $list = $this->treeList;
        }
        if (empty($list)) {
            foreach ($this->data as $k => $v) {
                $v['child'] = $this->getChild($v[$this->key]);
                if (!empty($v['child'])) {
                    $v['child'] = $this->tree($v['child']);
                } else {
                    unset($v['child']);
                }
                $this->treeList[$v[$this->key]] = $v;
            }
        } else {
            foreach ($list as $k => $v) {
                $v['child'] = $this->getChild($v[$this->key]);
                if (!empty($v['child'])) {
                    $v['child'] = $this->tree($v['child']);
                } else {
                    unset($v['child']);
                }
                $list[$k] = $v;
            }
        }
        return $list;
    }

    /**
     * 获取子数组
     * @param $parentId
     * @return array
     */
    private function getChild($parentId)
    {
        $list = [];
        foreach ($this->data as $k => $v) {
            if (isset($v[$this->parentKey]) && $v[$this->parentKey] == $parentId) {
                $this->childIds[$v[$this->key]] = $v[$this->key];
                $list[] = $v;
            }
        }
        return $list;
    }
}