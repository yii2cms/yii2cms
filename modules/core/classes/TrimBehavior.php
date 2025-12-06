<?php

/**
 * 授权请查看根目录下的LICENSE.md
 * @author ken <yiiphp@foxmail.com> 
 * @link   https://github.com/yii2cms/yii2cms 
 */
/**
 * 自动去除字符串属性的空格 
 */

namespace app\modules\core\classes;

use yii\base\Behavior;
use yii\db\ActiveRecord;

class TrimBehavior extends Behavior
{
    // 指定要修剪的属性（留空则修剪所有字符串属性）
    public $attributes = [];

    // 指定不修剪的属性
    public $excludeAttributes = [];

    /**
     * 定义事件绑定，修剪操作在插入或更新前触发
     * @return array 事件映射
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'trimAttributes',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'trimAttributes',
        ];
    }

    /**
     * 修剪模型的属性值
     * @param \yii\base\Event $event 事件对象
     */
    public function trimAttributes($event)
    {
        // 如果未指定属性，则修剪所有模型属性
        $attributes = $this->attributes ?: array_keys($this->owner->attributes);

        foreach ($attributes as $attribute) {
            // 跳过排除的属性
            if (in_array($attribute, $this->excludeAttributes)) {
                continue;
            }

            $value = $this->owner->$attribute;

            if ($value !== null) {
                // 对数组递归修剪，对字符串或数字执行 trim
                $this->owner->$attribute = $this->trimValue($value);
            }
        }
    }

    /**
     * 递归修剪值，支持字符串、数字和数组
     * @param mixed $value 输入值
     * @return mixed 修剪后的值
     */
    protected function trimValue($value)
    {
        if (is_array($value)) {
            // 递归修剪数组中的每个元素
            return array_map([$this, 'trimValue'], $value);
        } elseif (is_string($value) || is_numeric($value)) {
            // 对字符串或数字执行 trim
            return trim((string)$value);
        }
        // 其他类型（如 null、对象）保持不变
        return $value;
    }
}
