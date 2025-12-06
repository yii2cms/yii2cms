<?php

/**
 * 授权请查看根目录下的LICENSE.md
 * @author ken <yiiphp@foxmail.com> 
 * @link   https://github.com/yii2cms/yii2cms 
 */

namespace app\modules\core\controllers;

use Yii;

/**
 * 统一处理异常错误
 */
class ErrorController extends \app\modules\core\classes\BaseController
{
    /**
     * 处理异常错误
     */
    public function actionIndex()
    {
        $e = Yii::$app->errorHandler->exception;
        $message = $e->getMessage();
        if (is_ajax()) {
            echo json_encode(['code' => 250, 'msg' => $message], JSON_UNESCAPED_UNICODE);
            exit;
        }
        return $this->render('index', [
            'message' => $message,
        ]);
    }
}
