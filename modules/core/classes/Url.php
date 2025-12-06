<?php

/**
 * 授权请查看根目录下的LICENSE.md
 * @author ken <yiiphp@foxmail.com> 
 * @link   https://github.com/yii2cms/yii2cms 
 */

namespace app\modules\core\classes;

use Yii;

/**
 * URL助手类
 */
class Url
{
    /**
     * 生成带语言的URL
     * @param string $url URL
     * @param array $par 参数
     * @param bool $is_pure 是否不带语言
     * @return string 带语言的URL
     */
    public static function create($url, $par = [])
    {
        if (is_string($url) && strpos($url, '://') !== false) {
            return $url;
        }
        if (!is_array($url)) {
            $url = array_merge([$url], $par);
        }
        // 多语言
        $is_language = get_config('is_muit_language', false);
        if ($is_language == 1) {
            $lang = Yii::$app->language;
            if ($url[0] && $url[0] != '/') {
                if (strpos($url[0], '/') === false) {
                    $url[0] = Env::getQueryUri(2) . '/' . $url[0];
                }
                return "/" . $lang . Yii::$app->urlManager->createUrl($url);
            }
        }
        if (isset($url[0]) && strpos($url[0], '/') === false) {
            $url[0] = '/' . Env::getQueryUri(2) . '/' . $url[0];
        }
        return Yii::$app->urlManager->createUrl($url);
    }
}
