<?php

/**
 * 授权请查看根目录下的LICENSE.md
 * @author ken <yiiphp@foxmail.com> 
 * @link   https://github.com/yii2cms/yii2cms 
 */

namespace app\modules\core\classes;

use app\modules\core\models\Config as ConfigModel;
use Yii;
use app\modules\core\classes\Log;

/**
 * 配置类
 */
class Config
{
    /**
     * 获取配置
     * @param string $key 配置键名
     * @return array|string|null 配置内容
     */
    public static function get($key)
    {
        $key = trim($key);
        $config = ConfigModel::findOne(['key' => $key]);
        if ($config) {
            $content = $config->content;
            if (!$content) {
                return;
            }
            $arr = json_decode($content, true);
            if ($arr) {
                return $arr;
            } else {
                return $content;
            }
        }
        return null;
    }
    /**
     * 设置配置帮助
     * @param string $key 配置键名
     * @param string $help 配置帮助
     */
    public static function help($key, $help)
    {
        $config = ConfigModel::findOne(['key' => $key]);
        if ($config) {
            $config->help = $help;
            $config->save();
        }
    }
    /**
     * 初始化
     * @param string $key 配置键名
     * @param array|string $value 配置内容
     * @param string $name 配置名称
     * @param string $type 配置类型
     * @return bool 是否初始化成功
     */
    public static function init($key, $value, $name = '', $type = 'input', $type_value = [])
    {
        $key = trim($key);
        $config = ConfigModel::findOne(['key' => $key]);
        $content = is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value;

        if (!$config) {
            $config = new ConfigModel();
            $config->key = $key;
            $config->content = $content;
            $config->type_value = $type_value;
            $config->name = $name;
            $config->type = $type;
            $config->save();
        } else {
            $config->name = $name;
            $config->type = $type;
            $config->type_value = $type_value;
            $config->save(false, ['name', 'type', 'type_value']);
        }
        return false;
    }
    /**
     * 设置配置
     * @param string $key 配置键名
     * @param array|string $value 配置内容 
     * @return bool 是否设置成功
     */
    public static function set($key, $value)
    {
        $config = ConfigModel::findOne(['key' => $key]);
        $content = is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value;
        if ($config) {
            $config->content = $content;
            $config->save();
        } else {
            Log::add("配置" . $key . "未始化", 'error');
        }
        return false;
    }
}
