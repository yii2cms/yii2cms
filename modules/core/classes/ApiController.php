<?php

/**
 * 授权请查看根目录下的LICENSE.md
 * @author ken <yiiphp@foxmail.com> 
 * @link   https://github.com/yii2cms/yii2cms 
 */

namespace app\modules\core\classes;

use Yii;

/**
 * Api控制器 
 *  
 */
class ApiController extends BaseController
{
    /**
     * 是否需要登录
     */
    protected $needLogin = true;
    /**
     * 当前用户
     * 取值时需要->id ->phone，对应User模型
     */
    protected $currentUser;
    /**
     * 请求头中的Bearer
     */
    protected $header_bearer;
    /**
     * 忽略csrf验证
     */
    public $enableCsrfValidation = false;
    /**
     * 是否已登录
     */
    protected $isLogin = false;
    /**
     * 当前登录的用户ID
     */
    protected $uid = 0;
    /**
     * 检测role
     */
    protected $checkRole = false;
    /**
     * 预处理
     */
    public function beforeAction($action)
    {
        global $uid;
        Env::cross();
        parent::beforeAction($action);
        $this->header_bearer = Env::getBearer();
        /**
         * 检查登录
         */
        $this->loginWithHeader();
        /**
         * 检查登录
         */
        if ($this->needLogin) {
            if (Yii::$app->user->isGuest) {
                echo json_encode(['code' => 403, 'msg' => Yii::t('app', '未登录')]);
                exit;
            }
        }
        /**
         * 设置当前用户
         */
        $id = Yii::$app->user->identity->id ?? 0;
        if ($id) {
            $this->currentUser = get_user($id);
            if (!$this->currentUser) {
                echo json_encode(['code' => 403, 'msg' => Yii::t('app', '您没有权限执行此操作')]);
                exit;
            }
        }
        $this->uid = $this->currentUser->id ?? 0;
        if ($this->uid > 0) {
            $this->isLogin = true;
        }
        $uid = $this->uid;
        return true;
    }
    /**
     * 检查登录
     */
    protected function loginWithHeader()
    {
        if (!$this->header_bearer) {
            return false;
        }
        $id = User::decodeToken($this->header_bearer);
        if (!$id) {
            return false;
        }
        $user = get_user($id);
        if (!$user) {
            return false;
        }
        $platform = $this->getPost('platform', '微信小程序');
        $user->userLogin($platform, $this->header_bearer);
        return true;
    }
}
