<?php

/**
 * 授权请查看根目录下的LICENSE.md
 * @author ken <yiiphp@foxmail.com> 
 * @link   https://github.com/yii2cms/yii2cms 
 */

namespace app\modules\core\controllers;

use app\modules\core\models\MailTemplate;
use app\modules\core\models\MailTemplateSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class MailTemplateController extends \app\modules\core\classes\AdminController
{
    /**
     * @acl 邮件模板.查看
     */
    public function actionIndex()
    {
        $searchModel = new MailTemplateSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @acl 邮件模板.查看
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * @acl 邮件模板.创建
     */
    public function actionCreate()
    {
        $model = new MailTemplate();

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

    /**
     * @acl 邮件模板.更新
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirectAdmin(['index', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @acl 邮件模板.删除
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirectAdmin(['index']);
    }


    protected function findModel($id)
    {
        if (($model = MailTemplate::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
