<?php
use yii\helpers\Html;
use yii\widgets\LinkPager;
?>
<div class="user-pop">
    <?php echo Html::beginForm(['pop'], 'get', ['class' => 'layui-form', 'style' => 'margin-bottom:10px;']); ?>
        <div class="layui-inline">
            <input type="text" name="UserSearsh[username]" value="<?= Html::encode($searchModel->username) ?>" class="layui-input" placeholder="<?= Yii::t('app', '输入用户名/邮箱/手机号搜索') ?>">
        </div>
        <button class="layui-btn" type="submit"><?= Yii::t('app', '搜索') ?></button>
    <?php echo Html::endForm(); ?>

    <table class="layui-table">
        <thead>
            <tr>
                <th><?= Yii::t('app', '头像') ?></th>
                <th><?= Yii::t('app', '用户名') ?></th>
                <th><?= Yii::t('app', '邮箱') ?></th>
                <th><?= Yii::t('app', '电话') ?></th>
                <th><?= Yii::t('app', '操作') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($dataProvider->getModels() as $user): ?>
                <tr>
                    <td><?= Html::img($user->avatar ?: '', ['style' => 'width:40px;height:40px;']) ?></td>
                    <td><?= Html::encode($user->fullName ?? $user->username) ?></td>
                    <td><?= Html::encode($user->email) ?></td>
                    <td><?= Html::encode($user->phone) ?></td>
                    <td>
                        <button class="layui-btn layui-btn-sm layui-btn-normal select-user"
                            data-id="<?= Html::encode($user->id) ?>"
                            data-name="<?= Html::encode($user->fullName ?? $user->username) ?>"
                            data-email="<?= Html::encode($user->email) ?>"
                            data-phone="<?= Html::encode($user->phone) ?>"
                            data-avatar="<?= Html::encode($user->avatar ?: '') ?>"><?= Yii::t('app', '选择') ?></button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="pagination-wrap" style="margin-top:10px; text-align:center;">
        <?= LinkPager::widget(['pagination' => $dataProvider->pagination]) ?>
    </div>
</div>

<?php $this->registerJs(<<<JS
    $(document).on('click', '.select-user', function(){
        var payload = {
            id: $(this).data('id'),
            name: $(this).data('name'),
            email: $(this).data('email'),
            phone: $(this).data('phone'),
            avatar: $(this).data('avatar')
        };
        if (window.parent && typeof window.parent.selectUserCallback === 'function'){
            window.parent.selectUserCallback(payload);
            var index = window.parent.layer.getFrameIndex(window.name);
            window.parent.layer.close(index);
        }
    });
JS
); ?>