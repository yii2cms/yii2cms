<?php

/**
 * 授权请查看根目录下的LICENSE.md
 * @author ken <yiiphp@foxmail.com> 
 * @link   https://github.com/yii2cms/yii2cms 
 */

namespace app\modules\core\classes;

use yii\grid\GridView as BaseGridView;
use yii\helpers\Html;
use Yii;
use yii\grid\DataColumn;

/**
 * yii\grid\GridView 
 */
class GridView extends BaseGridView
{
    protected $langType = 'product';
    protected $langIcon = '<i class="fa fa-language"></i>';

    public function init()
    {
        $this->layout = " 
                {items}\n
                <div class='summary-pagination'>
                    {summary}
                    {pager}
                </div>\n";
        $this->summaryOptions['class'] = $this->summaryOptions['class'] ?? 'summary';
        $this->tableOptions['class']   = 'layui-table yii2-grid-table';
        return parent::init();
    }
    /**
     * 多语言列
     */
    public static function langColumn($searchModel)
    {
        return [
            'attribute' => 'id',
            'label' => Yii::t('app', '语言'),
            'filter' => false,
            'value' => function ($model) {
                $badges = [];
                $langData = Language::getAllLanguageCode();
                $defaultLanguage = Language::getDefaultLanguageCode();
                /**
                 * 当前的url加language=语言
                 */
                $current = Yii::$app->controller->module->id . '/' . Yii::$app->controller->id . '/update';
                foreach ($langData as $k => $v) {
                    $url = url($current, ['lang' => $k, 'id' => $model->id]);
                    /**
                     * 忽略当前语言
                     */
                    if ($k == $defaultLanguage) {
                        continue;
                    }
                    /**
                     * 判断语言有没有对应的翻译
                     */
                    $has = $model->hasLanguage($k);
                    $class = $has ? 'bg-primary' : 'bg-secondary';
                    $badges[] = Html::tag('a', $v['badge'], [
                        'class' => 'badge ' . $class . ' me-1',
                        'title' => $v['name'],
                        'style' => 'cursor:help; display:inline-block;',
                        'href' => $url,
                    ]);
                }
                return implode('', $badges);
            },
            'format' => 'raw',
        ];
    }
    /**
     * status
     * 状态筛选
     */
    public static function headerStatus($searchModel)
    {
        return [
            'attribute' => 'status',
            'filter' => $searchModel->getStatusList(),
            'value' => function ($model) {
                return "<span class='" . $model->statusLabelColor . "'>" . $model->statusLabel . "</span>";
            },
            'format' => 'raw',
            'options' => ['width' => '100px', 'text-align' => 'center'],
        ];
    }
    /**
     * 时间范围筛选
     * @param string $searchModel 搜索模型
     * @return array
     */
    public static function headerCreatedAt($searchModel)
    {
        return [
            'attribute' => 'created_at',
            'filter' => '<div class="row">' .
                '<div class="col-md-6">' .
                \yii\jui\DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'created_at_start',
                    'dateFormat' => 'yyyy-MM-dd',
                    'options' => ['class' => 'form-control', 'placeholder' => Yii::t('app', '开始日期')],
                ]) .
                '</div>' .
                '<div class="col-md-6">' .
                \yii\jui\DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'created_at_end',
                    'dateFormat' => 'yyyy-MM-dd',
                    'options' => ['class' => 'form-control', 'placeholder' => Yii::t('app', '结束日期')],
                ]) .
                '</div>' .
                '</div>',
            'value' => function ($model) {
                return $model->createdAtLabel;
            },
            'format' => 'raw',
            'options' => ['width' => '300px'],
        ];
    }

    protected function initColumns()
    {
        if (empty($this->columns)) {
            $this->guessColumns();
        }
        foreach ($this->columns as $i => $column) {
            if (!$column) {
                unset($this->columns[$i]);
                continue;
            }
            if (is_string($column)) {
                $column = $this->createDataColumn($column);
            } else {
                $column = Yii::createObject(array_merge([
                    'class' => $this->dataColumnClass ?: DataColumn::className(),
                    'grid' => $this,
                ], $column));
            }
            if (!$column->visible) {
                unset($this->columns[$i]);
                continue;
            }
            $this->columns[$i] = $column;
        }
    }
}
