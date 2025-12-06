<?php

use yii\helpers\Html;
use yii\jui\Tabs;
$this->title = Yii::t('app', '登录');   
?>

<div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center">
    <?= Tabs::widget([
        'items' => [
            [
                'label' => Yii::t('app', '账号登录'),
                'content' => $this->render('_account'),
                'active' => $login_type == 'account',
            ],
            [
                'label' => Yii::t('app', '邮箱登录'),
                'content' => $this->render('_email'),
                'active' => $login_type == 'email',
            ],
            [
                'label' => Yii::t('app', '手机登录'),
                'content' => $this->render('_phone'),
                'active' => $login_type == 'phone',
            ],
        ],
    ]); ?>
    
</div>
