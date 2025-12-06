<?php

/**
 * 授权请查看根目录下的LICENSE.md
 * @author ken <yiiphp@foxmail.com> 
 * @link   https://github.com/yii2cms/yii2cms 
 */

namespace app\modules\core\classes;

use Yii;
use yii\base\Event;
use app\modules\core\models\DataHis;

/**
 * 管理员控制器 
 */
class AdminController extends BaseController
{
    /**
     * 忽略权限检查的action
     */
    protected $ignoreAcl = [];
    /**
     * 是否只检查用户登录,不检测权限
     */
    protected $justCheckUserLogin = false;
    /**
     * 检测role
     */
    protected $checkRole = true;
    /**
     * 全局钩子事件
     */
    const EVENT_BEFORE_ACTION = 'globalBeforeAction';
    /**
     * 主题
     */
    public $theme = 'admin';
    /**
     * 登录url
     */
    public $loginUrl = '/core/login';
    /**
     * 角色
     */
    public $role = 'admin';
    /**
     * 当前用户
     */
    protected $currentUser;
    /**
     * uid
     */
    protected $uid = 0;

    /**
     * shopId
     */
    protected $shop_id = null;
    /**
     * shop
     */
    protected $shop = null;

    public function beforeAction($action)
    {
        global $uid;
        parent::beforeAction($action);
        if ($this->theme) {
            cookie('theme', $this->theme, 86400 * 365);
            /**
             * 设置主题
             */
            $this->setTheme($this->theme);
            /**
             * 设置布局
             */
            $this->setLayout('/main');
        }
        $isLogin = $this->checkIsLogin();
        if (!$isLogin) {
            header('Location: ' . $this->loginUrl);
            exit;
        }
        /**
         * 设置当前用户
         */
        $this->currentUser = User::findOne($this->uid);
        if (!$this->currentUser) {
            throw new \NewException(Yii::t('app', '用户不存在'));
            return;
        }
        $this->uid = $this->currentUser->id;
        $uid = $this->uid;
        /**
         * 检查角色
         */
        if ($this->checkRole) {
            $isRightRole = false;
            if (is_array($this->role)) {
                foreach ($this->role as $role) {
                    if ($role == $this->currentUser->role) {
                        $isRightRole = true;
                        break;
                    }
                }
            } else {
                if ($this->role == $this->currentUser->role) {
                    $isRightRole = true;
                }
            }
            if (!$isRightRole) {
                throw new \NewException(Yii::t('app', '您没有权限执行此操作'));
                return;
            }
        }
        /**
         * 设置当前菜单
         */
        Menu::setActive($this->module->id . '/' . $this->id);
        /**
         * 触发全局钩子事件
         */
        $event = new Event(['data' => ['action' => $action, 'controller' => $this]]);
        $this->trigger(self::EVENT_BEFORE_ACTION, $event);

        /**
         * 检查权限
         */
        if (in_array($action->id, $this->ignoreAcl)) {
            return true;
        }
        /**
         * 加载商家信息
         */
        $this->loadShipInfo();
        /**
         * 检查用户登录
         */
        if ($this->justCheckUserLogin) {
            return true;
        }
        /**
         * 检查权限
         */
        if (!$this->checkAcl()) {
            echo '您没有权限执行此操作';
            exit;
            return false;
        }

        return true;
    }
    /**
     * 检查登录
     */
    protected function checkIsLogin()
    {
        /**
         * 检查登录
         */
        $roles = $this->role;
        if (!is_array($roles)) {
            $roles = [$roles];
        }
        $isLogin = false;
        $this->uid = 0;
        foreach ($roles as $role) {
            if (Yii::$app->$role->isGuest) {
                continue;
            } else {
                $isLogin = true;
                $this->uid = Yii::$app->$role->identity->id;
            }
        }
        return $isLogin;
    }
    /**
     * 检查权限
     */
    protected function checkAcl()
    {
        $str = '/' . $this->actions['module'] . '/' . $this->actions['controller'] . '/' . $this->actions['action'];
        $roles = $this->role;
        if (!is_array($roles)) {
            $roles = [$roles];
        }
        foreach ($roles as $role) {
            if (check_acl($str, $role)) {
                return true;
            }
        }
        return false;
    }
    /**
     * 获取数据历史记录
     */
    public function getHis($table_name, $table_id)
    {
        $model = DataHis::find()->where([
            'table_name' => $table_name,
            'table_id' => $table_id,
        ])->orderBy(['id' => SORT_DESC])->all();
        return $model;
    }

    /**
     * 加载shop信息
     */
    protected function loadShipInfo()
    {
        global $shop_id, $shop;
        if (!class_exists('\app\modules\shop\models\Shop')) {
            return;
        }
        $shop = \app\modules\shop\models\Shop::findOne(['user_id' => $this->uid]);
        $this->shop_id = $shop_id = $shop->id ?? 0;
        $this->shop = $shop;
    }
}
