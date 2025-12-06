<?php

/**
 * 授权请查看根目录下的LICENSE.md
 * @author ken <yiiphp@foxmail.com> 
 * @link   https://github.com/yii2cms/yii2cms 
 */

namespace app\modules\core\classes;

use Yii;
use yii\widgets\ActiveForm as BaseActiveForm;

/**
 * yii\widgets\ActiveForm
 */
class ActiveForm extends BaseActiveForm
{
    /**
     * 重写field方法，根据模型是否启用多语言，判断是否需要添加语言后缀
     */
    public function field($model, $attribute, $options = [])
    {
        $allow = $model->getAllowLanguageFields() ?? [];
        if ($allow) {
            if (in_array($attribute, $allow)) {
                return parent::field($model, $attribute, $options);
            } else {
                return Yii::createObject(EmptyActiveField::class, [
                    $model,
                    $attribute,
                    $this
                ]);
            }
        }
        return parent::field($model, $attribute, $options);
    }

    /**
     * 重写checkbox渲染方法
     */
    public function checkbox($model, $attribute, $options = [])
    {
        $defaultOptions = [
            'template' => "<div class=\"form-check\">{input} {label}</div>\n<div class=\"invalid-feedback d-flex align-items-center\">\u{26A0} {error}</div>",
            'labelOptions' => ['class' => 'form-check-label'],
            'inputOptions' => ['class' => 'form-check-input']
        ];
        $options = array_merge($defaultOptions, $options);
        return parent::checkbox($model, $attribute, $options);
    }
}


class EmptyActiveField
{
    /**
     * 构造函数接收参数但不做任何事
     */
    public function __construct($model = null, $attribute = null, $form = null, $config = [])
    {
        // 什么都不做，只是接收参数
    }

    /**
     * 设置属性 - 忽略所有
     */
    public function __set($name, $value)
    {
        return;
    }

    /**
     * 获取属性 - 返回自身
     */
    public function __get($name)
    {
        return $this;
    }

    /**
     * 调用方法 - 返回自身
     */
    public function __call($name, $params)
    {
        return $this;
    }

    /**
     * 转换为字符串 - 返回空
     */
    public function __toString()
    {
        return '';
    }
}
