<?php

/**
 * 授权请查看根目录下的LICENSE.md
 * @author ken <yiiphp@foxmail.com> 
 * @link   https://github.com/yii2cms/yii2cms 
 */

namespace app\modules\core\classes;

use Yii;

/**
 * 阿里云市场请求
 */
class Aliyun
{

    /**
     * 发起请求
     * @param string $url 请求url
     * @param array|string $bodys 请求参数
     * @param string $method 请求方法 POST|GET
     * @param string $content_type 请求类型 json|form
     * @return array
     */
    public static function request($url, $bodys, $method = 'POST', $content_type = 'json')
    {
        $content_type = [
            'json' => 'application/json; charset=UTF-8',
            'form' => 'application/x-www-form-urlencoded; charset=UTF-8',
        ][$content_type] ?? '';
        $curl = curl_init();
        $appcode = get_config('aliyun_market_app_code');
        if (!$appcode) {
            echo json_encode(['code' => 1, 'msg' => Yii::t('app', '请配置阿里云市场appcode')]);
            exit;
        }
        $appcode = trim($appcode);
        $headers = array();
        array_push($headers, "Authorization:APPCODE " . $appcode);
        array_push($headers, "Content-Type" . ":" . $content_type);
        $querys = "";
        if ($bodys) {
            if ($method == 'POST') {
                curl_setopt($curl, CURLOPT_POSTFIELDS, $bodys);
            } else {
                if (is_array($bodys)) {
                    $str = '';
                    foreach ($bodys as $k => $v) {
                        $str .= $k . '=' . $v . "&";
                    }
                    $str = substr($str, 0, -1);
                    if (strpos($url, '?') === false) {
                        $url = $url . '?' . $str;
                    } else {
                        $url = $url . "&" . $str;
                    }
                } else {
                    if (strpos($url, '?') === false) {
                        $url = $url . '?' . $bodys;
                    } else {
                        $url = $url . "&" . $bodys;
                    }
                }
            }
        }
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, true);
        if (1 == strpos("$" . $url, "https://")) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }
        $out_put = curl_exec($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        list($header, $body) = explode("\r\n\r\n", $out_put, 2);
        if ($http_code == 200) {
            $body = json_decode($body, true);
            $body['code'] = 0;
            return $body;
        } else {
            if ($http_code == 400 && strpos($header, "Invalid Param Location") !== false) {
                $output = ['message' => "参数错误", 'code' => 250];
            } elseif ($http_code == 400 && strpos($header, "Invalid AppCode") !== false) {
                $output = ['message' => "AppCode错误", 'code' => 250];
            } elseif ($http_code == 400 && strpos($header, "Invalid Url") !== false) {
                $output = ['message' => "请求的 Method、Path 或者环境错误", 'code' => 250];
            } elseif ($http_code == 403 && strpos($header, "Unauthorized") !== false) {
                $output = ['message' => "服务未被授权（或URL和Path不正确）", 'code' => 250];
            } elseif ($http_code == 403 && strpos($header, "Quota Exhausted") !== false) {
                $output = ['message' => "套餐包次数用完", 'code' => 250];
            } elseif ($http_code == 403 && strpos($header, "Api Market Subscription quota exhausted") !== false) {
                $output = ['message' => "套餐包次数用完，请续购套餐", 'code' => 250];
            } elseif ($http_code == 500) {
                $output = ['message' => "API网关错误", 'code' => 250];
            } elseif ($http_code == 0) {
                $output = ['message' => "URL错误", 'code' => 250];
            } else {
                $headers = explode("\r\n", $header);
                $headList = array();
                foreach ($headers as $head) {
                    $value = explode(':', $head);
                    if (is_array($value)) {
                        $headList[$value[0]] = $value[1] ?? '';
                    }
                }
                $output = ['message' => $headList['x-ca-error-message'], 'http_code' => $http_code, 'code' => 250];
            }
            $log = $output;
            $log['url'] = $url;
            $log['bodys'] = $bodys;
            $log['method'] = $method;
            $log['content_type'] = $content_type;
            if ($output['code'] != 0) {
                Yii::error($output['message']);
            }
            return $output;
        }
    }
}
