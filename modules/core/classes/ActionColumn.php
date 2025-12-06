<?php

/**
 * 授权请查看根目录下的LICENSE.md
 * @author ken <yiiphp@foxmail.com> 
 * @link   https://github.com/yii2cms/yii2cms 
 */

namespace app\modules\core\classes;

use yii\helpers\Url;

/**
 * yii\grid\ActionColumn
 */
class ActionColumn extends \yii\grid\ActionColumn
{
    /**
     * update的url记住query
     * @param string $action 操作名称
     * @param mixed $model 模型
     * @param mixed $key 主键值
     * @param int $index 行索引
     * @return string 生成的URL
     */
    public function createUrl($action, $model, $key, $index)
    {
        $params = is_array($key) ? $key : ['id' => (string) $key];
        $params[0] = $this->controller ? $this->controller . '/' . $action : $action;
        return Url::toRoute(array_merge(\Yii::$app->request->queryParams, $params));
    }
}
