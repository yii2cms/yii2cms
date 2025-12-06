<?php

namespace app\modules\core\classes\sms;

use AlibabaCloud\SDK\Dysmsapi\V20170525\Dysmsapi;
use AlibabaCloud\SDK\Dysmsapi\V20170525\Models\SendSmsRequest;
use AlibabaCloud\Tea\Utils\Utils\RuntimeOptions;
use Darabonba\OpenApi\Models\Config;
use Exception;
use app\modules\core\classes\Log;

/**
 * 阿里云短信
 * https://dysms.console.aliyun.com/overview  
 */
class AliyunDrive
{
    public static $client;
    public static $req;
    /**
     * 发送短信
     * @param string $phone 接收短信的手机号
     * @param string $template_id 短信模板的id
     * @param string $content 短信模板的内容
     * @param array $data 短信模板中的参数
     * @param string $sign_name 短信签名
     * @return bool 是否发送成功
     */
    public static function send($phone, $template_id,  $content, $data = [], $sign_name = null)
    {
        $template_id = trim($template_id);
        $client = self::getClient();
        $par = [
            "phoneNumbers" => $phone,
            "signName" => $sign_name ?: get_config('aliyun.sms.sign'),
            'templateCode' => $template_id,
        ];
        if ($data) {
            $par['templateParam'] = json_encode($data);
        }
        $sendSmsRequest = new SendSmsRequest($par);
        try {
            $res = $client->sendSms($sendSmsRequest, new RuntimeOptions([]));
            if (!$res->body->bizId) {
                $err = $res->body->message ?: '';
                if ($err) {
                    Log::add('阿里云短信发送失败', $err, 'error');
                }
                Log::add('发送短信失败', [
                    'phone' => $phone,
                    'msg' => $err,
                    'sign' => $sign_name,
                ], 'error');
                return false;
            }
            Log::add('发送短信成功', [
                'phone' => $phone,
                'template_id' => $template_id,
                'sign' => $sign_name,
            ]);
            return true;
        } catch (Exception $error) {
            $err = $error->getMessage();
            Log::add('发送短信失败', [
                'phone' => $phone,
                'msg' => $err,
                'sign' => $sign_name,
            ], 'error');
        }
    }

    public static function less()
    {
        return false;
    }

    private static function getClient()
    {
        $config = new Config([
            "accessKeyId" => get_config('aliyun.sms.key_id'),
            "accessKeySecret" => get_config('aliyun.sms.key_secret'),
        ]);
        $config->endpoint = get_config('aliyun.sms.endpoint');
        return new Dysmsapi($config);
    }
}
