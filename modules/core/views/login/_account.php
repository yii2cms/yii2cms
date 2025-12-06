 <?php

    use yii\helpers\Html;
    ?>
 <form method="post" action="/core/login/index">
     <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
     <input type="hidden" name="login_type" value="account">
     <div class="mb-3">
         <label for="username" class="form-label"><?= Yii::t('app', '用户名') ?></label>
         <input type="text" value="<?= $username ?? '' ?>" class="form-control" name="username"
             placeholder="<?= Yii::t('app', '请输入用户名') ?>" required>
     </div>

     <?= $this->render('image_captcha', ['id' => 'account']) ?>

     <div class="mb-3">
         <label for="password" class="form-label"><?= Yii::t('app', '密码') ?></label>
         <input type="password" value="<?= $password ?? '' ?>" class="form-control" name="password"
             placeholder="<?= Yii::t('app', '请输入密码') ?>" required>
     </div>

     <div class="d-grid mb-3">
         <button type="submit" class="btn btn-primary"><?= Yii::t('app', '登录') ?></button>
     </div>
 </form>