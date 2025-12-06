<?php

use app\modules\core\models\LanguageT;
use yii\helpers\Html;
use yii\helpers\Url;
use app\modules\core\classes\ActionColumn;
use \app\modules\core\classes\GridView;



$this->title = Yii::t('app', '翻译');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="language-t-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php // echo $this->render('_search', ['model' => $searchModel]); 
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'code',
            'key',
            'value',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, LanguageT $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                },
                'template' => '{update}',
                'header' => Yii::t('app', '操作'),
                'options' => ['width' => '80'],
            ],
        ],
    ]); ?>


</div>