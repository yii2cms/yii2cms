<?php

/**
 * 授权请查看根目录下的LICENSE.md
 * @author ken <yiiphp@foxmail.com> 
 * @link   https://github.com/yii2cms/yii2cms 
 */

namespace app\modules\core\classes;

use Yii;

/**
 * 下载类
 */
class Download
{
    public static $downloadDir = 'uploads/download';

    /**
     * 下载资源文件到本地
     * @param string $url 文件URL
     * @param string $output 自定义输出文件名
     * @param array $allow_mime 允许的MIME类型数组
     * @return string|bool 返回本地文件路径或false
     */
    public static function file($url, $output = '', $allow_mime = ['image/*', 'video/*'])
    {
        // 检查输出文件名是否已存在
        if ($output) {
            $localUrl = '/' . self::$downloadDir . '/' . $output;
            $file = Yii::getAlias('@webroot') . $localUrl;
            if (file_exists($file)) {
                return $localUrl;
            }
        }

        // 获取文件MIME类型
        $contentType = Mime::getUrlMime($url);

        // 检查MIME类型是否在允许列表中
        foreach ($allow_mime as $mime) {
            $mime = str_replace('*', '', $mime);
            if (strpos($contentType, $mime) !== false) {
                // 下载文件内容
                $context = Curl::sendGet($url);
                if (!$context) {
                    return false;
                }

                // 确定本地文件路径
                $ext = File::getExt($url);
                if (!$output) {
                    $localUrl = '/' . self::$downloadDir . '/' . md5($url) . '.' . $ext;
                }

                // 创建目录并保存文件
                $file = Yii::getAlias('@webroot') . $localUrl;
                $dir  = File::getDir($file);
                if (!is_dir($dir)) {
                    mkdir($dir, 0777, true);
                }

                file_put_contents($file, $context);
                return $localUrl;
            }
        }

        return false;
    }
}
