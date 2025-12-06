<?php

use app\modules\core\models\Log;
use yii\helpers\Html;
use yii\helpers\Url;
use app\modules\core\classes\ActionColumn;
use yii\helpers\StringHelper;
use app\modules\core\classes\GridView;

$this->registerJs(
    <<<JS
// 初始化 tooltip
$(function () {
    $('[data-toggle="tooltip"]').tooltip({
        html: true,
        boundary: 'window'
    });
});
JS
);

$this->title = Yii::t('app', '日志');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="log-index"> 
    <?php // echo $this->render('_search', ['model' => $searchModel]); 
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'contentRaw',
                'format' => 'raw',
                'contentOptions' => ['class' => 'text-truncate-cell'],
                'value' => function ($model) {
                    return  "<a href='/core/log/view?id=".$model->id."' >".StringHelper::truncate(Html::encode($model->contentRaw), 100)."</a>";
                }
            ],
            [
                'attribute' => 'type',
                'filter' =>  $searchModel->getTypeList(),
                'value' => function ($model) {
                    return  "<span class='text-" . $model->typeColor . "'>" . $model->typeLabel . "</span>";
                },
                'options' => ['width' => '130'],
                'format' => 'raw',
            ],
            [
                'attribute' => 'word',
                'value' => function ($model) {
                    return $model->user->name ?? '';
                },
                'options' => ['width' => '130'],
                //搜索placeholder
                'filter' => Html::activeTextInput($searchModel, 'word', ['placeholder' => '邮件或手机号']),
            ],
            GridView::headerCreatedAt($searchModel),
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Log $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                },
                'template' => '{view}',
                'options' => ['width' => '80'],
                'header' => Yii::t('app', '操作'),
            ],
        ],
    ]); ?>


</div>