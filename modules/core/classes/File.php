<?php

/**
 * 授权请查看根目录下的LICENSE.md
 * @author ken <yiiphp@foxmail.com> 
 * @link   https://github.com/yii2cms/yii2cms 
 */

namespace app\modules\core\classes;

/**
 * 文件类
 */
class File
{
    /**
     * 获取目录
     * @param string $name 文件路径
     * @return string
     */
    public static function getDir($name)
    {
        return substr($name, 0, strrpos($name, '/'));
    }
    /**
     * 获取文件扩展名
     * @param string $name 文件路径
     * @return string
     */
    public static function getExt($name)
    {
        if (strpos($name, '?') !== false) {
            $name = substr($name, 0, strpos($name, '?'));
        }
        $name = substr($name, strrpos($name, '.'));
        return strtolower(substr($name, 1));
    }
    /**
     * 获取文件名
     * @param string $name 文件路径
     * @return string
     */
    public static function getName($name)
    {
        $name = substr($name, strrpos($name, '/'));
        $name = substr($name, 0, strrpos($name, '.'));
        $name = substr($name, 1);
        return $name;
    }
}
