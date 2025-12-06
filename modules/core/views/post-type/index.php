<?php

use app\modules\core\models\PostType;
use yii\helpers\Html;
use yii\helpers\Url;
use app\modules\core\classes\ActionColumn; 
use app\modules\core\classes\GridView;

$this->title = Yii::t('app', '文章分类');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '文章分类'), 'url' => ['index']];
$this->params['breadcrumbs'][] = [
    'label' => " ",
    'url' => ['create'],
    'class' => 'fas fa-circle-plus fa-blue',
];
?>
<div class="post-type-index">
 

    <?php // echo $this->render('_search', ['model' => $searchModel]); 
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'], 
            'name',
            'sort', 
            //'updated_at',
            GridView::langColumn($searchModel),
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, PostType $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                },
                'template' => '{view} {update} {delete}',
                'options' => ['width' => '123px'],
                'header' => Yii::t('app', '操作'),
            ],
        ],
    ]); ?>


</div>