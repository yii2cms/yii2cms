<?php

/**
 * 单图上传组件
 * 一般用户头像上传
 */

namespace app\modules\core\widgets;

use app\modules\core\classes\Mime;
use app\modules\core\classes\Url;
use yii\base\Widget;

class ImageOneButton extends Widget
{
    public $targetInput = "#image-url";
    public $model; // 模型实例
    public $value; // 图片值（URL 或数组） 
    public $attribute; // 模型属性名
    public $limit = 1; // 最大上传图片数量
    public $accept = ['jpg', 'png', 'jpeg', 'webp']; // 允许的文件扩展名
    public $id; // 自定义上传器 ID
    public $multiple = false; // 是否允许多选上传（即使 limit=1）
    public $url = '/core/upload/upload'; // 默认上传地址 
    public $name = ''; // 表单字段名 

    public function run()
    {
        // 准备视图数据
        $data = [
            'url'       => Url::create([$this->url]),
            'accept'    => Mime::get($this->accept), // 获取 MIME 类型
            'attribute' => $this->attribute,
            'limit'     => max(1, (int)$this->limit), // 确保 limit 至少为 1
            'muit'      => (bool)$this->multiple,
            'id'        => $this->id ?: 'uploader_' . $this->attribute, // 默认 ID
        ];
        $this->name = basename(str_replace('\\', '/', get_class($this->model)));
        $data['name'] = $this->name;
        $data['targetInput'] = $this->targetInput;
        return $this->render('image_one_button', $data);
    }
}
