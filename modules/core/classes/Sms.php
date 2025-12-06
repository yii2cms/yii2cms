<?php

/**
 * 授权请查看根目录下的LICENSE.md
 * @author ken <yiiphp@foxmail.com> 
 * @link   https://github.com/yii2cms/yii2cms 
 */

namespace app\modules\core\classes;

use app\modules\core\models\SmsTemplate;

/**
 * 短信助手类
 */
class Sms
{
    /**
     * 初始化短信模板
     * @param string $name 短信模板名称
     * @param string $key 短信模板key
     * @param string $content 短信模板内容
     */
    public static function init($name = '登录', $key = 'login', $content = '')
    {
        $types = self::getDriveTypes();
        foreach ($types as $type => $name) {
            // 检查模板是否已存在
            $model = SmsTemplate::findOne(['key' => $key, 'type' => $type]);
            if ($model) {
                continue;
            }
            $model = new SmsTemplate();
            $model->name = $name;
            $model->key = $key;
            $model->content = $content;
            $model->type = $type;
            $model->save();
        }
    }
    /**
     * 发送模板短信
     * @param string $phone 接收短信的手机号
     * @param string $key 短信模板的key
     * @param array $params 短信模板中的参数
     * @return bool 是否发送成功
     */
    public static function send($phone, $key, $params = [])
    {
        $type = get_config('sms.default.drive') ?: 'Default';
        $model = SmsTemplate::findOne(['key' => $key, 'type' => $type]);
        if (!$model) {
            Log::add("短信模板 $key 不存在", 'error', 'sms');
            return false;
        }
        $content = $model->content;
        if (!$content) {
            Log::add('短信模板内容为空', 'error', 'sms');
            return false;
        }
        $template_id = $key;
        $drive = self::getDrive($type);
        /**
         * content移除所有html代码，并trim
         */
        $content = trim(strip_tags($content));
        return $drive::send($phone, $template_id, $content, $params);
    }

    /**
     * 获取短信驱动
     */
    public static function getDrive($name)
    {
        $class = "\\app\\modules\\core\\classes\\sms\\" . $name . "Drive";
        return $class;
    }

    /**
     * 获取所有驱动类型
     * @return array 驱动类型数组 [类名 => 显示名称]
     */
    public static function getDriveTypes()
    {
        $types = [];

        // 默认驱动
        $types['Default'] = '默认驱动';

        // 查找lib目录下的所有Drive文件
        $libPath = __DIR__ . '/sms/';
        $files = scandir($libPath);

        foreach ($files as $file) {
            if (strpos($file, 'Drive.php') !== false) {
                $className = str_replace('Drive.php', '', $file);
                $filePath = $libPath . '/' . $file;

                // 读取文件内容
                $content = file_get_contents($filePath);

                // 提取第一个注释
                if (preg_match('/\/\*\*\s*\n\s*\*\s*(.+?)\s*\n/s', $content, $matches)) {
                    $comment = trim($matches[1]);
                    $types[$className] = $comment;
                } else {
                    $types[$className] = $className . '驱动';
                }
            }
        }

        return $types;
    }
}
