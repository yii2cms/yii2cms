<?php

namespace app\modules\core\content;

use app\modules\core\models\Post;
use yii\helpers\Html;
use yii\helpers\Url;
use app\modules\core\classes\ActionColumn;
use Yii;
use app\modules\core\classes\GridView;

class Grid
{
    public $title;
    public $create_title = '';
    public $columns = [];
    public $dataProvider;
    public $searchModel;

    public function getTitle()
    {
        if ($this->title) {
            return '<h1>' . Yii::t('app', Html::encode($this->title)) . '</h1>';
        }
    }

    public function getCreateButton()
    {
        if ($this->create_title) {
            return '<p>' .
                Html::a(Yii::t('app', '创建'), ['create'], ['class' => 'btn btn-primary']) .
                '</p>';
        }
    }

    public function getColumns()
    {
        $columns = [];
        foreach ($this->columns as $column) {
            if (isset($column['call'])) {
                $method = "get" . ucfirst($column['call']);
                $columns[] = call_user_func([$this, $method]);
            } else {
                $columns[] = $column;
            }
        }
        $columns[] = $this->getActions();
        return $columns;
    }

    public function getGrid()
    {
        return GridView::widget([
            'dataProvider' => $this->dataProvider,
            'filterModel' => $this->searchModel,
            'columns' => $this->getColumns(),
        ]);
    }

    public function getContent()
    {
        return $this->getCreateButton() . $this->getGrid();
    }

    public function getActions()
    {
        return [
            'class' => ActionColumn::className(),
            'urlCreator' => function ($action, Post $model, $key, $index, $column) {
                return Url::toRoute([$action, 'id' => $model->id]);
            },
            'template' => '{view} {update} {delete}',
            'options' => ['width' => '123px'],
            'header' => Yii::t('app', '操作'),
        ];
    }

    public function getHeaderStatus()
    {
        return GridView::headerStatus($this->searchModel);
    }

    public function getHeaderCreatedAt()
    {
        return GridView::headerCreatedAt($this->searchModel);
    }
}
