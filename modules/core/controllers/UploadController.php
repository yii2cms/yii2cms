<?php

/**
 * 授权请查看根目录下的LICENSE.md
 * @author ken <yiiphp@foxmail.com> 
 * @link   https://github.com/yii2cms/yii2cms 
 */

namespace app\modules\core\controllers;

use app\modules\core\classes\Uploader;

class UploadController extends \app\modules\core\classes\LoginController
{
    use Uploader;
    public $enableCsrfValidation = false;
    /**
     * 是否只检查用户登录,不检测权限
     */
    protected $justCheckUserLogin = true;
}
