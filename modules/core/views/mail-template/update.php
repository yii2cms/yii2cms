<?php

use yii\helpers\Html; 

$this->title = Yii::t('app', '更新邮件模板'); 
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '邮件模板'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mail-template-update">
 

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
