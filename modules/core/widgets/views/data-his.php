<?php

use yii\widgets\LinkPager;

$find = $model->dataHis?:[];
$pagination = $find['pagination'];
$his = $find['data'];
if ($his) {
?>
    <h2 class="mb-3"><?= Yii::t('app', '操作记录') ?></h2>
    <table class="table table-bordered">
        <tr>
            <th><?= Yii::t('app', '操作内容') ?></th>
            <th style="width: 300px;"><?= Yii::t('app', '操作人') ?></th>
            <th style="width: 180px;"><?= Yii::t('app', '操作时间') ?></th>

        </tr>
        <?php foreach ($his as $item) { ?>
            <tr>
                <td><span class="text text-<?= $item->color??'' ?>"><?= $item->body??'' ?></span></td>
                <td><?= $item->userName??'' ?></td>
                <td><?= $item->createdAtLabel??'' ?></td> 
            </tr>
        <?php } ?>
    </table>
    <?php
    echo LinkPager::widget([
        'pagination' => $pagination,
    ]);
    ?>
<?php } ?>