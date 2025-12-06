<?php
/**
 * 顶层级下拉框 
 * 字段： title parent_id 
 */

namespace app\modules\core\trait;

use yii\helpers\ArrayHelper;

trait TopSelect
{
    public static function getTopSelect()
    {
        $all = self::find()->where(['parent_id' => 0])->all();
        if (!$all) {
            return [];
        }
        return ArrayHelper::map($all, 'id', 'title');
    }
}
