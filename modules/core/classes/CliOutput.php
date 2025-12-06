<?php

/**
 * 授权请查看根目录下的LICENSE.md
 * @author ken <yiiphp@foxmail.com> 
 * @link   https://github.com/yii2cms/yii2cms 
 */

namespace app\modules\core\classes;

/**
 * 命令行输出
 */
class CliOutput
{
    /**
     * 防止重复执行
     * @param $argv 命令行数组
     * @param $find 查找的命令
     */
    public static function once($argv, $find = 'php cmd.php')
    {
        $cmd_line = "php";
        $str = '';
        foreach ($argv as $v) {
            $str .= " " . $v;
        }
        $cmd_line = $cmd_line . $str;
        exec("ps aux|grep '" . $cmd_line . "'", $arr);
        $list = [];
        foreach ($arr as $v) {
            if ($v) {
                $v = str_replace('  ', '', $v);
                preg_match('(' . $find . '.*)', $v, $output);
                $new = $output[0];
                if ($new) {
                    $list[] = trim($new);
                }
            }
        }
        $new_list = [];
        foreach ($list as $v => $k) {
            if (!$new_list[$k]) {
                $new_list[$k] = 1;
            } else {
                $new_list[$k]++;
            }
        }
        if ($new_list && $new_list[$cmd_line] > 2) {
            echo "程序已在运行，不能重复执行！\n";
            exit();
        }
    }

    /**
     * 信息
     * @param string $message 信息
     */
    public static function info($message)
    {
        echo "\033[34m" . $message . "  \033[0m \n";
    }
    /**
     * 成功
     * @param string $message 成功信息
     */
    public static function success($message)
    {
        echo "\033[32m" . $message . "  \033[0m \n";
    }
    /**
     * 错误
     * @param string $message 错误信息
     */
    public static function error($message)
    {
        echo "\033[31m" . $message . " \033[0m \n";
    }
    /**
     * 警告
     * @param string $message 警告信息
     */
    public static function warning($message)
    {
        echo "\033[33m" . $message . " \033[0m \n";
    }
    /**
     * 调试
     * @param string $message 调试信息
     */
    public static function debug($message)
    {
        echo "\033[35m" . $message . " \033[0m \n";
    }
}
