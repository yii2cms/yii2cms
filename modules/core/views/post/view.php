<?php

use app\modules\core\content\View;

$this->title = Yii::t('app','查看');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '文章'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$view = new View();
$view->model = $model;
$view->attributes = [
    'id',
    'name',
    'content:ntext',
    'type_id',
    'status',
];
echo $view->getContent();
