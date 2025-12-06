<?php

/**
 * 授权请查看根目录下的LICENSE.md
 * @author ken <yiiphp@foxmail.com> 
 * @link   https://github.com/yii2cms/yii2cms 
 */

namespace app\modules\core\classes;

/**
 * 前端控制器 
 */
class FrontController extends BaseController
{

    public function beforeAction($action)
    {
        parent::beforeAction($action);
        /**
         * 设置主题
         */
        $this->setTheme('default');
        /**
         * 设置布局
         */
        $this->setLayout('/main');
        return true;
    }
}
