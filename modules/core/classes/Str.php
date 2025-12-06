<?php

/**
 * 授权请查看根目录下的LICENSE.md
 * @author ken <yiiphp@foxmail.com> 
 * @link   https://github.com/yii2cms/yii2cms 
 */

namespace app\modules\core\classes;

/**
 * 字符串助手类
 */
class Str
{
    public static $size = ['B', 'KB', 'MB', 'GB', 'TB'];

    /**
     * 距离转换
     * 500m 1km
     * 1公里 
     * @param string $dis 距离
     * @return string　 
     */
    public static function dis($dis)
    {
        $l['公里'] = 1000;
        $l['里']   = 1000;
        $l['m']    = 1;
        foreach ($l as $k => $num) {
            if (strpos($dis, $k) !== false) {
                $dis = str_replace($k, "", $dis);
                $dis = $dis * $num;
            }
        }
        return $dis;
    }
    /**
     * 折扣 100 1 0.1折
     * @param string $price 原价
     * @param string $nowprice 当前价格
     * @return string　 
     */
    public static function discount($price, $nowprice)
    {
        return round(10 / ($price / $nowprice), 1);
    }


    /**
     * 计算时间剩余　  
     * @param   $timestamp 当前时间戳
     * @param   $small_timestamp 自定义时间戳，小于当前时间戳
     * @return array ２天３小时２８分钟１０秒 
     */
    public static function lessTime($timestamp, $small_timestamp = null)
    {
        if (!$small_timestamp) $time = $timestamp;
        else $time = $timestamp - $small_timestamp;
        if ($time <= 0) return -1;
        $days = intval($time / 86400);
        $remain = $time % 86400;
        $hours = intval($remain / 3600);
        $remain = $remain % 3600;
        $mins = intval($remain / 60);
        $secs = $remain % 60;
        return ["d" => $days, "h" => $hours, "m" => $mins, "s" => $secs];
    }

    /**
     * 字节单位自动转换 显示1GB MB等
     * @param string $size 
     * @return string　 
     */
    public static function size($size)
    {
        $units = static::$size;
        for ($i = 0; $size >= 1024 && $i < 4; $i++) {
            $size /= 1024;
        }
        return round($size, 2) . ' ' . $units[$i];
    }

    /**
     * 随机数字
     * @param string $j 位数 　 
     * @return int
     */
    public static function randNumber($j = 4)
    {
        $str = null;
        for ($i = 0; $i < $j; $i++) {
            $str .= mt_rand(0, 9);
        }
        return $str;
    }
    /**
     * 随机字符
     * @param string $j 位数 　 
     * @return string
     */
    public static function rand($j = 8)
    {
        $string = "";
        for ($i = 0; $i < $j; $i++) {
            srand((float)microtime() * 1234567);
            $x = mt_rand(0, 2);
            switch ($x) {
                case 0:
                    $string .= chr(mt_rand(97, 122));
                    break;
                case 1:
                    $string .= chr(mt_rand(65, 90));
                    break;
                case 2:
                    $string .= chr(mt_rand(48, 57));
                    break;
            }
        }
        return $string;
    }

    /**
     * 截取后，用 ...代替被截取的部分
     * @param  string $string 字符串
     * @param  int $length 截取长度
     * @return string
     */
    public static function cut($string, $length, $append = '...')
    {
        $new_str = '';
        preg_match_all("/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/", $string, $info);
        $j = 0;
        for ($i = 0; $i < count($info[0]); $i++) {
            $new_str .= $info[0][$i];
            $j = ord($info[0][$i]) > 127 ? $j + 2 : $j + 1;
            if ($j > $length - 3) {
                return $new_str . $append;
            }
        }
        return join('', $info[0]);
    }
    /**
     * 字符串中包含中文
     * @param string $str 字符串
     * @return bool 是否包含中文
     */
    public static function hasCn($str)
    {
        return preg_match('/[\x{4e00}-\x{9fa5}]/u', $str);
    }
    /**
     * 搜索替换\n , ，空格
     * @param string $name 
     * @return array
     */
    public static function toArray($name)
    {
        if (!$name) {
            return [];
        }
        $replace = [
            "\n",
            "，",
            "、",
            "|",
            ",",
            " ",
            chr(10),
        ];
        $arr = [];
        foreach ($replace as $str) {
            if (strpos($name, $str) !== false) {
                $name = str_replace($str, ',', $name);
            }
        }
        if (strpos($name, ",") !== false) {
            $arr = explode(",", $name);
        }
        if ($arr) {
            $arr = array_filter($arr);
            foreach ($arr as $k => $v) {
                if (!is_array($v)) {
                    $arr[$k] = trim($v);
                } else {
                    $arr[$k] = $v;
                }
            }
        } else {
            $arr = [trim($name)];
        }
        return $arr;
    }
}
