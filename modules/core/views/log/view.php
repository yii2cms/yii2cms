<?php

use yii\helpers\Html;
use yii\widgets\DetailView;  
use app\modules\core\classes\Url;

$this->title = $model->id;
\yii\web\YiiAsset::register($this);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '日志'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="log-view">
 

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'contentRaw:ntext', 
            [
                'attribute' => 'type', 
                'value' => function ($model) {
                    return  "<span class='text-" . $model->typeColor . "'>" . $model->typeLabel . "</span>";
                },  
                'format' => 'raw',
            ],
            [
                'attribute' => 'word',
                'value' => function ($model) {
                    return $model->user->name ?? '';
                }, 
            ],
            'ip',
            'agent',
        ],
    ]) ?>
    <div class="text-center">
         <a href="<?= Url::create(['index']) ?>" class="btn btn-primary" ><?= Yii::t('app', '返回') ?></a>
    </div>
</div>
