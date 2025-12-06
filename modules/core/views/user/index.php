<?php

use app\modules\core\models\User;
use yii\helpers\Html;
use yii\helpers\Url;
use app\modules\core\classes\ActionColumn;
use app\modules\core\classes\GridView;

$this->title = Yii::t('app', '用户管理');
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
$this->params['breadcrumbs'][] = [
    'label' => " ",
    'url' => ['create'],
    'class' => 'fas fa-circle-plus fa-blue',
];

?>
<div class="user-index">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'username',
            'email:email',
            'phone',
            //'access_token',
            //'auth_key',
            [
                'attribute' => 'role',
                'label' => Yii::t('app', '角色'),
                'value' => function ($model) {
                    return Html::tag('span', $model->roleName, ['class' => 'text text-' . $model->roleColor]);
                },
                'format' => 'raw',
            ],
            //'nickname',
            //'avatar',
            //'created_at',
            //'updated_at',
            [
                'attribute' => 'last_login_time',
                'label' => Yii::t('app', '最后登录时间'),
                'value' => function ($model) {
                    return $model->LastLoginTime;
                },
                'format' => 'raw',
                'filter' => false,
                'options' => ['width' => '160'],
            ],
            [
                'attribute' => 'status',
                'label' => Yii::t('app', '状态'),
                'value' => function ($model) {
                    return Html::tag('span', $model->statusLabel, ['class' => 'text text-' . $model->statusColor]);
                },
                'format' => 'raw',
            ],
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, User $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                },
                'template' => '{view} {update} {disable} {activate}',
                'buttons' => [
                    'activate' => function ($url, $model, $key) {
                        return Html::a('<span class="fas fa-check"></span>', $url, [
                            'title' => Yii::t('app', '激活用户'),
                            'class' => '',
                        ]);
                    },
                    'disable' => function ($url, $model, $key) {
                        return Html::a('<span class="fas fa-ban"></span>', $url, [
                            'title' => Yii::t('app', '禁用用户'),
                            'class' => '',
                        ]);
                    },
                ],
                'visibleButtons' => [
                    'disable' => function ($model) {
                        return $model->id != 1 && $model->status == 'active';
                    },
                    'activate' => function ($model) {
                        return $model->id != 1 && $model->status == 'disabled';
                    },
                ],
                'options' => ['width' => '130'],
                'header' => Yii::t('app', '操作'),
            ],
        ],
    ]); ?>


</div>