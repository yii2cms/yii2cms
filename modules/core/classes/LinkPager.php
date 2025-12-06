<?php

/**
 * 授权请查看根目录下的LICENSE.md
 * @author ken <yiiphp@foxmail.com> 
 * @link   https://github.com/yii2cms/yii2cms 
 */

namespace app\modules\core\classes;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * yii\widgets\LinkPager
 */
class LinkPager extends \yii\widgets\LinkPager
{
    public $url;

    /**
     * Bootstrap 5 样式分页器
     */
    public $options = ['class' => 'pagination justify-content-center'];
    public $linkContainerOptions = ['class' => 'page-item'];
    public $linkOptions = ['class' => 'page-link'];
    public $activePageCssClass = 'active';
    public $disabledPageCssClass = 'disabled';
    public $pageCssClass = 'page-item';
    public $disabledListItemSubTagOptions = ['class' => 'page-link'];

    /**
     * 渲染分页按钮 - Bootstrap 5 样式
     * @param string $label 按钮标签
     * @param int $page 页码
     * @param string $class 按钮类名
     * @param bool $disabled 是否禁用
     * @param bool $active 是否激活
     * @return string
     */
    protected function renderPageButton($label, $page, $class, $disabled, $active)
    {
        $options = $this->linkContainerOptions;
        $linkWrapTag = ArrayHelper::remove($options, 'tag', 'li');

        // 添加 Bootstrap 5 的 page-item 类
        Html::addCssClass($options, 'page-item');

        if ($active) {
            Html::addCssClass($options, 'active');
        }
        if ($disabled) {
            Html::addCssClass($options, 'disabled');
            $disabledItemOptions = $this->disabledListItemSubTagOptions;
            $tag = ArrayHelper::remove($disabledItemOptions, 'tag', 'span');
            Html::addCssClass($disabledItemOptions, 'page-link');

            return Html::tag($linkWrapTag, Html::tag($tag, $label, $disabledItemOptions), $options);
        }

        $linkOptions = $this->linkOptions;
        $linkOptions['data-page'] = $page;
        Html::addCssClass($linkOptions, 'page-link');
        $url = $this->createUrl($page);
        return Html::tag($linkWrapTag, Html::a($label, $url, $linkOptions), $options);
    }

    /**
     * 自定义创建 URL 的方法。
     * @param integer $page 页码。
     * @return string 自定义的 URL。
     */
    protected function createUrl($page)
    {
        $params = $this->pagination->params;
        $params[$this->pagination->pageParam] = $page + 1;
        $url = $this->url ?: Env::getQueryUri(3);
        return Url::create($url, $params);
    }
}
