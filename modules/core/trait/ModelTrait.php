<?php

namespace app\modules\core\trait;

use yii\web\NotFoundHttpException;
use Yii;

trait ModelTrait
{

    public function Index()
    {
        $class = $this->modelClassSearch;
        $searchModel = new $class;
        if (method_exists($this, 'beforeIndex')) {
            $this->beforeIndex($searchModel);
        }
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function View($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function Create()
    {
        $class = $this->modelClass;
        $model = new $class;
        if (method_exists($this, 'beforeCreate')) {
            $this->beforeCreate($model);
        }
        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirectAdmin(['index', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function Update($id)
    {
        $model = $this->findModel($id);
        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirectAdmin(['index', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }


    public function Delete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirectAdmin(['index']);
    }

    protected function findModel($id)
    {
        $class = $this->modelClass;
        $where = ['id' => $id,];
        if (method_exists($this, 'beforeFindModel')) {
            $this->beforeFindModel($where);
        }
        if (($model = $class::findOne($where)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException(Yii::t('app', '请求的资源不存在'));
    }
}
