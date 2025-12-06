<?php

use yii\helpers\Html; 

$this->title = Yii::t('app', '创建'); 
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '翻译'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="language-t-create">
 

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
