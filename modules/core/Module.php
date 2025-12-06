<?php

namespace app\modules\core;

use Yii;
use app\modules\core\classes\Menu;
use yii\base\Event;
use app\modules\core\classes\AdminController;
use app\modules\core\classes\Acl;
use app\modules\core\classes\Config;

class Module extends \yii\base\Module
{

    public $controllerNamespace = 'app\modules\core\controllers';


    public function init()
    {
        parent::init();
        if (Yii::$app instanceof \yii\console\Application) {
            $this->controllerNamespace = 'app\modules\core\commands';
        } 
        
    }
}
