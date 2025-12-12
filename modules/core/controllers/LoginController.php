<?php

/**
 * 授权请查看根目录下的LICENSE.md
 * @author ken <yiiphp@foxmail.com> 
 * @link   https://github.com/yii2cms/yii2cms 
 */

namespace app\modules\core\controllers;

use Yii;
use app\modules\core\models\User;
use app\modules\core\classes\Mail;
use app\modules\core\classes\Sms;
use app\modules\core\classes\Cookie;
use app\modules\core\classes\Log;
use yii\captcha\CaptchaAction;

class LoginController extends \app\modules\core\classes\FrontController
{

    /**
     * 发送短信验证码
     */
    protected function sendSms($phone, $data)
    {
        /**
         * 发送短信验证码
         */
        Sms::send($phone, 'login', ['code' => $data]);
    }
    /**
     * 发送邮箱验证码
     */
    protected function sendEmail($email, $code)
    {
        Mail::send($email, 'login', ['code' => $code]);
    }

    public function beforeAction($action)
    {
        parent::beforeAction($action);
        /**
         * 设置主题
         */
        $this->setTheme('admin');
        /**
         * 设置布局
         */
        $this->setLayout('/empty');
        return true;
    }

    public function actions()
    {
        return [
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'backColor' => 0xFFFFFF,  // 背景颜色
                'maxLength' => 4,         // 最大显示字符数
                'minLength' => 4,         // 最小显示字符数
                'padding'   => 5,           // 间距
                'height'    => 40,            // 高度
                'width'     => 100,           // 宽度
                'foreColor' => 0x2040A0,  // 字体颜色
                'offset'    => 4,            // 字符偏移量 

            ],
        ];
    }

    /**
     * send-phone-code
     * 发送手机号验证码
     */
    public function actionSendPhoneCode()
    {
        $phone = $this->request->post('phone') ?? '';
        if (!$phone) {
            $model = $this->currentUser;
            $phone = $model->phone;
        }
        if (!$phone) {
            return $this->asJson([
                'code' => 1,
                'msg' => Yii::t('app', '手机号不能为空'),
            ]);
        }
        // 验证图形验证码
        $res = $this->jsonCheckCaptcha('phone');
        if ($res) {
            return $res;
        }
        //cache 缓存手机号验证码
        $cache = Yii::$app->cache;
        $cache_key = 'phone_login_code_' . $phone;
        $data = mt_rand(100000, 999999);
        $cache->set($cache_key, $data, 60 * 5);
        /**
         * 发送手机号验证码
         */
        $this->sendSms($phone, $data);

        return $this->asJson([
            'code' => 0,
            'data' => is_dev() ? $data : '',
        ]);
    }

    /**
     * send-email-code
     * 发送邮箱验证码
     */
    public function actionSendEmailCode()
    {
        $type = $this->request->post('type') ?? '';
        $email = $this->request->post('email') ?? '';
        if (!$email) {
            return $this->asJson([
                'code' => 1,
                'message' => '用户邮箱不能为空',
            ]);
        }
        // 验证图形验证码
        $res = $this->jsonCheckCaptcha('email');
        if ($res) {
            return $res;
        }
        $code = rand(100000, 999999);
        $cache = Yii::$app->cache;
        $cache->set('email_login_code_' . $email, $code, 60 * 5);

        $this->sendEmail($email, $code);

        return $this->asJson([
            'code' => 0,
            'message' => '发送成功',
            'data' => [
                'email' => $email,
                'code'  => is_dev() ? $code : '',
            ],
        ]);
    }
    /**
     * 验证图形验证码
     */
    protected function jsonCheckCaptcha($type)
    {
        $captcha_code = $this->request->post('captcha_code_' . $type) ?? '';
        if (!$captcha_code) {
            return $this->asJson([
                'code' => 1,
                'message' => Yii::t('app', '图形验证码不能为空'),
            ]);
        }
        /**
         * 验证图形验证码
         */
        $captchaAction = new CaptchaAction('captcha', $this);
        $code = $captchaAction->getVerifyCode();
        if (strtolower($code) != strtolower($captcha_code)) {
            return $this->asJson([
                'code' => 1,
                'message' => Yii::t('app', '图形验证码错误'),
            ]);
        }
    }

    public function actionIndex()
    {
        $cache = Yii::$app->cache;
        $data = [];
        $login_type = Cookie::get('admin_login_type', 'account');
        if (!Yii::$app->request->isPost) {
            $tab = Yii::$app->request->get('tab');
            $allow = ['account', 'email', 'phone'];
            if ($tab && in_array($tab, $allow, true)) {
                $login_type = $tab;
            }
        }
        if (Yii::$app->request->isPost) {
            $login_type  = Yii::$app->request->post('login_type');
            $captcha_code = Yii::$app->request->post('captcha_code_' . $login_type) ?? '';

            $username = Yii::$app->request->post('username');
            $password = Yii::$app->request->post('password');


            $email = Yii::$app->request->post('email');
            $email_code = Yii::$app->request->post('email_code');


            $phone = Yii::$app->request->post('phone');
            $phone_code = Yii::$app->request->post('phone_code');

            $data['email']      = $email;
            $data['password']   = $password;
            $data['phone']      = $phone;
            $data['phone_code'] = $phone_code;
            $data['email_code'] = $email_code;
            $data['username']   = $username;
            $data['login_type']  = $login_type;
            $data['captcha_code'] = $captcha_code;

            Cookie::set('admin_login_type', $login_type, time() + 86400 * 365);

            if (!$captcha_code) {
                Yii::$app->session->setFlash('error', Yii::t('app', '图形验证码不能为空'));
                return $this->render('index', $data);
            }
            /**
             * 验证图形验证码
             */
            $captchaAction = new CaptchaAction('captcha', $this);
            // 手动验证验证码
            if (!$captchaAction->validate($captcha_code, false)) {
                Yii::$app->session->setFlash('error', Yii::t('app', '图形验证码错误'));
                return $this->render('index', $data);
            }

            /**
             * 判断有没有帐号，没有帐号先创建一个admin@msn.com 密码 111111
             */
            $count = User::find()->count();
            if ($count < 1) {
                $user = new User();
                $user->email = 'admin@msn.com';
                $user->password = 111111;
                $user->role = 'admin';
                $user->nickname = '管理员';
                $user->save();
            }
            $url = '';
            if ($login_type == 'account') {
                //邮件或手机号
                $user = User::find()->where(['email' => $username])
                    ->orWhere(['phone' => $username])
                    ->orWhere(['username' => $username])
                    ->one();
                if ($user && $user->validatePassword($password)) {
                    if ($user->status == 'disabled') {
                        Yii::$app->session->setFlash('error', Yii::t('app', '用户已被禁用'));
                        return $this->render('index', $data);
                    }
                    $user->login('电脑端', '', $user->role);
                    $url = "/core/{$user->role}/index";
                    Log::add("用户{$username}登录成功", 'info');
                    return $this->redirectAdmin([$url]);
                } else {
                    Log::add("用户{$username}登录失败，邮箱或密码错误", 'error');
                    Yii::$app->session->setFlash('error', Yii::t('app', '邮箱或密码错误'));
                }
            } else if ($login_type == 'email') {
                //邮箱验证码
                $user = User::find()->where(['email' => $email])->one();
                if ($user) {
                    $code = $cache->get('email_login_code_' . $email);
                    if (!is_local() &&  (!$code || $code != $email_code)) {
                        Yii::$app->session->setFlash('error', Yii::t('app', '邮箱验证码错误'));
                    } else {
                        if ($user->status == 'disabled') {
                            Yii::$app->session->setFlash('error', Yii::t('app', '用户已被禁用'));
                            return $this->render('index', $data);
                        }
                        $user->login('电脑端', '', $user->role);
                        $url = "/core/{$user->role}/index";
                        /**
                         * 删除cache
                         */
                        $cache->delete('email_login_code_' . $email);
                        Log::add("用户{$email}登录成功", 'info');
                        return $this->redirectAdmin([$url]);
                    }
                } else {
                    Log::add("用户{$email}登录失败，邮箱不存在", 'error');
                    Yii::$app->session->setFlash('error', Yii::t('app', '邮箱不存在'));
                }
            } else if ($login_type == 'phone') {
                //手机号验证码
                $user = User::find()->where(['phone' => $phone])->one();
                if ($user) {
                    $code = $cache->get('phone_login_code_' . $phone);
                    if (!is_local() && (!$code || $code != $phone_code)) {
                        Yii::$app->session->setFlash('error', Yii::t('app', '手机号验证码错误'));
                    } else {
                        if ($user->status == 'disabled') {
                            Yii::$app->session->setFlash('error', Yii::t('app', '用户已被禁用'));
                            return $this->render('index', $data);
                        }
                        $user->login('电脑端', '', $user->role);
                        $url = "/core/{$user->role}/index";
                        /**
                         * 删除cache
                         */
                        $cache->delete('phone_login_code_' . $phone);
                        Log::add("用户{$phone}登录成功", 'info');
                        return $this->redirectAdmin([$url]);
                    }
                } else {
                    Log::add("用户{$phone}登录失败，手机号不存在", 'error');
                    Yii::$app->session->setFlash('error', Yii::t('app', '手机号不存在'));
                }
            }
        }

        $data['login_type'] = $login_type;
        return $this->render('index', $data);
    }
}
