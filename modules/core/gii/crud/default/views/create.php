<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;
 

echo "<?php\n";
?>

use yii\helpers\Html; 

$this->title = Yii::t('app', '创建'); 
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', ''), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-create"> 

    <?= "<?= " ?>$this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
