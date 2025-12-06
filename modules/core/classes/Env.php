<?php

/**
 * 授权请查看根目录下的LICENSE.md
 * @author ken <yiiphp@foxmail.com> 
 * @link   https://github.com/yii2cms/yii2cms 
 */

namespace app\modules\core\classes;

use Yii;

/**
 * 环境类
 */
class Env
{
    /**
     * 获取输入
     */
    public static function getInput($key = '')
    {
        $data = file_get_contents("php://input");
        try {
            $data = json_decode($data, true);
        } catch (\NewException $e) {
            $data = [];
        }
        return $key ? $data[$key] ?? null : $data;
    }
    /**
     * 获取get
     * @param string $key
     * @return mixed
     */
    public static function getQuery($key = '')
    {
        return $key ? Yii::$app->request->get($key) : Yii::$app->request->get();
    }
    /**
     * 获取post
     * @param string $key
     * @return mixed
     */
    public static function getPost($key = '')
    {
        return $key ? Yii::$app->request->post($key) : Yii::$app->request->post();
    }
    /**
     * 获取所有参数
     * @param string $key
     * @return mixed
     */
    public static function getAll($key = '')
    {
        /**
         * input post get 合并 
         */
        $input = self::getInput();
        $post = self::getPost();
        $query = self::getQuery();
        //array_merge 这些有可能不是数组
        $input = $input ?: [];
        $post = $post ?: [];
        $query = $query ?: [];
        $data = array_merge($input, $post, $query);
        return $key ? $data[$key] ?? null : $data;
    }

    /**
     * 获取语言
     */
    public static function getLang()
    {
        return Yii::$app->request->get('language');
    }
    /**
     * 获取域名，包含http
     * @return string
     */
    public static function getHost()
    {
        return Yii::$app->request->hostInfo;
    }
    /**
     * 获取域名部分，不包含http
     * @return string
     */
    public static function getDomain()
    {
        $str = self::getHost();
        $str = str_replace("https://", "", $str);
        $str = str_replace("http://", "", $str);
        $str = str_replace("/", "", $str);
        $str = trim($str);
        return $str;
    }
    /**
     * 跨域
     */
    public static function cross()
    {
        $cross_domain = get_config('cross_domain', '*');
        if ($cross_domain == '*') {
            header('Access-Control-Allow-Origin: *');
        } else {
            $allowed_origins = Str::toArray($cross_domain);
            $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
            if (in_array($origin, $allowed_origins)) {
                header("Access-Control-Allow-Origin: $origin");
            }
        }
        header('Access-Control-Allow-Origin: ' . $cross_domain);
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: X-Requested-With, Authorization, Content-Type');
        if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            exit(0);
        }
    }
    /**
     * 是否是POST请求
     */
    public static function isPost()
    {
        return Yii::$app->request->isPost;
    }
    /**
     * 是否是ajax请求
     */
    public static function isAjax()
    {
        $isAjax = Yii::$app->request->isAjax;
        if ($isAjax) {
            return true;
        }
        $contentType = Yii::$app->request->headers->get('content-type');
        if ($contentType && strpos($contentType, 'application/json') !== false) {
            return true;
        }
    }
    /**
     * 获取Bearer
     * @return string
     */
    public static function getBearer()
    {
        $Bearer = self::getHeader('Authorization');
        if ($Bearer && substr($Bearer, 0, 6) == 'Bearer') {
            $Bearer = substr($Bearer, 6);
            if ($Bearer) {
                return trim($Bearer);
            }
        }
    }
    /**
     * 获取请求Header
     * @param string $name
     * @return string|null
     */
    public static function getHeader($name)
    {
        return Yii::$app->request->headers->get($name);
    }
    /**
     * 验证图形 验证码
     * @param $captcha
     * @return array
     */
    public static function verifyCapcha()
    {
        $captcha = Yii::$app->request->post('captcha') ?? '';
        if (!$captcha) {
            return false;
        }
        if (Yii::$app->controller->createAction('captcha')->getVerifyCode() != $captcha) {
            return false;
        }
        return true;
    }

    /**
     * 获取当前请求的URI
     * @return string
     */
    public static function getQueryUri($total = 3)
    {
        $moduleId = Yii::$app->controller->module->id;
        $controllerId = Yii::$app->controller->id;
        $actionId = Yii::$app->controller->action->id;
        if (Yii::$app->controller !== null) {
            switch ($total) {
                case 1:
                    return $moduleId;
                    break;
                case 2:
                    return $moduleId . '/' . $controllerId;
                    break;
                default:
                    return $moduleId . '/' . $controllerId . '/' . $actionId;
                    break;
            }
        }
    }
    /**
     * 判断是否是本地环境
     * @return boolean
     */
    public static function isLocal()
    {
        $ip = self::getIp();
        if ($ip === '127.0.0.1' || $ip === '::1') {
            return true;
        } else {
            return false;
        }
    }
    /**
     * 获取IP
     */
    public static function getIp()
    {
        return Yii::$app->request->userIP;
    }
    /**
     * 判断是否是命令行模式
     */
    public static function isCli()
    {
        return PHP_SAPI === 'cli';
    }
}
