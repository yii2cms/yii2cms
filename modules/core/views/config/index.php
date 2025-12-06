<?php

use app\modules\core\models\Config;
use yii\helpers\Html;
use yii\helpers\Url;
use app\modules\core\classes\ActionColumn;
use app\modules\core\classes\GridView;
use app\modules\core\widgets\Help;
use app\modules\core\classes\Str;

$this->title = Yii::t('app', '配置');
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
$this->params['breadcrumbs'][] = [
    'label' => " ",
    'url' => ['create'],
    'class' => 'fas fa-circle-plus fa-blue',
];

?>
<div class="config-index"> 

    <?php // echo $this->render('_search', ['model' => $searchModel]); 
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'name',
            'key',
            [
                'attribute' => 'content',
                'value' => function (Config $model) {
                    $content  = $model->displayContent;
                    if ($content && strlen($content) > 50 && !is_html($content)) {
                        $content = Str::cut($content, 50, '...');
                    }
                    return $content;
                },
                'format' => 'raw',
            ],
            //'created_at',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Config $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                },
                'template' => '{update}',
                'header' => Yii::t('app', '操作'),
                'options' => ['width' => '80px'],
            ],
        ],
    ]); ?>


</div>

<?= Help::widget([
    'content' => "
# 配置页面帮助

这是配置页面，您可以在这里配置系统的参数。

- 请谨慎操作，确保配置参数正确。
- 如配置短信时，可在 **配置名称** 搜索 **短信** 查看短信配置参数。
- 如果您不确定某个参数的作用，请咨询软件服务方。
- 开发者可通过 `get_config(key)` 函数获取配置参数的值。

    ",
]) ?>