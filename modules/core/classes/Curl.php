<?php

/**
 * 授权请查看根目录下的LICENSE.md
 * @author ken <yiiphp@foxmail.com> 
 * @link   https://github.com/yii2cms/yii2cms 
 */

namespace app\modules\core\classes;

use Yii;

/**
 * Curl 类
 */
class Curl
{
    /**
     * 配置
     * @param $option
     */
    public static $option = [
        'timeout' => 10,
        'verify' => false,
    ];
    /**
     * PUT 请求
     * @param $upload_url 上传url
     * @param $local_file 本地文件路径
     * @param $headers 请求头
     * @param $timeout 超时时间
     */
    public static function sendPut($upload_url, $local_file, $headers = [], $timeout = 60)
    {
        if (!file_exists($local_file)) {
            return false;
        }
        $body      = file_get_contents($local_file);
        $client    = self::init();
        $request   = new \GuzzleHttp\Psr7\Request('PUT', $upload_url, $headers, $body);
        $response  = $client->send($request, ['timeout' => $timeout]);
        if ($response->getStatusCode() == 200) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * POST 请求
     * @param $url
     * @param $data ['body'=>] || ['json]=>] || ['form_params'=>[]]
     */
    public static function sendPost($url, $data = [])
    {
        $client = self::init();
        $res = $client->request('POST', $url, $data);
        return (string) $res->getBody();
    }
    /**
     * GET 请求
     * @param $url
     * @param $data ['body'=>] || ['json]=>] || ['form_params'=>[]]
     */
    public static function sendGet($url, $data = [])
    {
        $client = self::init();
        if ($data) {
            if (strpos($url, '?') === false) {
                $url .= '?' . http_build_query($data);
            } else {
                $url .= '&' . http_build_query($data);
            }
        }
        $res = $client->request('GET', $url);
        return (string) $res->getBody();
    }
    /**
     * 初始化
     */
    public static function init()
    {
        $client = new \GuzzleHttp\Client(self::$option);
        return $client;
    }
}
