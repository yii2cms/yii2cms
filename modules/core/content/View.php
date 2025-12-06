<?php

namespace app\modules\core\content;

use yii\helpers\Html;
use yii\widgets\DetailView;
use Yii;

class View
{
    public $model;
    public $attributes = [];
    public function getAction()
    {
        $model = $this->model;
        return '<p>' .
            Html::a(Yii::t('app', '编辑'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) .
            Html::a(Yii::t('app', '删除'), ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger ms-4',
                'data' => [
                    'confirm' => Yii::t('app', '确定要删除此项目吗？'),
                    'method' => 'post',
                ],
            ]) .
            '</p>';
    }

    public function getContent()
    {
        $action = $this->getAction();
        return $action .
            DetailView::widget([
                'model' => $this->model,
                'attributes' =>  $this->getAttributes(),
            ]);
    }

    public function getAttributes()
    {
        return $this->attributes;
    }
}
