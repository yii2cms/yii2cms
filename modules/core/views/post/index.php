<?php

use app\modules\core\content\Grid;
use app\modules\core\classes\Breadcrumbs;
use app\modules\core\classes\GridView;

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '文章'), 'url' => ['index']];
$this->params['breadcrumbs'][] = [
    'label' => " ",
    'url' => ['create'],
    'class' => 'fas fa-circle-plus fa-blue',
];

$grid = new Grid();
$grid->columns = [
    ['class' => 'yii\grid\SerialColumn'],
    'name',
    [
        'attribute' => 'type_id',
        'value' => function ($model) {
            return $model->type->name ?? '';
        },
    ],
    GridView::langColumn($searchModel),
    [
        'call' => 'headerStatus'
    ],
    [
        'call' => 'headerCreatedAt'
    ],
];
$grid->dataProvider = $dataProvider;
$grid->searchModel  = $searchModel;
echo $grid->getContent();
