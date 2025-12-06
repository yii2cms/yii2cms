<?php

$className = $generator->moduleClass;
$pos = strrpos($className, '\\');
$ns = ltrim(substr($className, 0, $pos), '\\');
$className = substr($className, $pos + 1);

$name = $generator->moduleID;
echo "<?php\n";
?> 
 
namespace <?= $ns ?>;

use Yii;
use \app\modules\core\classes\Menu;
use yii\base\Event;
use \app\modules\core\classes\AdminController;
use \app\modules\core\classes\Acl;
use \app\modules\core\classes\Config;

class <?= $className ?> extends \yii\base\Module
{
    
    public $controllerNamespace = '<?= $generator->getControllerNamespace() ?>';

    public function init()
    {
        parent::init();

        if (Yii::$app instanceof \yii\console\Application) {
            $this->controllerNamespace = 'app\modules\<?=$name?>\commands';
        }
       
    }
}
