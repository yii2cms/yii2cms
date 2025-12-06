<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = Yii::t('app', '修改手机号');
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
                            <i class="fas fa-mobile-alt me-2"></i>
                            <?= Html::encode($this->title) ?>
                        </h3>
                    </div>

                    <div class="card-body">
                        <?php $form = ActiveForm::begin([
                            'id' => 'update-phone-form',
                            'options' => ['class' => 'form-horizontal'],
                        ]); ?>

                        <div class="form-group">
                            <?= $form->field($model, 'phone')->textInput([
                                'placeholder' => Yii::t('app', '请输入当前手机号'),
                                'class' => 'form-control',
                                'disabled' => true
                            ])->label(Yii::t('app', '当前手机号')) ?>
                        </div>

                        <div class="form-group">
                            <?= $form->field($model, 'phone_code')->textInput([
                                'placeholder' => Yii::t('app', '请输入当前手机验证码'),
                                'class' => 'form-control'
                            ])->label(Yii::t('app', '当前手机验证码')) ?>   
                            <div class="text-end mt-2">
                                <button type="button" class="btn btn-sm btn-outline-primary" id="send-current-phone-code">
                                    <?= Yii::t('app', '获取验证码') ?>
                                </button>
                            </div>
                        </div>

                        <div class="form-group">
                            <?= $form->field($model, 'new_phone')->textInput([
                                'placeholder' => Yii::t('app', '请输入新手机号'),
                                'class' => 'form-control'
                            ])->label(Yii::t('app', '新手机号')) ?>
                        </div>

                        <div class="form-group">
                            <?= $form->field($model, 'new_phone_code')->textInput([
                                'placeholder' => Yii::t('app', '请输入新手机验证码'),
                                'class' => 'form-control'
                            ])->label(Yii::t('app', '新手机验证码')) ?>
                            <div class="text-end mt-2">
                                <button type="button" class="btn btn-sm btn-outline-primary" id="send-new-phone-code">
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
// 发送当前手机验证码
$('#send-current-phone-code').click(function() {
    var btn = $(this);
    btn.prop('disabled', true);
    
    $.post('/core/user-setting/send-phone-code', {type: 'current'}, function(response) {
        if (response.code == 0) {
            layer.msg('验证码已发送至当前手机', {icon: 1});
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

// 发送新手机验证码
$('#send-new-phone-code').click(function() {
    var btn = $(this);
    var newPhone = $('#user-new_phone').val();
    
    if (!newPhone) {
        layer.msg('请先输入新手机号', {icon: 2});
        return;
    }
    
    if (!/^1[3-9]\d{9}$/.test(newPhone)) {
        layer.msg('请输入有效的手机号码', {icon: 2});
        return;
    }
    
    btn.prop('disabled', true);
    
    $.post('/core/user-setting/send-phone-code', {type: 'new', phone: newPhone}, function(response) {
        if (response.code == 0) {
            layer.msg('验证码已发送至新手机', {icon: 1});
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