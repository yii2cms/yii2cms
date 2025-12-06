<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = $model->name;
\yii\web\YiiAsset::register($this);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '邮件模板'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mail-template-view">
 

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
            'name',
            'key',
            'content:ntext',
        ],
    ]) ?>

</div>