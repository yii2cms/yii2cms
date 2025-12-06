<?php

/**
 * 单视频、多视频上传组件
 * 一般用于商品视频、宣传视频等
 */

namespace app\modules\core\widgets;

use app\modules\core\classes\Mime;
use app\modules\core\classes\Url;
use yii\base\Widget;

class Video extends Widget
{
    public $model; // 模型实例
    public $value; // 视频值（URL 或数组） 
    public $attribute; // 模型属性名
    public $limit = 1; // 最大上传视频数量
    public $accept = ['mp4', 'avi', 'mov', 'wmv', 'webm']; // 允许的文件扩展名
    public $id; // 自定义上传器 ID
    public $multiple = false; // 是否允许多选上传（即使 limit=1）
    public $url = '/core/upload/upload'; // 默认上传地址 
    public $name = ''; // 表单字段名
    public $maxSize = 100; // 最大文件大小（MB）

    /**
     * 初始化 widget 并渲染视图
     * @return string 渲染的 HTML
     */
    public function run()
    {

        // 准备视图数据
        $data = [
            'url'       => Url::create([$this->url]),
            'accept'    => Mime::get($this->accept), // 获取 MIME 类型
            'attribute' => $this->attribute,
            'limit'     => max(1, (int)$this->limit), // 确保 limit 至少为 1
            'muit'      => (bool)$this->multiple,
            'id'        => $this->id ?: 'video_uploader_' . $this->attribute, // 默认 ID
            'maxSize'   => $this->maxSize,
        ];
        $this->name = basename(str_replace('\\', '/', get_class($this->model)));

        // 处理默认值
        $defaultValue = '';
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="pointer delete bi bi-trash3" viewBox="0 0 16 16"><path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5M11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47M8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5"/></svg>';
        $delete = "<div class='cover hide'>{$svg}</div>";

        // 视频播放图标
        $playIcon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="white" class="play-icon" viewBox="0 0 16 16"><path d="m11.596 8.697-6.363 3.692c-.54.313-1.233-.066-1.233-.697V4.308c0-.63.692-1.01 1.233-.696l6.363 3.692a.802.802 0 0 1 0 1.393z"/></svg>';

        // 获取视频值
        $value = $this->value ?: ($this->model ? $this->model->{$this->attribute} : null);

        // 处理单视频或多视频默认值
        if ($value) {
            if ($this->limit == 1 && !$this->multiple) {
                if (!is_array($value)) {
                    $defaultValue = "<div class=\"item\"><div class=\"video-preview\"><video src=\"{$value}\" preload=\"metadata\"></video><div class=\"play-overlay\">{$playIcon}</div></div>{$delete}<input type=\"hidden\" name=\"{$this->name}[{$this->attribute}]\" value=\"{$value}\" ></div>";
                }
            } else {
                $values = is_array($value) ? $value : [$value];
                foreach ($values as $v) {
                    if (!is_array($v)) {
                        $defaultValue .= "<div class=\"item\"><div class=\"video-preview\"><video src=\"{$v}\" preload=\"metadata\"></video><div class=\"play-overlay\">{$playIcon}</div></div>{$delete}<input type=\"hidden\" name=\"{$this->name}[{$this->attribute}][]\" value=\"{$v}\" ></div>";
                    }
                }
            }
        }

        $data['defaultValue'] = $defaultValue;
        $data['svg'] = $svg;
        $data['playIcon'] = $playIcon;
        $data['name'] = $this->name;

        return $this->render('video', $data);
    }
}
