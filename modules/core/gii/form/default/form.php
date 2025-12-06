<?php 

$class = str_replace('/', '-', trim($generator->viewName, '_'));

echo "<?php\n";
?>

use yii\helpers\Html;
use app\modules\core\classes\ActiveForm;
 
<?= "?>" ?>

<div class="<?= $class ?>">

    <?= "<?php " ?>$form = ActiveForm::begin(); ?>

    <?php foreach ($generator->getModelAttributes() as $attribute): ?>
    <?= "<?= " ?>$form->field($model, '<?= $attribute ?>') ?>
    <?php endforeach; ?>

        <div class="form-group">
            <?= "<?= " ?>Html::submitButton(<?= $generator->generateString('Submit') ?>, ['class' => 'btn btn-primary']) ?>
        </div>
    <?= "<?php " ?>ActiveForm::end(); ?>

</div>
