<?php

/**
 * 授权请查看根目录下的LICENSE.md
 * @author ken <yiiphp@foxmail.com> 
 * @link   https://github.com/yii2cms/yii2cms 
 */

namespace app\modules\core\classes;

use Yii;

/**
 * 目录类
 */
class Dir
{
    /**
     * 创建目录
     * @param string|array $arr 目录路径
     * @return void
     */
    public static function create($arr)
    {
        if (is_string($arr)) {
            $v = $arr;
            if (!is_dir($v)) {
                mkdir($v, 0777, true);
            }
        } elseif (is_array($arr)) {
            foreach ($arr as $v) {
                if (!is_dir($v)) {
                    mkdir($v, 0777, true);
                }
            }
        }
    }
    /**
     * 获取目录下的所有文件
     * @param string $path 目录路径
     * @return array
     */
    public static function getDeep($path)
    {
        $arr = array();
        $arr[] = $path;
        if (is_file($path)) {
        } else {
            if (is_dir($path)) {
                $data = scandir($path);
                if (!empty($data)) {
                    foreach ($data as $value) {
                        if ($value != '.' && $value != '..') {
                            $sub_path = $path . "/" . $value;
                            $temp = self::getDeep($sub_path);
                            $arr = array_merge($temp, $arr);
                        }
                    }
                }
            }
        }
        return $arr;
    }
}
