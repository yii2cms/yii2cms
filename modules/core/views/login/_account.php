<?php

use yii\helpers\Html;
?>
<form method="post" action="/core/login/index" class="layui-form" style="max-width: 420px;">
    <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
    <input type="hidden" name="login_type" value="account">
    <div class="mb-3">
        <label for="username" class="form-label"><?= Yii::t('app', '用户名') ?></label>
        <input type="text" value="<?= $username ?? '' ?>" class="layui-input" name="username"
            placeholder="<?= Yii::t('app', '请输入用户名') ?>" required>
    </div>

    <?= $this->render('image_captcha', ['id' => 'account']) ?>

    <div class="mb-3">
        <label for="password" class="form-label"><?= Yii::t('app', '密码') ?></label>
        <input type="password" value="<?= $password ?? '' ?>" class="layui-input" name="password"
            placeholder="<?= Yii::t('app', '请输入密码') ?>" required>
    </div>

    <div class="mb-3">
        <button type="submit" class="layui-btn layui-bg-blue" style="width:100%;"><?= Yii::t('app', '登录') ?></button>
    </div>
</form>