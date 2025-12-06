<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\core\widgets\Image;
?>

<div class="config-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>


    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?php if ($model->isNewRecord): ?>
        <?= $form->field($model, 'key')->textInput(['maxlength' => true]) ?>
    <?php else: ?>
        <?= $form->field($model, 'key')->textInput(['maxlength' => true, 'disabled' => true]) ?>
    <?php endif; ?>

    <?php if ($model->isNewRecord) { ?>
        <?= $form->field($model, 'content')->textarea(['rows' => 6]) ?>
    <?php } else { ?>
        <?php if ($model->type == 'dropDownList') { ?>
            <?= $form->field($model, 'content')->dropDownList($model->type_value) ?>
        <?php } ?>
        <?php if ($model->type == 'input') { ?>
            <?= $form->field($model, 'content')->textInput(['maxlength' => true]) ?>
        <?php } ?>
        <?php if ($model->type == 'text') { ?>
            <?= $form->field($model, 'content')->textarea(['rows' => 6]) ?>
        <?php } ?>
        <?php if ($model->type == 'image') { ?>
            <?= $form->field($model, 'content')->widget(Image::class, []) ?>
        <?php } ?>
        <?php if ($model->type == 'list') { ?>
            <div id="list-container">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>值  <span id="btn-add-row" class="fas fa-plus hand link"></span></th>
                            <th style="width:80px;">操作</th>
                        </tr>
                    </thead>
                    <tbody id="list-tbody">
                        <?php
                        $listData = json_decode($model->content, true) ?: [];
                        foreach ($listData as $idx => $val): ?>
                            <tr>
                                <td><?= Html::textInput("Config[content][{$idx}]", $val, ['class' => 'form-control']) ?></td>
                                <td><?= Html::button('删除', ['class' => 'btn btn-danger btn-sm btn-remove-row']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
               
            </div>

            <?php
            $this->registerJs(
                <<<JS
                $('#btn-add-row').on('click', function(){
                    var index = new Date().getTime();
                    var row = '<tr>' +
                        '<td><input type="text" name="Config[content][]" class="form-control" /></td>' +
                        '<td><button type="button" class="btn btn-danger btn-sm btn-remove-row">删除</button></td>' +
                        '</tr>';
                    $('#list-tbody').append(row);
                });
                $(document).on('click', '.btn-remove-row', function(){
                    $(this).closest('tr').remove();
                });
JS
            );
            ?>

        <?php } ?>
    <?php } ?>

    <?php if ($model->isNewRecord): ?>
        <?= $form->field($model, 'type')->textInput(['maxlength' => true]) ?>
    <?php else: ?>

    <?php endif; ?>


    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', '保存'), ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <?php include __DIR__ . '/help.php'; ?>

</div>