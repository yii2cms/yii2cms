<?php

use yii\helpers\Html; 

$this->title = Yii::t('app', '创建'); 
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '文章'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="post-create">

    <h1><?= Yii::t('app', '创建') ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
