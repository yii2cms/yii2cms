<?php

use yii\helpers\Html;
use yii\widgets\DetailView;  

$this->title = $model->id;
\yii\web\YiiAsset::register($this);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '翻译'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="language-t-view">
 

    <p>
        <?= Html::a(Yii::t('app', '更新'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', '删除'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', '确定要删除吗？'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'code',
            'key',
            'value',
        ],
    ]) ?>

</div>
