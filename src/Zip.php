<?php

namespace baoxu1993\Tools;

class Zip
{
    /**
     * 文件压缩
     * @param string $zipName
     * @param array  $files
     * @param bool   $delOld
     * @return bool
     * @throws \Exception
     */
    public function doZip(string $zipName, array $files, bool $delOld = false)
    {
        $zip = new \ZipArchive();
        if ($zip->open($zipName, \ZIPARCHIVE::CREATE)) {
            $oldFile = [];
            foreach ($files as $k => $v) {
                if (is_array($v)) {
                    if (!file_exists($v['fileName'])) {
                        continue;
                    }
                    $zip->addFile($v['fileName'], $v['localName']);
                    $oldFile[] = $v['fileName'];
                } else {
                    if (!file_exists($v)) {
                        continue;
                    }
                    //获取文件名
                    $path_parts = pathinfo($v);
                    $zip->addFile($v, $path_parts['basename']);
                    $oldFile[] = $v;
                }
            }
            $zip->close();

            //删除源文件
            if ($delOld && !empty($oldFile)) {
                //(new File())->localDel($oldFile);
            }
            return true;
        } else {
            //autoException(1);
        }
    }
}