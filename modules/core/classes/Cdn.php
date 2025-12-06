<?php


/**
 * 授权请查看根目录下的LICENSE.md
 * @author ken <yiiphp@foxmail.com> 
 * @link   https://github.com/yii2cms/yii2cms 
 */

namespace app\modules\core\classes;

use Yii;

/**
 * CDN
 */
class Cdn
{
    /**
     * 获取cdn域名
     */
    public static function getDoamin()
    {
        $cdn_url = Yii::$app->params['cdn.urls'];
        $cdn_url[] = Yii::$app->request->hostInfo;
        return $cdn_url[array_rand($cdn_url)];
    }
    /**
     * 获取带cdn的url
     * @param string $url 原始url
     * @return string 带cdn的url
     */
    public static function getUrl($url)
    {
        if (strpos($url, '://') !== false) {
            return $url;
        }
        if (substr($url, 0, 1) == '/') {
            return self::getDoamin() . $url;
        } else {
            return self::getDoamin() . '/' . $url;
        }
    }
}
