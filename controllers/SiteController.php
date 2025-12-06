<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use Mail;
use app\modules\core\classes\Translate;

class SiteController extends Controller
{

    public function actionIndex()
    {
        //echo Translate::to("欢迎1",'zh','en'); 

        //Mail::send('sunkangchina1002@2925.com', '标题','内容');

        return;
        return $this->render('index');
    }

    public function actionError()
    {
        $exception = Yii::$app->errorHandler->exception;
        if ($exception) {
            $message = $exception->getMessage();
        } else {
            $name = '错误';
            $message = '未知错误';
        }
        return $this->render('error', compact('name', 'message'));
    }
}
