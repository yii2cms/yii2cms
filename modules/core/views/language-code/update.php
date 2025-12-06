<?php

use yii\helpers\Html; 

$this->title = Yii::t('app', '更新'); 
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '语言代码'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="language-code-update">


    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
