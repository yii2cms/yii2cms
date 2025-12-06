<?php

use yii\helpers\Html; 

$this->title = Yii::t('app', '更新用户'); 
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['update', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-update">
 
    <?= $this->render('_form', [
        'model' => $model,
        'acl_value'=>$acl_value,
    ]) ?>

</div>
