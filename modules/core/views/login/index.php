<?php

use yii\helpers\Html;

$this->title = Yii::t('app', '登录');
?>

<div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center">
    <div class="login-card">
        <div class="login-title">
            <span><?= Yii::t('app', '登录') ?></span>
        </div>
        <div class="layui-tab" lay-filter="loginTab">
            <ul class="layui-tab-title">
                <li class="<?= $login_type === 'account' ? 'layui-this' : '' ?>" lay-id="account"><?= Yii::t('app', '账号登录') ?></li>
                <li class="<?= $login_type === 'email' ? 'layui-this' : '' ?>" lay-id="email"><?= Yii::t('app', '邮箱登录') ?></li>
                <li class="<?= $login_type === 'phone' ? 'layui-this' : '' ?>" lay-id="phone"><?= Yii::t('app', '手机登录') ?></li>
            </ul>
            <div class="layui-tab-content">
                <div class="layui-tab-item <?= $login_type === 'account' ? 'layui-show' : '' ?>">
                    <?= $this->render('_account') ?>
                </div>
                <div class="layui-tab-item <?= $login_type === 'email' ? 'layui-show' : '' ?>">
                    <?= $this->render('_email') ?>
                </div>
                <div class="layui-tab-item <?= $login_type === 'phone' ? 'layui-show' : '' ?>">
                    <?= $this->render('_phone') ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs(
    <<<JS
layui.use('element', function(){
  var element = layui.element;
  element.on('tab(loginTab)', function(data){
    var id = this.getAttribute('lay-id') || 'account';
    var url = new URL(window.location.href);
    url.searchParams.set('tab', id);
    window.history.replaceState({}, '', url.toString());
  });
});
JS
);
?>

<?php
$this->registerCss(<<<CSS
.login-card{width:420px;max-width:92vw;background:#fff;border-radius:12px;box-shadow:0 8px 24px rgba(0,0,0,.08);overflow:hidden}
.login-title{padding:16px 20px;border-bottom:1px solid #f2f2f2;font-size:16px;font-weight:600}
.layui-tab-title{padding:0 12px}
.layui-tab-title li{min-width:100px}
.layui-tab-content{padding:16px}
body{background:linear-gradient(135deg,#f5f7fb 0%,#eef2f7 100%)}
.form-label{display:block;margin-bottom:6px;color:#666}
.input-group .layui-input{border-top-right:0;border-bottom-right:0}
.input-group .layui-btn{border-top-left:0;border-bottom-left:0}
.input-group{display:flex;align-items:center}
.input-group .layui-input{flex:1}
.captcha-group{display:flex;align-items:center}
.captcha-group .layui-input{flex:1;border-top-right:0;border-bottom-right:0;border-radius:0}
.captcha-group img{height:38px;border:1px solid #e6e6e6;border-radius:0;margin-left:0;border-left:0}
CSS);
?>