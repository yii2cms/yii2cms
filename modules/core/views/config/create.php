<?php

use yii\helpers\Html; 

$this->title = Yii::t('app', '创建'); 
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '配置'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="config-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
