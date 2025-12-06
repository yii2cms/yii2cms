<?php

/**
 * 授权请查看根目录下的LICENSE.md
 * @author ken <yiiphp@foxmail.com> 
 * @link   https://github.com/yii2cms/yii2cms 
 */

namespace app\modules\core\controllers;

use yii\web\UploadedFile;
use app\modules\core\models\UploadForm;
use Yii;

class ShopController extends \app\modules\core\classes\ShopController
{
    protected $ignoreAcl = ['index'];
    public function actionIndex()
    {
        return $this->render('index');
    }
}
