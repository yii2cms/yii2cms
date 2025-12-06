<?php

/**
 * 数据操作记录
 */

namespace app\modules\core\widgets;

use yii\base\Widget;

class DataHis extends Widget
{
    public $model;
    public function run()
    {
        return $this->render('data-his', [
            'model' => $this->model,
        ]);
    }
}
