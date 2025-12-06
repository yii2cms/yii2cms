<?php 

use yii\helpers\Inflector;
use yii\helpers\StringHelper;
 

echo "<?php\n";
?> 

namespace <?= $generator->getControllerNamespace() ?>;

class <?= StringHelper::basename($generator->controllerClass) ?> extends \app\modules\core\classes\AdminController
{
<?php foreach ($generator->getActionIDs() as $action): ?>
    public function action<?= Inflector::id2camel($action) ?>()
    {
        return $this->render('<?= $action ?>');
    }

<?php endforeach; ?>
}
