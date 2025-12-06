<?php

namespace app\modules\core\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Json;

class Help extends Widget
{
    public $title = '帮助'; // 对话框标题
    public $content = '这是默认的帮助内容，请配置 content 属性。'; // 默认帮助内容
    public $buttonIcon = 'fas fa-question-circle'; // Font Awesome 图标类
    public $buttonOptions = ['class' => 'btn btn-primary btn-floating']; // 浮动按钮样式
    public $dialogOptions = [
        'width' => 800, // 对话框宽度
        'maxHeight' => 500, // 对话框高度
        'modal' => true, // 模态对话框
        'autoOpen' => false, // 默认不自动打开
        'position'=>['my'=>'center','at'=>'center'],
    ];

    // 初始化 Widget
    public function init()
    {
        parent::init();
    }

    // 运行 Widget，生成按钮和对话框
    public function run()
    {
        // 渲染帮助内容
        $content = $this->renderContent();

        // 配置 jQuery UI Dialog 选项
        $dialogOptions = array_merge($this->dialogOptions, [
            'title' => $this->title
        ]);

        // 注册 CSS 和 JS，并生成对话框容器
        $dialogHtml = $this->registerAssets($dialogOptions, $content);

        // 输出浮动按钮，使用 Font Awesome 图标
        $button = Html::button('<i class="' . Html::encode($this->buttonIcon) . '"></i>', [
            'id' => $this->getId() . '-help-btn',
            'class' => $this->buttonOptions['class'],
        ]);

        return $button . $dialogHtml;
    }

    // 渲染帮助内容视图
    protected function renderContent()
    {
        return $this->getView()->render('help', [
            'content' => $this->content,
            'widget' => $this
        ], $this);
    }

    // 注册 CSS 和 JS，并生成对话框容器
    protected function registerAssets($dialogOptions, $content)
    {
        $view = $this->getView();
        $buttonId = $this->getId() . '-help-btn';
        $dialogId = $this->getId() . '-help-dialog';

        // CSS 样式：浮动按钮、对话框及关闭按钮
        $css = <<<CSS
        .ui-dialog{
            z-index:9999 !important;
        }
        .btn-floating {
            position: fixed;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 1000;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        #{$dialogId} {
            display: none;
            padding: 20px;
            font-size: 14px;
            line-height: 1.6;
        }
        /* 优化 jQuery UI Dialog 关闭按钮样式 */
        .ui-dialog .ui-dialog-titlebar-close {
            background: none;
            border: none;
            width: 20px;
            height: 20px;
            margin: -10px 0 0 0;
            padding: 0;
            color: #333;
            font-size: 16px;
            cursor: pointer;
        }
        .ui-dialog .ui-dialog-titlebar-close:hover {
            color: #000;
            background: #f0f0f0;
            border-radius: 50%;
        }
        .ui-dialog .ui-dialog-titlebar-close::before {
            content: "\\2716"; /* 使用 Unicode 叉号 */
            font-family: Arial, sans-serif;
        }
        .ui-dialog .ui-dialog-buttonpane button {
            background: #007bff;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
        }
        .ui-dialog .ui-dialog-buttonpane button:hover {
            background: #0056b3;
        }
        CSS;

        // JS 脚本：初始化 jQuery UI Dialog 并绑定点击事件
        $js = <<<JS
        $(function() {
            $('#{$dialogId}').dialog({$this->encodeDialogOptions($dialogOptions)});
            $('#{$buttonId}').on('click', function() {
                $('#{$dialogId}').dialog('open');
            });
        });
        JS;

        // 注册 CSS 和 JS
        $view->registerCss($css);
        $view->registerJs($js);
        $view->registerCssFile('@web/css/markdown.css');
        // 返回对话框容器 HTML
        return Html::tag('div', $content, ['id' => $dialogId]);
    }

    // 编码 jQuery UI Dialog 选项
    protected function encodeDialogOptions($options)
    {
        return Json::encode($options);
    }
}
