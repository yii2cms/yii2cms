<?php

/**
 * 授权请查看根目录下的LICENSE.md
 * @author ken <yiiphp@foxmail.com> 
 * @link   https://github.com/yii2cms/yii2cms 
 */

namespace app\modules\core\classes;

use Yii;

/**
 * 时间助手类
 */
class Time
{
    /**
     * 取今日0点时间戳
     */
    public static function today()
    {
        return strtotime(date("Y-m-d 00:00:00"));
    }
    /**
     * 取每月
     * @param string $start_month 开始时间
     * @param string $end_month 结束时间
     * @return array 每个月的时间范围
     */
    public static function getEveryMonth($start_month, $end_month)
    {
        $list = [];
        while ($start_month < $end_month) {
            $time = strtotime($start_month);
            $last_day_of_month = date("Y-m-d", strtotime("last Day of this month 23:59:59", $time));
            $list[] = [$start_month, $last_day_of_month];
            $start_month = date("Y-m-01", strtotime("+1 month", $time));
        }
        return $list;
    }
    /**
     * 取月最后一天
     * @param string $month 月份
     * @return string 月最后一天
     */
    public static function getLastDay($month)
    {
        if (strpos($month, '-') !== false) {
            $month = strtotime($month);
        }
        return date("Y-m-d", strtotime("last Day of this month 23:59:59", $month));
    }
    /**
     * 多少岁
     * @param string|int $bornUnix 出生日期 
     * @return string 年龄
     */
    public static function getAge($bornUnix)
    {
        if (strpos($bornUnix, ' ') !== false || strpos($bornUnix, '-') !== false) {
            $bornUnix = strtotime($bornUnix);
        }
        return ceil((time() - $bornUnix) / 86400 / 365);
    }
    /**
     * 计算时间剩余
     * @param string|int $a 时间1
     * @param string|int $b 时间2
     * @return string ２天３小时２８分钟１０秒
     */
    public static function getSecToTime($a, $b = null)
    {
        if (!$b) {
            $time = $a;
        } else {
            $time = $a - $b;
        }
        if ($time <= 0) {
            return -1;
        }
        $days = intval($time / 86400);
        $remain = $time % 86400;
        $hours = intval($remain / 3600);
        $remain = $remain % 3600;
        $mins = intval($remain / 60);
        $secs = $remain % 60;
        return ["d" => $days, "h" => $hours, "m" => $mins, "s" => $secs];
    }

    /**
     * 最近30天
     * @param int $day 天数
     * @param string $separate 分隔符
     * @param string $add_or_sub 操作符
     * @return array 最近30天
     */
    public static function getDays($day = 30, $separate = "-", $add_or_sub = '-')
    {
        $arr = [];
        for ($i = 0; $i < $day; $i++) {
            $arr[] = date("Y" . $separate . "m" . $separate . "d", strtotime($add_or_sub . $i . ' days'));
        }
        if ($add_or_sub == '-') {
            $arr = array_reverse($arr);
        }
        return $arr;
    }

    /**
     * 返回最近几月
     * @param int $num 数量
     * @param string $separate 分隔符
     * @param string $add_or_sub 操作符
     * @return array 最近几月
     */
    public static function getMonths($num = 5, $separate = "-", $add_or_sub = '-')
    {
        $list = [];
        for ($i = 0; $i < $num; $i++) {
            $list[] = date("Y" . $separate . "m", strtotime($add_or_sub . $i . " month", time()));
        }
        if ($add_or_sub == '+') {
            return $list;
        }
        $list = array_reverse($list);
        return $list;
    }

    /**
     * 返回最近几年
     * @param int $num 数量
     * @param string $add_or_sub 操作符
     * @return array 最近几年
     */
    public static function getYears($num = 5, $add_or_sub = '-')
    {
        if ($add_or_sub == '-') {
            $start = date("Y", strtotime($add_or_sub . ($num - 1) . " year", time()));
        } else {
            $start = date("Y");
        }
        $list = [];
        for ($i = 1; $i <= $num; $i++) {
            $list[] = $start++;
        }
        return $list;
    }
    /**
     * 取今日、本周、本月、本年、昨日、上周、上月、上年
     * @param string $key 时间范围
     * @param bool $date_format 是否返回日期格式
     * @return array 时间范围
     */
    public static function get($key = '', $date_format = false)
    {
        $arr = [
            'today' => ['today', 'tomorrow'],
            'yesterday' => ['yesterday', 'today'],
            'week' => ['this week 00:00:00', 'next week 00:00:00'],
            'lastweek' => ['last week 00:00:00', 'this week 00:00:00'],
            'month' => ['first Day of this month 00:00:00', 'first Day of next month 00:00:00'],
            'lastmonth' => ['first Day of last month 00:00:00', 'first Day of this month 00:00:00'],
            'year' => ['this year 1/1', 'next year 1/1'],
            'lastyear' => ['last year 1/1', 'this year 1/1'],
        ];
        if (!$key) {
            $list = [];
            foreach ($arr as $k => $v) {
                $a = strtotime($v[0]);
                $b = strtotime($v[1]) - 1;
                if ($date_format) {
                    $a = date('Y-m-d 00:00:00', $a);
                    $b = date('Y-m-d 23:59:59', $b);
                }
                $list[$k] = [$a, $b];
            }
            return $list;
        }
        $data = $arr[$key];
        if ($data) {
            $ret = [
                strtotime($data[0]),
                strtotime($data[1]) - 1,
            ];
            if ($date_format) {
                $ret = [
                    date('Y-m-d 00:00:00', $ret[0]),
                    date('Y-m-d 23:59:59', $ret[1]),
                ];
            }
            return $ret;
        }
    }
    /**
     * 获取时间差
     * @param $time
     * @return string
     */
    public static function ago($time)
    {
        if (strpos($time, '-') !== false) {
            $time = strtotime($time);
        }
        $rtime = date("m-d H:i", $time);
        $top = date("Y-m-d H:i", $time);
        $htime = date("H:i", $time);
        $time = time() - $time;
        if ($time < 60) {
            $str = '刚刚';
        } elseif ($time < 60 * 60) {
            $min = floor($time / 60);
            $str = $min . '分钟前';
        } elseif ($time < 60 * 60 * 24) {
            $h = floor($time / (60 * 60));
            $str = $h . '小时前 ' . $htime;
        } elseif ($time < 60 * 60 * 24 * 3) {
            $d = floor($time / (60 * 60 * 24));
            if ($d == 1) {
                $str = '昨天 ' . $rtime;
            } else {
                $str = '前天 ' . $rtime;
            }
        } else {
            $str = $top;
        }
        return $str;
    }
    /**
     * 当前时间是周几
     */
    public static  function getWeekName($date)
    {
        $weekarray = array("日", "一", "二", "三", "四", "五", "六");
        return $weekarray[date("w", strtotime($date))];
    }
}
