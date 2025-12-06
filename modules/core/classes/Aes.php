<?php

/**
 * 授权请查看根目录下的LICENSE.md
 * @author ken <yiiphp@foxmail.com> 
 * @link   https://github.com/yii2cms/yii2cms 
 */

namespace app\modules\core\classes;

use Yii;

/**
 * 加密  
 */
class Aes
{
    /**
     * 加密
     * @param string|array $data 要加密的数据
     * @param string $method 加密方法
     * @return string 加密后的数据
     */
    public static function encode($data, $method = 'AES-128-CBC')
    {
        $iv = Yii::$app->params["aes.iv"];
        $secret_key = Yii::$app->params["aes.secret"];
        if (is_array($data)) {
            $data = json_encode($data);
        }
        return base64_encode(openssl_encrypt($data, $method, $secret_key, OPENSSL_RAW_DATA, $iv));
    }
    /**
     * 解密
     * @param string $data 加密后的数据
     * @param string $method 加密方法
     * @return string|array 解密后的数据
     */
    public static function decode($data, $method = 'AES-128-CBC')
    {
        $iv = Yii::$app->params["aes.iv"];
        $secret_key = Yii::$app->params["aes.secret"];
        $data = openssl_decrypt(base64_decode($data), $method, $secret_key, OPENSSL_RAW_DATA, $iv);
        try {
            $data = json_decode($data, true);
        } catch (\Throwable $th) {
        }
        return $data;
    }
}
