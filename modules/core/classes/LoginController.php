<?php

/**
 * 授权请查看根目录下的LICENSE.md
 * @author ken <yiiphp@foxmail.com> 
 * @link   https://github.com/yii2cms/yii2cms 
 */

namespace app\modules\core\classes;

class LoginController extends AdminController
{
    public $theme = '';
    /**
     * 角色
     */
    public $role = ['shop', 'user', 'admin'];
    /**
     * 只检测是否登录了
     */
    protected $justCheckUserLogin = true;
    public function beforeAction($action)
    {
        parent::beforeAction($action);
        $theme = cookie('theme');
        if (!$theme) {
            $theme = $this->theme;
        }
        $this->setTheme($theme);
        return true;
    }
}
