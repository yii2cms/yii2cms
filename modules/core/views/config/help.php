<?php

use app\modules\core\widgets\Help;
?>

<?= Help::widget([
    'content' => $model->help,
]) ?>