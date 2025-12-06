<?php

/**
 * 授权请查看根目录下的LICENSE.md
 * @author ken <yiiphp@foxmail.com> 
 * @link   https://github.com/yii2cms/yii2cms 
 */

namespace app\modules\core\classes;

/**
 * 脱敏类
 */
class Desensitizer
{
    /**
     * 脱敏手机号：隐藏中间4位，如 13812345678 -> 138****5678
     * @param string $phone
     * @return string
     */
    public static function phone($phone)
    {
        if (empty($phone) || !preg_match('/^1[3-9]\d{9}$/', $phone)) {
            return $phone;
        }
        return substr($phone, 0, 3) . '****' . substr($phone, -4);
    }

    /**
     * 脱敏昵称：保留首尾字符，中间用*替换，如 张三丰 -> 张*丰
     * @param string $nickname
     * @return string
     */
    public static function nickname($nickname)
    {
        if (empty($nickname)) {
            return $nickname;
        }
        $length = mb_strlen($nickname, 'UTF-8');
        if ($length <= 2) {
            return $nickname;
        }
        $firstChar = mb_substr($nickname, 0, 1, 'UTF-8');
        $lastChar = mb_substr($nickname, -1, 1, 'UTF-8');
        return $firstChar . '*' . $lastChar;
    }

    /**
     * 脱敏身份证号：显示前6位和后4位，中间用*替换，如 110101199003071234 -> 110101****1234
     * @param string $idCard
     * @return string
     */
    public static function idCard($idCard)
    {
        if (empty($idCard) || !preg_match('/^\d{6}\d{8}\d{3}[\dXx]$/', $idCard)) {
            return $idCard;
        }
        return substr($idCard, 0, 6) . '****' . substr($idCard, -4);
    }
    /**
     * 脱敏6位数字验证码：显示前2位和后1位，中间用*替换，如 123456 -> 12***6
     * 从字符串中提取连续6位数字进行脱敏，如 你了123456这年 -> 你了12***6这年
     * @param string $number
     * @return string
     */
    public static function number($number)
    {
        if (empty($number)) {
            return $number;
        }
        // 尝试提取连续6位数字
        if (preg_match('/\d{6}/', $number, $matches)) {
            $code = $matches[0];
            $desensitizedCode = substr($code, 0, 2) . '***' . substr($code, -1);
            // 替换原字符串中的6位数字
            return preg_replace('/\d{6}/', $desensitizedCode, $number, 1);
        }
        return $number;
    }
}
