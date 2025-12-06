<?php

/**
 * 授权请查看根目录下的LICENSE.md
 * @author ken <yiiphp@foxmail.com> 
 * @link   https://github.com/yii2cms/yii2cms 
 */

namespace app\modules\core\classes;

use Yii;

/**
 * 基础控制器 
 */
class BaseController extends \yii\web\Controller
{

    /**
     * 请求
     */
    protected $actions = [];
    /**
     * beforeAction
     */
    public function beforeAction($action)
    {
        parent::beforeAction($action);
        global $request_actions;
        /**
         * 设置cache key前缀
         */
        if (Env::isCli()) {
            Yii::$app->cache->keyPrefix = 'console';
        } else {
            $host = Yii::$app->request->getHostName();
            Yii::$app->cache->keyPrefix = md5($host) . ".";
        }
        $this->actions = [
            'module' => Yii::$app->controller->module->id,
            'controller' => Yii::$app->controller->id,
            'action' => Yii::$app->controller->action->id,
        ];
        $request_actions = $this->actions;
        /**
         * 取 GET中的 change-language 参数
         */
        $lang = Yii::$app->request->get('change-language') ?? '';
        if ($lang) {
            cookie('language', $lang, 3600 * 24 * 7);
            Yii::$app->language = $lang;
        } else {
            $lang = cookie('language');
            if ($lang) {
                Yii::$app->language = $lang;
            }
        }
        /**
         * 返回true才能继续执行
         */
        return true;
    }

    /**
     * 设置主题
     * @param string $name 主题名称
     */
    public function setTheme($name)
    {
        $this->view->theme = Yii::$app->getView()->theme;
        $this->view->theme->basePath = Yii::getAlias('@app/themes/' . $name);
        $this->view->theme->baseUrl = Yii::getAlias('@web/themes/' . $name);
        $this->view->theme->pathMap = [
            '@app/views' => '@app/themes/' . $name,
            '@app/modules' => '@app/themes/' . $name . '/modules',
            '@app/widgets' => '@app/themes/' . $name . '/widgets',
        ];
    }
    /**
     * 设置布局
     * @param string $name 布局名称
     */
    public function setLayout($name)
    {
        $this->layout = $name;
    }

    /**
     * 添加flash消息
     * @param string $type 消息类型
     * @param string $message 消息内容
     */
    public function addFlash($type, $message)
    {
        Yii::$app->session->addFlash($type, $message);
    }
    /**
     * 重定向
     * @param string $url 重定向地址
     * @param int $statusCode 状态码
     */
    public function redirect($url = [], $withLang = true, $statusCode = 302)
    {
        $default = \Yii::$app->request->queryParams ?? [];
        if (!$withLang && isset($default['lang'])) {
            unset($default['lang']);
        }
        if (!is_array($url)) {
            $url = [$url];
        }
        $url = array_merge($default, $url);
        return parent::redirect($url, $statusCode);
    }
    /**
     * 后台跳转
     */
    public function redirectAdmin($url = [], $statusCode = 302)
    {
        return $this->redirect($url, false, $statusCode);
    }
    /**
     * 获取POST参数
     */
    public function getPost($key = '', $default = '')
    {
        $input = Env::getInput();
        $post  = Env::getPost();
        if ($key) {
            $value = array_get($input, $key, '');
            if ($value) {
                return $value;
            }
            $value = array_get($post, $key, '');
            if ($value) {
                return $value;
            }
            return $default;
        } else {
            if (is_array($input)) {
                return $input;
            }
            return $post;
        }
    }
    /**
     * 获取所有参数
     */
    public function getAll($key = '', $default = '')
    {
        return Env::getAll($key) ?: $default;
    }
    /**
     * json 成功消息 
     * @param string $msg 消息内容 如为数组直接返回json
     * @param array $data 数据
     */
    public function success($msg = '', $data = [])
    {
        if (is_array($msg)) {
            $msg['code'] = 0;
            return $this->asJson($msg);
        }
        return $this->asJson([
            'code' => 0,
            'msg'  => $msg,
            'data' => $data,
        ]);
    }
    /**
     * json 错误消息
     * @param string $msg 消息内容
     * @param int $code 错误码
     */
    public function error($msg = '', $code = 250)
    {
        return $this->asJson([
            'code' => $code,
            'msg' => $msg,
            'data' => [],
        ]);
    }
}
