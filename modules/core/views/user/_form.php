<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\core\widgets\Image;
use app\modules\core\widgets\AclTable;
use app\modules\core\classes\Acl;

$data = Acl::getAll();

?>

<div class="user-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
    <?= $form->field($model, 'avatar')->widget(Image::class, []) ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'role')->dropDownList($model->getRoleOptions(), ['prompt' => Yii::t('app', '请选择角色')]) ?>

    <?php
    if ($model->isNewRecord) {
        echo $form->field($model, 'password')->passwordInput(['maxlength' => true]);
    }
    ?>

    <?php if ($model->role == 'admin') { ?>
        <?= $form->field($model, 'acl')->widget(AclTable::class, [
            'data' => $data,
            'value' => $acl_value,
            'columns' => [
                ['key' => 'name', 'label' => Yii::t('app', '名称'), 'class' => 'text-start'],
                ['key' => 'description', 'label' => Yii::t('app', '描述'), 'format' => 'text'],
            ],
            'enableSelection' => true,
            'selectionLabel' => Yii::t('app', '选择'),
            'selectionField' => 'userAcls',
        ]) ?>
    <?php } ?>



    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', '保存'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>