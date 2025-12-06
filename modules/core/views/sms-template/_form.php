<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
 
?>

<div class="sms-template-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>


    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?php if ($model->isNewRecord) { ?>
        <?= $form->field($model, 'key')->textInput(['maxlength' => true]) ?>
    <?php } else {
        echo $form->field($model, 'key')->textInput(['maxlength' => true, 'disabled' => true]);
    } ?>

    <?= $form->field($model, 'content')->textarea(['rows' => 6]) ?>

    <?php if ($model->isNewRecord) { ?>
        <?= $form->field($model, 'type')->dropDownList($model->getDropdownType(), ['maxlength' => true]) ?>
    <?php } else {
        echo $form->field($model, 'type')->dropDownList($model->getDropdownType(), ['maxlength' => true, 'disabled' => true]);
    } ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', '保存'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php 
require_once __DIR__ . '/help.php';
?>
