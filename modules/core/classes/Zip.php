<?php

/**
 * 授权请查看根目录下的LICENSE.md
 * @author ken <yiiphp@foxmail.com> 
 * @link   https://github.com/yii2cms/yii2cms 
 */

namespace app\modules\core\classes;

use Alchemy\Zippy\Zippy;
use Yii;

/**
 * 压缩包助手类
 */
class Zip
{
    /**
     * 解压压缩包
     * @param string $zip_file 压缩包路径
     * @param string $extract_dir 解压目录
     */
    public static function extract($zip_file, $extract_dir)
    {
        $zippy = Zippy::load();
        $archive = $zippy->open($zip_file);
        Dir::create($extract_dir);
        $archive->extract($extract_dir);
    }
    /**
     * 创建压缩包
     * @param string $zip_file 压缩包路径
     * @param array $files 文件列表
     */
    public static function create($zip_file, $files = [])
    {
        if (!$files) {
            throw new \NewException(t('创建压缩包失败，文件列表为空'), 403);
        }
        foreach ($files as $k => $v) {
            if (strpos($v, Yii::getAlias('@webroot')) === false) {
                throw new \NewException(t('Access Deny'), 403);
            }
            if (!file_exists($v)) {
                unset($files[$k]);
            }
        }
        if (!$files) {
            throw new \NewException(t('创建压缩包失败，文件列表为空'), 403);
        }
        $zippy = Zippy::load();
        $list = [];
        foreach ($files as $name => $path) {
            if (is_numeric($name)) {
                $name = basename($path);
            }
            $list[$name] = $path;
        }
        $dir = File::getDir($zip_file);
        Dir::create($dir);
        $zippy->create($zip_file, $list);
    }
}
