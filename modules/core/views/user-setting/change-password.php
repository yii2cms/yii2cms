<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = Yii::t('app', '修改密码');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '用户信息'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="user-view">
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card user-card">
                    <div class="card-header user-card-header">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-lock me-2"></i>
                            <?= Html::encode($this->title) ?>
                        </h3>
                    </div>

                    <div class="card-body">
                        <?php $form = ActiveForm::begin([
                            'id' => 'change-password-form',
                            'options' => ['class' => 'form-horizontal'],
                        ]); ?>

                        <div class="form-group">
                            <?= $form->field($model, 'old_password')->passwordInput([
                                'placeholder' => Yii::t('app', '请输入当前密码'),
                                'class' => 'form-control'
                            ])->label(Yii::t('app', '当前密码')) ?>
                        </div>

                        <div class="form-group">
                            <?= $form->field($model, 'new_password')->passwordInput([
                                'placeholder' => Yii::t('app', '请输入新密码'),
                                'class' => 'form-control'
                            ])->label(Yii::t('app', '新密码')) ?>
                        </div>

                        <div class="form-group">
                            <?= $form->field($model, 'password_repeat')->passwordInput([
                                'placeholder' => Yii::t('app', '请再次输入新密码'),
                                'class' => 'form-control'
                            ])->label(Yii::t('app', '确认新密码')) ?>   
                        </div>

                        <div class="form-group text-center mt-4">
                            <?= Html::submitButton(Yii::t('app', '保存更改'), ['class' => 'btn btn-primary px-4']) ?>
                            <?= Html::a(Yii::t('app', '取消'), ['index'], ['class' => 'btn btn-outline-secondary ms-2']) ?>
                        </div>

                        <?php ActiveForm::end(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>