<?php

use yii\helpers\Html;
use yii\widgets\DetailView;  

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '文章分类'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;  

\yii\web\YiiAsset::register($this);
?>
<div class="post-type-view">
 
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
            'sort',
            'delete_at',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
