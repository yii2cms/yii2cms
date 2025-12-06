<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\core\widgets\Editor; 
?>

<div class="mail-template-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>


    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?php if ($model->isNewRecord) { ?>
        <?= $form->field($model, 'key')->textInput(['maxlength' => true]) ?>
    <?php } else { ?>
        <?= $form->field($model, 'key')->textInput(['maxlength' => true, 'disabled' => true]) ?>
    <?php } ?>
    
    <?= $form->field($model, 'content')->textarea(['rows' => 6])->widget(Editor::className(), []) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', '保存'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php 
require_once __DIR__ . '/help.php';
?>
