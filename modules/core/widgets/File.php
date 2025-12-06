<?php

namespace app\modules\core\widgets;

use app\modules\core\classes\Mime;
use app\modules\core\classes\Url;
use yii\base\Widget;

/**
 * 通用文件上传组件（支持单个/多个）
 * 默认支持: pdf, xls, xlsx, doc, docx, ppt, pptx, jpg, png, jpeg, webp
 */
class File extends Widget
{
    public $model;        // 模型实例
    public $value;        // 文件值（URL 或数组）
    public $attribute;    // 模型属性名（如 pdf / red_pdf）
    public $limit = 1;    // 最大上传数量
    public $multiple = false; // 是否允许多选
    public $accept = ['pdf', 'xls', 'xlsx', 'doc', 'docx', 'ppt', 'pptx', 'jpg', 'png', 'jpeg', 'webp'];
    public $id;           // 组件ID
    public $url = '/core/upload/upload'; // 上传接口
    public $name = '';    // 表单字段名（自动按模型类名生成）

    public function run()
    {
        $data = [
            'url'       => Url::create([$this->url]),
            'accept'    => Mime::get($this->accept),
            'attribute' => $this->attribute,
            'limit'     => max(1, (int)$this->limit),
            'muit'      => (bool)$this->multiple,
            'id'        => $this->id ?: 'file_uploader_' . $this->attribute,
        ];
        $this->name = basename(str_replace('\\', '/', get_class($this->model)));

        // 处理默认值
        $defaultValue = '';
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="pointer delete bi bi-trash3" viewBox="0 0 16 16"><path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5M11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47M8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5"/></svg>';
        $delete = "<div class='cover hide'>{$svg}</div>";

        $value = $this->value ?: ($this->model ? $this->model->{$this->attribute} : null);
        if ($value) {
            if ($this->limit == 1 && !$this->multiple) {
                if (!is_array($value)) {
                    $fileName = basename($value);
                    $defaultValue = "<div class=\"item\"><a href=\"{$value}\" target=\"_blank\">{$fileName}</a>{$delete}<input type=\"hidden\" name=\"{$this->name}[{$this->attribute}]\" value=\"{$value}\" ></div>";
                }
            } else {
                $values = is_array($value) ? $value : [$value];
                foreach ($values as $v) {
                    if (!is_array($v)) {
                        $fileName = basename($v);
                        $defaultValue .= "<div class=\"item\"><a href=\"{$v}\" target=\"_blank\">{$fileName}</a>{$delete}<input type=\"hidden\" name=\"{$this->name}[{$this->attribute}][]\" value=\"{$v}\" ></div>";
                    }
                }
            }
        }

        $data['defaultValue'] = $defaultValue;
        $data['svg'] = $svg;
        $data['name'] = $this->name;

        return $this->render('file', $data);
    }
}
