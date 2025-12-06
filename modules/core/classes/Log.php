<?php

/**
 * 授权请查看根目录下的LICENSE.md
 * @author ken <yiiphp@foxmail.com> 
 * @link   https://github.com/yii2cms/yii2cms 
 */

namespace app\modules\core\classes;

use Yii;

/**
 * 日志助手类
 */
class Log
{
    /**
     * 添加日志
     * @param string $content 日志内容
     * @param string $type 日志类型
     * @param string $app_user 应用用户 可选，默认'user'
     */
    public static function add($content, $type = 'info', $app_user = 'user')
    {
        $log = new \app\modules\core\models\Log();
        $log->content = $content;
        $log->type   = $type;
        $log->user_id = Yii::$app->$app_user->id ?? 0;
        $log->ip = Yii::$app->request->userIP ?? '';
        $log->agent = Yii::$app->request->userAgent ?? '';
        $log->save();
    }

    /**
     * 管理员日志
     * @param string $content 日志内容
     * @param string $type 日志类型 可选，默认'info'
     */
    public static function admin($content, $type = 'info')
    {
        self::add($content, $type, 'admin');
    }
}
