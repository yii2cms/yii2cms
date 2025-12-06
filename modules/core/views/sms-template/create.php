<?php

use yii\helpers\Html; 

$this->title = Yii::t('app', '创建'); 
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '短信模板'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sms-template-create"> 

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
