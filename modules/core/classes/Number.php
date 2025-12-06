<?php

/**
 * 授权请查看根目录下的LICENSE.md
 * @author ken <yiiphp@foxmail.com> 
 * @link   https://github.com/yii2cms/yii2cms 
 */

namespace app\modules\core\classes;

use Yii;

/**
 * 数字助手类
 */
class Number
{
    /**
     * 销量显示
     * 1000显示为1k 
     */
    public static function sales($sales)
    {
        if ($sales < 1000) {
            return $sales;
        }
        if ($sales < 10000) {
            return round($sales / 1000, 1) . 'k';
        }
        if ($sales < 100000) {
            $formatted = round($sales / 10000, 1);
            return (strpos($formatted, '.0') !== false) ? ($formatted / 1) . 'w' : $formatted . 'w';
        }
        return round($sales / 10000) . 'w+';
    }
    /**
     * 优化数量显示
     * <code>
     * 1.10显示为1.1
     * 1.05显示为1.05
     * 1.00显示为1
     * </code>
     */
    public static function show($num)
    {
        return rtrim(rtrim($num, '0'), '.');
    }
    /**
     * 取字符中的数字
     */
    public static function get($input)
    {
        $pattern = '/(\d+(\.\d+)?)/';
        preg_match_all($pattern, $input, $matches);
        return $matches[1];
    }
    /**
     * float不进位，如3.145 返回3.14
     * 进位的有默认round(3.145) 或sprintf("%.2f",3.145);
     */
    public static function float($float_number, $dot = 2)
    {
        $p = pow(10, $dot);
        return floor($float_number * $p) / $p;
    }
    /**
     * 四舍五入
     * @param $mid_val 逢几进位
     */
    public static function floatUp($float_number, $dot = 2, $mid_val = 5)
    {
        $p = pow(10, $dot);
        if (strpos($float_number, '.') !== false) {
            $a = substr($float_number, strpos($float_number, '.') + 1);
            $a = substr($a, $dot, 1) ?: 0;
            if ($a >= $mid_val) {
                return bcdiv(bcmul($float_number, $p) + 1, $p, $dot);
            } else {
                return bcdiv(bcmul($float_number, $p), $p, $dot);
            }
        }
        $p = pow(10, $dot);
        return floor($float_number * $p) / $p;
    }
}
