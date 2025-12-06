<?php

use yii\helpers\Html;
use app\modules\core\classes\GridView;

$this->title = Yii::t('app', '用户信息');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="user-view">
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card user-card">
                    <div class="card-header user-card-header">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-user me-2"></i>
                            <?= Html::encode($this->title) ?>
                        </h3>
                    </div>

                    <div class="card-body">
                        <div class="user-avatar text-center mb-4">
                            <img src="<?= $model->avatarReset ?>"
                                class="avatar-img rounded-circle shadow"
                                alt="<?= Yii::t('app', '用户头像') ?>">
                            <p class="mt-4">
                                <?= Html::a(Yii::t('app', '修改头像'), ['update-avatar'], ['class' => 'btn-edit ms-2']) ?>
                            </p>
                        </div>

                        <div class="user-details">
                            <div class="detail-item row">
                                <div class="col-sm-4 detail-label"><?= Yii::t('app', '邮箱') ?></div>
                                <div class="col-sm-8 detail-value">
                                    <?= Html::encode($model->email) ?: '<span class="text-muted">' . Yii::t('app', '未设置') . '</span>' ?>
                                    <?php if ($model->email) { ?>
                                        <?= Html::a(Yii::t('app', '修改'), ['update-email'], ['class' => 'btn-edit ms-2']) ?>
                                    <?php  } else { ?>
                                        <?= Html::a(Yii::t('app', '设置'), ['set-email'], ['class' => 'btn-edit ms-2']) ?>
                                    <?php } ?>

                                </div>
                            </div>

                            <div class="detail-item row">
                                <div class="col-sm-4 detail-label"><?= Yii::t('app', '手机号') ?></div>
                                <div class="col-sm-8 detail-value">
                                    <?= Html::encode($model->phone) ?: '<span class="text-muted">' . Yii::t('app', '未设置') . '</span>' ?>
                                    <?php if ($model->phone) { ?>
                                        <?= Html::a(Yii::t('app', '修改'), ['update-phone'], ['class' => 'btn-edit ms-2']) ?>
                                    <?php  } else { ?>
                                        <?= Html::a(Yii::t('app', '设置'), ['set-phone'], ['class' => 'btn-edit ms-2']) ?>
                                    <?php } ?>
                                </div>
                            </div>

                            <div class="detail-item row">
                                <div class="col-sm-4 detail-label"><?= Yii::t('app', '密码') ?></div>
                                <div class="col-sm-8 detail-value">
                                    ********
                                    <?= Html::a(Yii::t('app', '修改'), ['change-password'], ['class' => 'btn-edit ms-2']) ?>
                                </div>
                            </div>

                            <div class="detail-item row">
                                <div class="col-sm-4 detail-label"><?= Yii::t('app', '注册时间') ?></div>
                                <div class="col-sm-8 detail-value">
                                    <?= $model->created_at ? Yii::$app->formatter->asDatetime($model->created_at) : '-' ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .avatar-img {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border: 4px solid #fff;
    }

    .detail-item {
        padding: 12px 0;
        border-bottom: 1px solid #f0f0f0;
    }

    .detail-item:last-child {
        border-bottom: none;
    }

    .detail-label {
        font-weight: 500;
        color: #6c757d;
    }

    .detail-value {
        color: #343a40;
        font-weight: 400;
    }

    .btn-edit {
        color: #007bff;
        text-decoration: none;
        font-size: 0.875rem;
        padding: 0;
        background: none;
        border: none;
    }

    .btn-edit:hover {
        text-decoration: underline;
        color: #0056b3;
    }

    @media (max-width: 576px) {

        .detail-item .col-sm-4,
        .detail-item .col-sm-8 {
            padding: 5px 15px;
        }

        .detail-label {
            margin-bottom: 5px;
        }

        .btn-edit {
            display: block;
            margin-top: 5px;
            margin-left: 0 !important;
        }
    }
</style>