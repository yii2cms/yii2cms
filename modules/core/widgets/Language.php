<?php

namespace app\modules\core\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Url;
use app\modules\core\classes\Language as LanguageHelper;

class Language extends \yii\base\Widget
{
    public function run()
    {
        $allLanguages = LanguageHelper::getAllLanguageCode();
        $currentLanguage = Yii::$app->language;
        if (!LanguageHelper::isEnable()) {
            return '';
        } else {
            if (!$allLanguages) {
                LanguageHelper::init();
            }
        }


        // 获取当前语言信息
        $currentName = $allLanguages[$currentLanguage]['name'] ?? $currentLanguage;

        // 生成唯一ID用于Bootstrap dropdown
        $dropdownId = 'language-dropdown-' . $this->getId();

        $url = Yii::$app->controller->module->id . '/' . Yii::$app->controller->id . '/' . Yii::$app->controller->action->id;
        // 生成下拉菜单项
        $items = [];
        foreach ($allLanguages as $code => $language) {
            $isActive = $code === $currentLanguage;
            $items[] = Html::a(
                Html::tag('span', '', [
                    'class' => 'language-badge me-2'
                ]) . $language['name'],
                url($url, ['change-language' => $code]),
                [
                    'class' => 'dropdown-item' . ($isActive ? ' active' : ''),
                    'data-method' => 'post'
                ]
            );
        }

        return Html::tag(
            'div',
            // 触发器 - 使用Bootstrap5正确的data属性
            Html::button(
                Html::tag('span', $currentName, ['class' => 'current-badge me-1']) .

                    Html::tag('span', '', ['class' => 'dropdown-arrow ms-1']),
                [
                    'class' => 'language-trigger btn btn-light dropdown-toggle',
                    'type' => 'button',
                    'id' => $dropdownId,
                    'data-bs-toggle' => 'dropdown',
                    'aria-expanded' => 'false'
                ]
            ) .
                // 下拉菜单 - 使用正确的Bootstrap5类
                Html::tag(
                    'div',
                    implode('', $items),
                    [
                        'class' => 'dropdown-menu dropdown-menu-end',
                        'aria-labelledby' => $dropdownId
                    ]
                ),
            ['class' => 'dropdown language-selector']
        );
    }
}
