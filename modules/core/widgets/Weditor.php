<?php

namespace app\modules\core\widgets;

use Yii;

class Weditor extends \yii\base\Widget
{
    public $model;
    public $name;
    public $attribute;
    public $toolbar;
    public $value;
    public $id;
    public $fullName;
    public $toolbarConfig = [
        'sample' => [
           'bold', 'italic','color','bgColor'
        ],
        'default' => [
           'bold', 'italic','color','bgColor','fontSize','underline','uploadImage'
        ],
    ];
    public $toolbarType = 'sample';

    public function run()
    {
        $this->view->registerJsFile('https://unpkg.com/@wangeditor/editor@latest/dist/index.js');
        $this->view->registerCssFile('https://unpkg.com/@wangeditor/editor@latest/dist/css/style.css');
        $this->toolbar = $this->toolbar ?: $this->toolbarConfig[$this->toolbarType];
        $model = $this->model;
        $attribute = $this->attribute;
        if (!isset($this->value)) {
            $this->value = $model->$attribute;
        }
        $attributeId = $attribute;
        if (strpos($attributeId, '[') !== false) {
            $attributeId = str_replace("[", "", $attributeId);
            $attributeId = str_replace("]", "", $attributeId);
        }
        $attributeId = $this->id ?: $attributeId;
        $attributeId = str_replace("-", "_", $attributeId);
        return $this->render('weditor', [
            'model' => $model,
            'attribute' => $attribute,
            'name' => $this->name,
            'toolbar' => $this->toolbar,
            'language' => Yii::$app->language,
            'value' => $this->value,
            'attributeId' => $attributeId,
            'fullName' => $this->fullName,
        ]);
    }
}
