<?php

/**
 * 授权请查看根目录下的LICENSE.md
 * @author ken <yiiphp@foxmail.com> 
 * @link   https://github.com/yii2cms/yii2cms 
 */

namespace app\modules\core\controllers;

use Yii;

class LogoutController extends \app\modules\core\classes\BaseController
{

    public function actionIndex()
    {
        Yii::$app->admin->logout();
        return $this->redirectAdmin(['/core/login/index']);
    }
}
