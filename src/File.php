<?php
/**
 * Created by PhpStorm.
 * User: zb
 * Date: 2021/4/29
 * Time: 16:34
 */

namespace baoxu1993\Tools;


class File
{
    /**
     * 递归创建目录
     * @param $dir
     * @return bool
     */
    public static function create_folders($dir)
    {
        return is_dir($dir) or (self::create_folders(dirname($dir)) and mkdir($dir, 0777, true));
    }

    /**
     * 创建文件夹
     * @param $dir
     * @return string
     */
    public static function createDir($dir)
    {
        if (substr($dir, 0, 1) != '/') {
            $dir = "/{$dir}";
        }

        if (substr($dir, -1, 1) != '/') {
            $dir = "{$dir}/";
        }

        $dir = BASE_PATH . $dir;
        if (!file_exists($dir)) {
            @mkdir($dir, 0777, true);
            @chown($dir, 'www');
            @chmod($dir, 0777);
        }
        return $dir;
    }

    /**
     * 获取文件夹下的文件列表
     * @param $dir
     * @return array
     */
    public static function getDirFiles($dir)
    {
        $fileArray[] = NULL;
        if (false != ($handle = opendir($dir))) {
            while (false !== ($file = readdir($handle))) {
                if ($file != '.' && $file != '..') {
                    $fileArray[] = $file;
                }
            }
            //关闭句柄
            closedir($handle);
        }
        return $fileArray;
    }


    /**
     * 创建文件
     * @param $fileName
     * @return string
     */
    public static function createFile($fileName)
    {
        if (substr($fileName, 0, 1) != '/') {
            $fileName = "/{$fileName}";
        }
        $file = pathinfo($fileName);
        $filePath = self::createDir($file['dirname']);

        if (!empty($file['basename'])) {
            $realFile = "{$filePath}{$file['basename']}";
            file_put_contents($realFile, '');
        } else {
            $realFile = $filePath;
        }

        return $realFile;
    }
}