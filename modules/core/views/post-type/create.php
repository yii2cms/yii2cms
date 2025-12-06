<?php

use yii\helpers\Html;

$this->title = Yii::t('app', '创建');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '文章分类'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="post-type-create">

    <h1><?= Yii::t('app', '创建') ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>