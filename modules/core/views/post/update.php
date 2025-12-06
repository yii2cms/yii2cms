<?php

use yii\helpers\Html; 

$this->title = Yii::t('app', '更新'); 
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '文章'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="post-update">

    <h1><?= Yii::t('app', '更新') ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
