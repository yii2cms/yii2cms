<?php

/**
 * 授权请查看根目录下的LICENSE.md
 * @author ken <yiiphp@foxmail.com> 
 * @link   https://github.com/yii2cms/yii2cms 
 */

namespace app\modules\core\controllers;

use app\modules\core\models\User;
use app\modules\core\models\UserSearsh;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class UserController extends \app\modules\core\classes\AdminController
{
    /**
     * @acl 用户.查看 
     */
    public function actionIndex()
    {
        $searchModel = new UserSearsh();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 用户选择弹窗（供 layer.open iframe 使用）
     * 路由：/core/user/pop
     * @acl 用户.查看
     */
    public function actionPop()
    {
        $this->layout = '/empty';
        $searchModel = new UserSearsh();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('pop', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @acl 用户.查看 
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * @acl 用户.创建 
     */
    public function actionCreate()
    {
        $model = new User();
        //场景为create
        $model->scenario = 'create';
        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirectAdmin(['index', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
            'acl_value' => [],
        ]);
    }

    /**
     * @acl 用户.更新 
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->role != 'admin') {
            // $this->addFlash('error', '不能更新非管理员用户');
            // return $this->redirectAdmin(['index']);
        }
        $model->scenario = 'update';
        $model->password = '';
        $value = [];
        $acls = $model->acls ?? [];
        if ($acls) {
            foreach ($acls as $acl) {
                $value[] = $acl->url;
            }
        }

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirectAdmin(['index', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'acl_value' => $value,
        ]);
    }
    /**
     * @acl 用户.禁用
     */
    public function actionDisable($id)
    {
        $model = $this->findModel($id);
        if ($model->id == 1) {
            $this->addFlash('error', '不能禁用超级管理员用户');
            return $this->redirectAdmin(['index']);
        }
        $model->status = 'disabled';
        if ($model->save()) {
            add_his('user', $model->id, '禁用', 'danger');
            $this->addFlash('success', '用户禁用成功');
        } else {
            $this->addFlash('error', '用户禁用失败');
        }
        return $this->redirectAdmin(['index']);
    }
    /**
     * @acl 用户.激活
     */
    public function actionActivate($id)
    {
        $model = $this->findModel($id);
        if ($model->id == 1) {
            $this->addFlash('error', '不能激活超级管理员用户');
            return $this->redirectAdmin(['index']);
        }
        $model->status = 'active';
        if ($model->save()) {
            add_his('user', $model->id, '激活', 'success');
            $this->addFlash('success', '用户激活成功');
        } else {
            $this->addFlash('error', '用户激活失败');
        }
        return $this->redirectAdmin(['index']);
    }


    protected function findModel($id)
    {
        if (($model = User::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
