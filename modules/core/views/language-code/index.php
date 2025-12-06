<?php

use app\modules\core\models\LanguageCode;
use yii\helpers\Html;
use yii\helpers\Url;
use app\modules\core\classes\ActionColumn;
use \app\modules\core\classes\GridView;
$this->title = Yii::t('app', '语言代码');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '语言代码'), 'url' => ['index']];
$this->params['breadcrumbs'][] = [
    'label' => " ",
    'url' => ['create'],
    'class' => 'fas fa-circle-plus fa-blue',
];
?>
<div class="language-code-index">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'name',
            'code',
            'sort',
            [
                'attribute' => 'is_default',
                'filter' => false,
                'value' => function ($model) {
                    return $model->is_default ? '<span class="text-success">' . Yii::t('app', '是') . '</span>' : '<span class="label label-default">' . Yii::t('app', '否') . '</span>';
                },
                'format' => 'raw',
            ],
            [
                'label' => Yii::t('app', '翻译'),
                'filter' => false,
                'value' => function ($model) {
                    if ($model->is_default) {
                        return Html::a($model->name, ['/core/language-t/index', 'code' => $model->code, 'is_default' => 1]);
                    } else {
                        return Html::a($model->name, ['/core/language-t/index', 'code' => $model->code, 'is_default' => 0]);
                    }
                },
                'format' => 'raw',
                'contentOptions' => ['style' => 'width: 80px;'],
            ],
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, LanguageCode $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                },
                'header' => Yii::t('app', '操作'), 
            ],
        ],
    ]); ?>


</div>
<?php include __DIR__.'/help.php';?>