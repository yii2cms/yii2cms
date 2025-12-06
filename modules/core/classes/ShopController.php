<?php

/**
 * 授权请查看根目录下的LICENSE.md
 * @author ken <yiiphp@foxmail.com> 
 * @link   https://github.com/yii2cms/yii2cms 
 */

namespace app\modules\core\classes;

use Yii;
use yii\base\Event;
use app\modules\shop\models\Shop;

/**
 * 商家控制器 
 */
class ShopController extends AdminController
{
    /**
     * 是否只检查用户登录,不检测权限
     */
    protected $justCheckUserLogin = true;
    /**
     * 主题
     */
    public $theme = 'shop';
    /**
     * 登录url
     */
    public $loginUrl = '/core/login';
    /**
     * 角色
     */
    public $role = 'shop';

    public function beforeAction($action)
    {
        parent::beforeAction($action);
        $this->loadShipInfo();
        if (!$this->shop_id) {
            throw new \NewException(Yii::t('app', '商家不存在'));
        }
        if ($this->shop->status != 'active') {
            throw new \NewException(Yii::t('app', '商家未审核通过'));
        }
        return true;
    }
}
