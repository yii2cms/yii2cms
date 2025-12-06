<?php

use yii\helpers\Html;
use app\modules\core\classes\ActiveForm;
?>

<div class="post-type-form">

    <?php $form = ActiveForm::begin(); ?>
    
    
    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'sort')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app','保存'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
