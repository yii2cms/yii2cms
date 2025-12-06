<?php

/**
 * 授权请查看根目录下的LICENSE.md
 * @author ken <yiiphp@foxmail.com> 
 * @link   https://github.com/yii2cms/yii2cms 
 */

namespace app\modules\core\controllers;

use app\modules\core\models\Config;
use app\modules\core\models\ConfigSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class DefaultController extends \app\modules\core\classes\LoginController
{

    public function actionIndex()
    {
        if ($this->shop_id) {
            jump('/core/shop/index');
        }
        jump('/core/admin/index');
    }
}
