<?php

namespace app\modules\core\widgets;

use Yii;

class Editor extends \yii\base\Widget
{
    /**
     * 模型
     */
    public $model;
    /**
     * 模型属性
     */
    public $attribute;
    /**
     * 工具栏
     */
    public $toolbar;
    /**
     * 工具栏配置
     */
    public $toolbarConfig = [
        'sample' => [
            'undo',
            'redo',
            '|',
            'bold',
            'italic',
            '|',
            'fontColor',
            'fontBackgroundColor',

        ],
        'default' => [
            'undo',
            'redo',
            '|',
            'bold',
            'italic',
            '|',
            'fontColor',
            'fontBackgroundColor',
            'insertImage',
            'insertTable',
        ],
    ];
    /**
     * 工具栏类型
     */
    public $toolbarType = 'default';

    public function run()
    {
        $this->view->registerJsFile('@web/lib/ckeditor5.uploader.js');
        $this->view->registerCssFile('@web/lib/ckeditor5/ckeditor5-editor.css');
        $this->toolbar = $this->toolbar ?: $this->toolbarConfig[$this->toolbarType];
        $model = $this->model;
        $attribute = $this->attribute;
        $value = $model->$attribute;
        //取model 名称，不包含namespace
        $modelName = basename(str_replace('\\', '/', get_class($model)));
        /**
         * 编辑器id
         */
        $attributeId = $attribute;
        if (strpos($attributeId, '[') !== false) {
            $attributeId = str_replace("[", "", $attributeId);
            $attributeId = str_replace("]", "", $attributeId);
        }
        $attributeId = $this->id ?: $attributeId;
        $attributeId = str_replace("-", "_", $attributeId);

        return $this->render('editor', [
            'model'     => $model,
            'attribute' => $attribute,
            'modelName' => $modelName,

            'toolbar'   => $this->toolbar,
            'language'  => Yii::$app->language,
            'value'     => $value,
            'attributeId' => $attributeId,
        ]);
    }
}
