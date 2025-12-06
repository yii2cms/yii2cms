<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = Yii::t('app', '修改邮箱');
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
                            <i class="fas fa-envelope me-2"></i>
                            <?= Html::encode($this->title) ?>
                        </h3>
                    </div>

                    <div class="card-body">
                        <?php $form = ActiveForm::begin([
                            'id' => 'update-email-form',
                            'options' => ['class' => 'form-horizontal'],
                        ]); ?>
                        <div class="form-group">
                            <?= $form->field($model, 'email')->textInput([
                                'placeholder' => Yii::t('app', '请输入当前邮箱'),
                                'class' => 'form-control',
                                'disabled' => true
                            ])->label(Yii::t('app', '当前邮箱')) ?>
                        </div>

                        <div class="form-group">
                            <?= $form->field($model, 'email_code')->textInput([
                                'placeholder' => Yii::t('app', '请输入当前邮箱验证码'),
                                'class' => 'form-control'
                            ])->label(Yii::t('app', '当前邮箱验证码')) ?>
                            <div class="text-end mt-2">
                                <button type="button" class="btn btn-sm btn-outline-primary" id="send-current-email-code">
                                    <?= Yii::t('app', '获取验证码') ?>
                                </button>
                            </div>
                        </div>

                        <div class="form-group">
                            <?= $form->field($model, 'new_email')->textInput([
                                'placeholder' => Yii::t('app', '请输入新邮箱'),
                                'class' => 'form-control'
                            ])->label(Yii::t('app', '新邮箱')) ?>
                        </div>

                        <div class="form-group">
                            <?= $form->field($model, 'new_email_code')->textInput([
                                'placeholder' => Yii::t('app', '请输入新邮箱验证码'),
                                'class' => 'form-control'
                            ])->label(Yii::t('app', '新邮箱验证码')) ?>
                            <div class="text-end mt-2">
                                <button type="button" class="btn btn-sm btn-outline-primary" id="send-new-email-code">
                                    <?= Yii::t('app', '获取验证码') ?>
                                </button>
                            </div>
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

<?php
$js = <<<JS
// 发送当前邮箱验证码
$('#send-current-email-code').click(function() {
    var btn = $(this);
    btn.prop('disabled', true);
    
    $.post('/core/user-setting/send-email-code', {type: 'current'}, function(response) {
        if (response.code == 0) {
            layer.msg('验证码已发送至当前邮箱', {icon: 1});
            var countdown = 60;
            var timer = setInterval(function() {
                if (countdown <= 0) {
                    clearInterval(timer);
                    btn.text('获取验证码').prop('disabled', false);
                } else {
                    btn.text(countdown + '秒后重新获取');
                    countdown--;
                }
            }, 1000);
        } else {
            layer.msg(response.msg || '发送失败', {icon: 2});
            btn.prop('disabled', false);
        }
    }).fail(function() {
        layer.msg('请求失败，请重试', {icon: 2});
        btn.prop('disabled', false);
    });
});

// 发送新邮箱验证码
$('#send-new-email-code').click(function() {
    var btn = $(this);
    var newEmail = $('#user-new_email').val();
    
    if (!newEmail) {
        layer.msg('请先输入新邮箱', {icon: 2});
        return;
    }
    
    if (!/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/.test(newEmail)) {
        layer.msg('请输入有效的邮箱地址', {icon: 2});
        return;
    }
    
    btn.prop('disabled', true);
    
    $.post('/core/user-setting/send-email-code', {type: 'new', email: newEmail}, function(response) {
        if (response.code == 0) {
            layer.msg('验证码已发送至新邮箱', {icon: 1});
            var countdown = 60;
            var timer = setInterval(function() {
                if (countdown <= 0) {
                    clearInterval(timer);
                    btn.text('获取验证码').prop('disabled', false);
                } else {
                    btn.text(countdown + '秒后重新获取');
                    countdown--;
                }
            }, 1000);
        } else {
            layer.msg(response.msg || '发送失败', {icon: 2});
            btn.prop('disabled', false);
        }
    }).fail(function() {
        layer.msg('请求失败，请重试', {icon: 2});
        btn.prop('disabled', false);
    });
});
JS;
$this->registerJs($js);
?>