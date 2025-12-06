<?php

use yii\helpers\Html;

$this->title = Yii::t('app', '创建用户');
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['create']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-create">

    <?= $this->render('_form', [
        'model' => $model,
        'acl_value' => $acl_value,
    ]) ?>

</div>