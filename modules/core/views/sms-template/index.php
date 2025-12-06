<?php

use app\modules\core\models\SmsTemplate;
use yii\helpers\Html;
use yii\helpers\Url;
use app\modules\core\classes\ActionColumn;
use \app\modules\core\classes\GridView; 

$this->title = Yii::t('app', '短信模板');
$this->params['breadcrumbs'][] =  ['label' => Yii::t('app', '短信模板'), 'url' => ['index']];
$this->params['breadcrumbs'][] = [
    'label' => " ",
    'url' => ['create'],
    'class' => 'fas fa-circle-plus fa-blue',
];
?>
<div class="sms-template-index"> 

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'], 
            'name',
            'key',
            'content:ntext',
            [
                'attribute' => 'type',
                'value' => function(SmsTemplate $model){
                    return $model->getDisplayType();
                },
                'filter' => $searchModel->getDropdownType(),
            ],
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, SmsTemplate $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                },
                'template' => '{view} {update}',
                'options' => ['width' => '100px'],
                'header' => Yii::t('app', '操作'),
            ],
        ],
    ]); ?>


</div>

<?php 
require_once __DIR__ . '/help.php';
?>
