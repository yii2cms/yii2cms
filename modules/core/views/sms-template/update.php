<?php

use yii\helpers\Html; 

$this->title = Yii::t('app', '更新'); 
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '短信模板'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sms-template-update"> 

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
