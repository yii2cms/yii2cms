<?php

use yii\helpers\Html; 

$this->title = Yii::t('app', '创建邮件模板'); 
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '邮件模板'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mail-template-create"> 

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
