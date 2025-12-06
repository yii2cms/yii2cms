<?php 

echo "<?php\n";
?> 
 
namespace <?= $generator->getControllerNamespace() ?>; 
 
class DefaultController extends \app\modules\core\classes\AdminController
{
     
    public function actionIndex()
    {
        return $this->render('index');
    }
}
