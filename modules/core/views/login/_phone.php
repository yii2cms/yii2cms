<?php

use yii\helpers\Html;
?>
<form method="post" action="/core/login/index">
    <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
    <input type="hidden" name="login_type" value="phone">
    <div class="mb-3">
        <label for="phone" class="form-label"><?= Yii::t('app', '手机号码') ?></label>
        <input type="tel" class="form-control" name="phone" id="phoneInput"
            value="<?= $phone ?? '' ?>" placeholder="<?= Yii::t('app', '请输入手机号码') ?>" required>
    </div>

    <?= $this->render('image_captcha', ['id' => 'phone']) ?>

    <div class="mb-3 input-group">
        <input type="text" value="<?= $phone_code ?? '' ?>" name="phone_code" class="form-control" placeholder="<?= Yii::t('app', '手机号码验证码') ?>" required>
        <button class="btn btn-outline-secondary" type="button" id="phoneCodeBtn">
            <?= Yii::t('app', '获取验证码') ?>
        </button>
    </div>

    <div class="d-grid mb-3">
        <button type="submit" class="btn btn-primary"><?= Yii::t('app', '登录') ?></button>
    </div>
</form>


<?php
// 验证码倒计时脚本
$this->registerJs(
    <<<JS
// 手机验证码
$('#phoneCodeBtn').click(function() {
    let btn = $(this);
    let phone = $('#phoneInput').val();
    let captcha_code = $('#captcha_code_phone').val();
    
    if (!phone) {
        layer.msg('请输入手机号码', {icon: 2});
        return;
    }
    layer.load();
    // 发送验证码请求
    $.post('/core/login/send-phone-code', {
        phone: phone,
        captcha_code_phone: captcha_code,
        _csrf: yii.getCsrfToken()
    }).done(function(res) {
        layer.closeAll('loading');
        if (res.code == 0) {
            layer.msg('验证码已发送，请查收', {icon: 1});
            
            // 开始倒计时
            let count = 60;
            btn.prop('disabled', true).text(count + '秒后重新获取');
            
            let timer = setInterval(function() {
                count--;
                btn.text(count + '秒后重新获取');
                
                if (count <= 0) {
                    clearInterval(timer);
                    btn.prop('disabled', false).text('获取验证码');
                }
            }, 1000);
        } else {
            layer.msg(res.message || '验证码发送失败', {icon: 2});
        }
    }).fail(function() {
        layer.msg('请求失败，请重试', {icon: 2});
    });
});
JS
);
