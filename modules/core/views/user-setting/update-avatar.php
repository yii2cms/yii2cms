<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\core\widgets\ImageOneButton;

$this->title = Yii::t('app', '更新头像');
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
                            <i class="fas fa-user-circle me-2"></i>
                            <?= Html::encode($this->title) ?>
                        </h3>
                    </div>

                    <div class="card-body">
                        <?php $form = ActiveForm::begin([
                            'id' => 'update-avatar-form',
                            'options' => ['class' => 'form-horizontal', 'enctype' => 'multipart/form-data'],
                        ]); ?>

                        <div class="text-center mb-4">
                            <div class="avatar-preview-container">
                                <img id="avatar-preview"
                                    src="<?= Html::encode($model->avatarReset) ?>"
                                    class="rounded-circle shadow"
                                    style="width: 150px; height: 150px; object-fit: cover; border: 4px solid #fff;"
                                    alt="<?= Yii::t('app', '当前头像') ?>">
                            </div>
                        </div>

                        <div class="form-group text-center">
                            <input type="hidden" id="avatar-url" name="User[avatar]">
                            <?= $form->field($model, 'avatar')->widget(ImageOneButton::class, [
                                'targetInput' => '#avatar-url',
                            ])->label(Yii::t('app', '选择新头像')) ?>  
                        </div>

                        <div class="form-group text-center mt-4">
                            <?= Html::submitButton(Yii::t('app', '保存头像'), ['class' => 'btn btn-primary px-4']) ?>
                            <?= Html::a(Yii::t('app', '取消'), ['index'], ['class' => 'btn btn-outline-secondary ms-2']) ?>
                        </div>

                        <?php ActiveForm::end(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>