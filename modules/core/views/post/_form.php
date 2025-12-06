<?php

use app\modules\core\content\Form;

$action_name = $model->isNewRecord ? Yii::t('app', '创建') : Yii::t('app', '更新');

$form = new Form();
$form->open($model);
$form->fields = [
    [
        'name' => 'name',
        'type' => 'textInput',
    ],
    [
        'name' => 'image',
        'type' => 'image',
    ],
    [
        'name' => 'images',
        'type' => 'images',
    ],
    [
        'name' => 'type_id',
        'type' => 'dropDownList',
        'options' => $model->getTypeList(),
    ],
    [
        'name' => 'content',
        'type' => 'editor',
    ],
    [
        'name' => 'status',
        'type' => 'dropDownList',
        'options' => $model->getStatusList(),
    ],
    [
        'name' =>  $action_name,
        'type' => 'submitButton',
        'options' => ['class' => 'btn btn-primary'],
    ]
];

$form->getContent();
$form->outputFields();
$form->close();
