<?php

use yii\captcha\Captcha;

$captcha_code = Yii::$app->request->post('captcha_code') ?? '';

?>
<div class="mb-3">
    <label class="form-label"><?= Yii::t('app', '图形验证码') ?></label>
    <?= Captcha::widget([
        'name' => 'captcha_code_' . $id,
        'id'   => 'captcha_code_' . $id,
        'template' => '
            <div class="captcha-group">
                {input}
                {image}
            </div>
        ',
        'options' => [
            'class' => 'layui-input',
            'placeholder' => Yii::t('app', '请输入验证码'),
            'autocomplete' => 'off'
        ],
        'imageOptions' => [
            'id' => 'captcha-' . $id,
            'style' => 'cursor: pointer; height: 38px;',
            'title' => Yii::t('app', '点击刷新验证码'),
        ],
        'captchaAction' => '/core/login/captcha'
    ]) ?>
</div>