<?php
/**
 * @var \app\modules\core\widgets\Table $widget
 * @var \yii\web\View $this
 */

use yii\helpers\Html;

// 处理 Acl::getAll() 格式的数据
$processedData = [];
if (!empty($widget->data) && isset($widget->data[0]['title']) && isset($widget->data[0]['items'])) {
    foreach ($widget->data as $row) {
        $title = $row['title'] ?? '';
        $items = $row['items'] ?? [];
        foreach ($items as $item => $urls) {
            // 将 URLs 合并为逗号分隔的字符串
            $urlString = implode(', ', $urls);
            $processedData[] = [
                'name' => $title,
                'description' => $item,
                'value' => $urlString, // 使用逗号连接的 URL 字符串
                'urls' => $urls        // 保留原始 URL 数组以供参考
            ];
        }
    }
}
?>

<table <?= Html::renderTagAttributes($widget->tableOptions) ?>>
    <thead>
        <tr>
            <?php if ($widget->enableSelection): ?>
                <th class="text-center" style="width: 100px">
                    <?= Html::encode($widget->selectionLabel) ?>
                    <?= Html::checkbox('select-all', false, [
                        'class' => 'select-all-checkbox',
                        'data-target' => '#' . Html::encode($widget->tableOptions['id']) . ' .select-row'
                    ]) ?>
                </th>
            <?php endif; ?>
            <?php foreach ($widget->columns as $column): ?>
                <?php
                $label = $column['label'] ?? '';
                $options = [];
                if (isset($column['class'])) {
                    $options['class'] = $column['class'];
                }
                if (isset($column['style'])) {
                    $options['style'] = $column['style'];
                }
                ?>
                <th <?= Html::renderTagAttributes($options) ?>><?= Html::encode($label) ?></th>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
        <?php
        $selectedValues = $widget->value ? (array) $widget->value : [];
        $currentTitle = null;
        ?>
        <?php foreach ($processedData as $index => $row): ?>
            <?php
            $rowId = $row['value'] ?? $index;
            $title = $row['name'] ?? '';
            $displayName = ($currentTitle !== $title) ? $title : '';
            $showGroupSelect = ($currentTitle !== $title);
            $currentTitle = $title;
            ?>
            <tr data-id="<?= Html::encode($rowId) ?>">
                <?php if ($widget->enableSelection): ?>
                    <td class="text-center">
                        <?php
                        $value = $row['value'] ?? $rowId;
                        $isChecked = in_array($value, $selectedValues, true);
                        echo Html::checkbox("{$widget->selectionField}[]", $isChecked, [
                            'value' => $value,
                            'class' => 'select-row',
                            'data-group' => Html::encode($title)
                        ]);
                        if ($showGroupSelect) {
                            echo Html::checkbox("select-group-{$title}", false, [
                                'class' => 'select-group-checkbox',
                                'data-target' => '#' . Html::encode($widget->tableOptions['id']) . ' .select-row[data-group="' . Html::encode($title) . '"]',
                                'style' => 'margin-left: 10px;'
                            ]);
                        }
                        ?>
                    </td>
                <?php endif; ?>
                <?php foreach ($widget->columns as $column): ?>
                    <?php
                    $key = $column['key'] ?? '';
                    $value = ($key === 'name') ? $displayName : (isset($row[$key]) ? $row[$key] : '');
                    $format = $column['format'] ?? 'text';
                    switch ($format) {
                        case 'raw':
                            $content = $value;
                            break;
                        case 'html':
                            $content = Html::encode($value);
                            break;
                        case 'text':
                        default:
                            $content = Html::encode($value);
                            break;
                    }
                    $options = [];
                    if (isset($column['class'])) {
                        $options['class'] = $column['class'];
                    }
                    if (isset($column['style'])) {
                        $options['style'] = $column['style'];
                    }
                    ?>
                    <td <?= Html::renderTagAttributes($options) ?>><?= $content ?></td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php
// 注册 "Select All" 和 "Select Group" 功能的 JavaScript
if ($widget->enableSelection) {
    $view = $this;
    $id = $widget->tableOptions['id'];
    $js = new \yii\web\JsExpression("
        // 全局全选
        $('#{$id} .select-all-checkbox').on('change', function() {
            var isChecked = $(this).is(':checked');
            var \$checkboxes = $(this).data('target');
            \$(\$checkboxes).prop('checked', isChecked).trigger('change');
            // 更新组全选状态
            $('.select-group-checkbox').prop('checked', isChecked);
        });

        // 组全选
        $('.select-group-checkbox').on('change', function() {
            var isChecked = $(this).is(':checked');
            var \$target = $(this).data('target');
            \$(\$target).prop('checked', isChecked).trigger('change');
        });
    ");
    $view->registerJs($js);
}
?>