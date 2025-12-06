<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = $model->id;
\yii\web\YiiAsset::register($this);
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">
 

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'label' => Yii::t('app', '头像'),
                'value' => Html::img($model->avatar, ['style' => 'width: 100px;']),
                'format' => 'raw',
            ],
            'nickname',
            'username',
            'email:email',
            'phone',
            'role',

            [
                'label' => Yii::t('app', '创建时间'),
                'value' => Yii::$app->formatter->asDatetime($model->created_at),
            ],
        ],
    ]) ?>

    <?= \app\modules\core\widgets\DataHis::widget([
        'model' => $model,
    ]) ?>
    
</div>