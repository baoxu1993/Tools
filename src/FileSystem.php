<?php
/**
 * Created by PhpStorm.
 * User: zb
 * Date: 2020/12/10
 * Time: 15:21
 */

namespace baoxu1993\Tools;

use Hyperf\Di\Annotation\Inject;

class FileSystem
{
    /**
     * @Inject()
     * @var \League\Flysystem\Filesystem
     */
    private $filesystem;

    /**
     * 检测文件上传后缀是否允许
     * @param $ext
     * @throws \Exception
     */
    function checkFileUploadAllowExt($ext)
    {
        //TODO::获取配置信息
        if (!in_array(strtolower($ext), explode(',', getSystemConfig("file_upload.allow_ext")))) {
            autoException(10055);
        }
    }

    /**
     * 文件上传云端
     * @param      $localFile
     * @param      $cloudFile
     * @param bool $fullUrl
     * @return string
     * @throws \League\Flysystem\FileExistsException
     */
    public function cloudUpload($localFile, $cloudFile, bool $fullUrl = false)
    {
        $stream = fopen($localFile, 'r+');
        $filePath = env("FILEPATH") . $cloudFile;
        $this->filesystem->writeStream($filePath, $stream);
        @fclose($stream);

        if ($fullUrl) {
            $filePath = env(env("FILESYSTEM") . "_DOMAIN") . $filePath;
        }
        return $filePath;
    }

    /**
     * 复制云端文件
     * @param $oldPath
     * @param $newPath
     * @return bool
     * @throws \League\Flysystem\FileExistsException
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function cloudCopy($oldPath, $newPath)
    {
        return $this->filesystem->copy($oldPath, $newPath);
    }

    /**
     * 移动/重命名云端文件
     * @param $oldPath
     * @param $newPath
     * @return bool
     * @throws \League\Flysystem\FileExistsException
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function cloudRename($oldPath, $newPath)
    {
        return $this->filesystem->rename($oldPath, $newPath);
    }

    /**
     * 云端文件保存本地
     * @param $ossPath
     * @param $newPath
     * @return bool|false|int
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function cloudDownload($ossPath, $newPath)
    {
        return file_put_contents($newPath, $this->filesystem->readStream($ossPath));
    }

    /**
     * 删除文件
     * @param $file_paths
     * @throws \Exception
     */
    public function cloudDel($file_paths)
    {
        try {
            if (!is_array($file_paths)) {
                $file_paths = [$file_paths];
            }
            foreach ($file_paths as $file_path) {
                if (empty($file_path)) {
                    continue;
                }

                if (substr($file_path, 0, 8) == 'https://' || substr($file_path, 0, 7) == 'http://') {
                    $path = parse_url($file_path)['path'];
                    if (!isset($path['path']) || $path['path'] == '/') {
                        continue;
                    }

                    $file_path = $path;
                }

                if ($this->filesystem->has($file_path)) {
                    $this->filesystem->delete($file_path);
                }
            }
        }
        catch (\Exception $exception) {
            autoException($exception);
        }
    }

    /**
     * 删除本地文件/文件夹
     * @param string|array $paths
     * @throws \Exception
     */
    public function localDel($paths)
    {
        try {
            if (!is_array($paths)) {
                $paths = [$paths];
            }

            foreach ($paths as $path) {
                if (empty($path)) {
                    continue;
                }
                if (is_dir($path)) {
                    $self = $path;
                    $path .= '/';
                    $p = scandir($path);
                    foreach ($p as $val) {
                        //排除目录中的.和..
                        if ($val != "." && $val != "..") {
                            //如果是目录则递归子目录，继续操作
                            if (is_dir($path . $val)) {
                                //子目录中操作删除文件夹和文件
                                $this->localDel($path . $val . '/');
                                //目录清空后删除空文件夹
                                @rmdir($path . $val . '/');
                            } else {
                                //如果是文件直接删除
                                @unlink($path . $val);
                            }
                        }
                    }
                    @rmdir($self);
                } else {
                    @unlink($path);
                }
            }
        }
        catch
        (\Exception $exception) {
            autoException($exception);
        }
    }

    /**
     * 设置文件/文件夹权限
     * @param string $file
     */
    public function localMod(string $file)
    {
        @chown($file, 'www');
        @chmod($file, 0777);
    }

    /**
     * base64图片保存
     * @param string $base64_image_content
     * @param string $filePath
     * @param bool   $uploadCloud
     * @param string $cloudFilePath
     * @param bool   $fullUrl
     * @return string
     * @throws \League\Flysystem\FileExistsException|\Exception
     */
    public function save_base64_image(string $base64_image_content, string $filePath, bool $uploadCloud = false, string $cloudFilePath = '', bool $fullUrl = false)
    {
        //匹配出图片的格式
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result)) {
            $filePath = createDir($filePath);
            $type = $result[2];

            $fileName = getMicrotime() . getRand(10) . ".{$type}";
            $new_file = $filePath . $fileName;
            if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64_image_content)))) {
                //文件上传云端
                if ($uploadCloud) {
                    if (empty($cloudFilePath)) {
                        autoException(10058);
                    }
                    $cloudFile = $cloudFilePath . $fileName;
                    $new_file = $this->cloudUpload($new_file, $cloudFile, $fullUrl);
                }
                return $new_file;
            } else {
                autoException(10057);
            }
        } else {
            autoException(10056);
        }
    }
}