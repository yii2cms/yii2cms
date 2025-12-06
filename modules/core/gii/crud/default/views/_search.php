<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;
 
echo "<?php\n";
?>

use yii\helpers\Html;
use yii\widgets\ActiveForm;
 
?>

<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-search">

    <?= "<?php " ?>$form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
<?php if ($generator->enablePjax): ?>
        'options' => [
            'data-pjax' => 1
        ],
<?php endif; ?>
    ]); ?>

<?php
$count = 0;
foreach ($generator->getColumnNames() as $attribute) {
    if (++$count < 6) {
        echo "    <?= " . $generator->generateActiveSearchField($attribute) . " ?>\n\n";
    } else {
        echo "    <?php // echo " . $generator->generateActiveSearchField($attribute) . " ?>\n\n";
    }
}
?>
    <div class="form-group">
        <?= "<?= " ?>Html::submitButton(Yii::t('app','搜索'), ['class' => 'btn btn-primary']) ?>
        <?= "<?= " ?>Html::resetButton(Yii::t('app','重置'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?= "<?php " ?>ActiveForm::end(); ?>

</div>
