<?php

/**
 * 授权请查看根目录下的LICENSE.md
 * @author ken <yiiphp@foxmail.com> 
 * @link   https://github.com/yii2cms/yii2cms 
 */

namespace app\modules\core\classes;

use Yii;

/**
 * Cookie 操作封装类
 */
class Cookie
{
    /**
     * 设置 Cookie
     * @param string $name Cookie名称
     * @param mixed $value Cookie值
     * @param int $expire 过期时间（秒）
     * @param string $path 路径
     * @param string $domain 域名
     * @param bool $secure 是否仅HTTPS
     * @param bool $httpOnly 是否仅HTTP访问
     * @return bool
     */
    public static function set($name, $value, $expire = 0, $path = '/', $domain = '', $secure = false, $httpOnly = true)
    {
        $cookie = new \yii\web\Cookie([
            'name'   => $name,
            'value'  => $value,
            'expire' => $expire > 0 ? time() + $expire : 0,
            'path'   => $path,
            'domain' => $domain,
            'secure' => $secure,
            'httpOnly' => $httpOnly,
        ]);

        Yii::$app->response->cookies->add($cookie);
        $_COOKIE[$name] = $value;
        return true;
    }

    /**
     * 获取 Cookie
     * @param string $name Cookie名称
     * @param mixed $defaultValue 默认值
     * @return mixed
     */
    public static function get($name, $defaultValue = null)
    {
        return Yii::$app->request->cookies->getValue($name, $defaultValue);
    }

    /**
     * 删除 Cookie
     * @param string $name Cookie名称
     * @param string $path 路径
     * @param string $domain 域名
     * @return bool
     */
    public static function remove($name, $path = '/', $domain = '')
    {
        $cookie = new \yii\web\Cookie([
            'name'   => $name,
            'value'  => null,
            'expire' => 1, // 设置为过去时间
            'path'   => $path,
            'domain' => $domain,
        ]);
        Yii::$app->response->cookies->remove($name);
        Yii::$app->response->cookies->add($cookie);
        return true;
    }

    /**
     * 检查 Cookie 是否存在
     * @param string $name Cookie名称
     * @return bool
     */
    public static function has($name)
    {
        return Yii::$app->request->cookies->has($name);
    }
}
