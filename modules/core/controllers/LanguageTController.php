<?php

/**
 * 授权请查看根目录下的LICENSE.md
 * @author ken <yiiphp@foxmail.com> 
 * @link   https://github.com/yii2cms/yii2cms 
 */

namespace app\modules\core\controllers;

use app\modules\core\classes\Language;
use app\modules\core\models\LanguageT;
use app\modules\core\models\LanguageTSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Yii;
use app\modules\core\trait\LanguageTrait;
use app\modules\core\classes\Menu;

class LanguageTController extends \app\modules\core\classes\AdminController
{
    use LanguageTrait;
    /**
     * 语言翻译列表
     * @acl 语言翻译.查看
     */
    public function actionIndex($code, $is_default = false)
    {
        $this->sync();
        $searchModel = new LanguageTSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->query->andWhere(['code' => $code]);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 语言翻译详情
     * @acl 语言翻译.查看
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * 新增语言翻译
     * @acl 语言翻译.新增
     */
    public function actionCreate()
    {
        $model = new LanguageT();

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
     * 更新语言翻译
     * @acl 语言翻译.更新
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
     * 删除语言翻译
     * @acl 语言翻译.删除
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirectAdmin(['index']);
    }


    protected function findModel($id)
    {
        if (($model = LanguageT::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
