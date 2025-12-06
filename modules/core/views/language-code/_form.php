<?php

use yii\helpers\Html;
use app\modules\core\classes\ActiveForm;
?>

<div class="language-code-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
    
    
    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'badge')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'sort')->textInput() ?>

    <?= $form->field($model, 'is_default')->dropDownList([0 => Yii::t('app', '否'), 1 => Yii::t('app', '是')]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app','保存'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php include __DIR__.'/help.php';?>