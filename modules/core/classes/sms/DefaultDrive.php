<?php

namespace app\modules\core\classes\sms;

use app\modules\core\classes\Log;
use app\modules\core\classes\Desensitizer;

/**
 * 默认短信驱动
 */
class DefaultDrive
{
    /**
     * 获取短信配置
     */
    public static function get_config()
    {
        $data['sms_user'] = get_config('sms.default.user');
        $data['sms_pwd'] = get_config('sms.default.pwd');
        $data['sms_ip'] = get_config('sms.default.ip');
        $data['sign'] = get_config('sms.default.sign');
        if (!$data['sms_user'] || !$data['sms_pwd'] || !$data['sms_ip'] || !$data['sign']) {
            throw new \NewException('短信配置错误', 405);
        }
        return $data;
    }
    /**
     * 获取短信剩余条数
     */
    public static function less()
    {
        $c = self::get_config();
        $sms_user = $c['sms.default.user'];
        $sms_pwd = $c['sms.default.pwd'];
        $sms_ip = $c['sms.default.ip'];
        $url = "http://" . $sms_ip . "/plain/qryBal.php?acctno=" . $sms_user . "&passwd=" . $sms_pwd . "&product=C000001";
        $res = file_get_contents($url);
        $res = json_decode($res, true);
        $list = $res['Lists'][0];
        //可发送短信条数
        $out['sms_less'] = $list['ValidCnt'];
        //已发送条数
        $out['sms_used'] = $list['UsedCnt'];
        return $out;
    }
    /**
     * 发送短信
     * @param string $phone 接收短信的手机号
     * @param string $template_id 短信模板的id
     * @param string $content 短信模板的内容
     * @param array $data 短信模板中的参数
     * @param string $sign 短信签名
     * @return bool 是否发送成功
     */
    public static function send($phone, $template_id, $content, $data = [], $sign = null)
    {
        /**
         * 替换 content中的 {key} 对应  $params ['key']=>'value'
         */
        foreach ($data as $key => $value) {
            $content = str_replace('{' . $key . '}', $value, $content);
        }
        $c = self::get_config();
        $sms_user = $c['sms_user'];
        $sms_pwd = $c['sms_pwd'];
        $sms_ip = $c['sms_ip'];
        $sign = $c['sign'];
        $url = "http://" . $sms_ip . "/plain/SmsMt.php?acctno=" . $sms_user . "&passwd=" . $sms_pwd . "&mobile=" . $phone . "&msg=【" . $sign . "】" . $content;
        $res = file_get_contents($url);
        $res = json_decode($res, true);
        $msg = self::code()[$res['RetCode']];
        $content = Desensitizer::number($content);
        if ($res['RetCode'] == 0) {
            Log::add('发送短信成功', [
                'phone' => $phone,
                'content' => $content,
                'sign' => $sign,
            ]);
            return true;
        } else {
            Log::add('发送短信失败', [
                'phone' => $phone,
                'content' => $content,
                'msg'  => $msg,
                'sign' => $sign,
            ], 'error');
            return false;
        }
    }
    /**
     * 短信状态码
     */
    protected static function code()
    {
        return [
            0 => '成功返回',
            100 => '系统忙（因平台侧原因，暂时无法处理提交的短信）',
            101 => '无此短信账号/短信账号未登陆',
            102 => '密码错',
            103 => '提交过快（提交速度超过流速限制）',
            104 => '未知错误（参数配置错）',
            105 => '敏感短信（短信内容包含敏感词）',
            106 => '消息长度错（>系统设定 或 <=0）',
            107 => '无合法手机号码',
            108 => '手机号码个数错',
            109 => '无发送额度（该短信账号可用短信数已使用完）',
            110 => '未定',
            111 => '短信账号自定义扩展号超长',
            112 => '无此产品，短信账号没有订购该产品',
            113 => '模板不存在，或模板检查失败',
            114 => '签名在黑名单',
            115 => '签名不合法，未带签名（短信账号必须带签名的前提下）',
            116 => 'IP 地址在黑名单内',
            117 => 'IP地址认证错,请求调用的IP地址不是系统登记的IP地址',
            118 => '短信账号没有相应的发送权限 / 状态不符',
            119 => '短信账号已过期',
            120 => '未定',
            121 => '手机号码在黑名单',
            122 => '手机号码不在白名单',
            123 => '号码所属运营商，不在短信账号支持范围',
            124 => '手机号码未找到对应运营商',
            125 => '手机号码格式错误',
            126 => '1分钟号码发送频率超限',
            127 => '1小时号码发送频率超限',
            128 => '24小时号码频率发送超限',
            141 => '不在短信账号有效时段',
            199 => '无此类型接口权限',
        ];
    }
}
