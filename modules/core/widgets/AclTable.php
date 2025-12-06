<?php

namespace app\modules\core\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use yii\base\Model;
use yii\web\JsExpression;

/**
 * Bootstrap 5 表格组件，用于 ActiveForm，支持行选择和多列配置
 */
class AclTable extends Widget
{
    /**
     * @var Model|null 绑定的 ActiveForm 模型
     */
    public $model;

    /**
     * @var string|null 绑定的模型属性名
     */
    public $attribute;

    /**
     * @var array 表格数据，格式为数组的数组，例如 [['id' => 1, 'name' => 'Item 1'], ...]
     * 对于 Acl::getAll()，将在视图中处理为适合的格式
     */
    public $data = [];

    /**
     * @var array 列配置，格式为 [
     *     'key' => '字段名',
     *     'label' => '列标题',
     *     'format' => 'raw'|'text'|'html'（可选，默认为 text）,
     *     'class' => 'CSS 类'（可选）,
     *     'style' => 'CSS 样式'（可选）
     * ]
     */
    public $columns = [];

    /**
     * @var bool 是否启用行选择功能，默认为 true
     */
    public $enableSelection = true;

    /**
     * @var array 表格的 HTML 属性，例如 ['class' => 'table table-striped']
     */
    public $tableOptions = ['class' => 'table table-striped table-bordered table-hover'];

    /**
     * @var string 选择列的表头标题，默认为空
     */
    public $selectionLabel = '';

    /**
     * @var string 选择字段名称，用于 ActiveForm，默认为 'selection'
     */
    public $selectionField = 'selection';

    /**
     * @var array 选中的值，格式为 ['url1', 'url2', ...]
     */
    public $value = [];

    /**
     * 初始化组件
     */
    public function init()
    {
        parent::init();
        // 确保 tableOptions 包含 Bootstrap 5 的默认类
        if (!isset($this->tableOptions['class'])) {
            $this->tableOptions['class'] = 'table table-striped table-bordered table-hover';
        }
        // 为表格添加唯一 ID
        if (!isset($this->tableOptions['id'])) {
            $this->tableOptions['id'] = $this->getId();
        }
    }

    /**
     * 运行组件并渲染表格
     * @return string 渲染的 HTML 内容
     */
    public function run()
    {
        $this->registerClientScript();
        return $this->render('acl_table', [
            'widget' => $this, 
        ]);
    }

    /**
     * 注册客户端脚本，用于行选择功能
     */
    protected function registerClientScript()
    {
        if ($this->enableSelection) {
            $view = $this->getView();
            $id = $this->tableOptions['id'];
            $js = new JsExpression("
                $('#{$id} .select-row').on('change', function() {
                    var \$row = $(this).closest('tr');
                    if ($(this).is(':checked')) {
                        \$row.addClass('table-active');
                    } else {
                        \$row.removeClass('table-active');
                    }
                });
                $('#{$id}').on('click', 'tr', function(e) {
                    if (e.target.type !== 'checkbox') {
                        var \$checkboxes = $(this).find('.select-row');
                        \$checkboxes.each(function() {
                            $(this).prop('checked', !$(this).prop('checked')).trigger('change');
                        });
                    }
                });
            ");
            $view->registerJs($js);
        }
    }
}
