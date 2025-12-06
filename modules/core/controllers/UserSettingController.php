<?php

/**
 * 授权请查看根目录下的LICENSE.md
 * @author ken <yiiphp@foxmail.com> 
 * @link   https://github.com/yii2cms/yii2cms 
 */

namespace app\modules\core\controllers;

use Yii;
use app\modules\core\classes\Mail;
use app\modules\core\classes\Sms;

class UserSettingController extends \app\modules\core\classes\LoginController
{
    /**
     * 发送短信验证码
     */
    protected function sendSms($phone, $data)
    {
        /**
         * 发送短信验证码
         */
        Sms::send($phone, 'bind_change', ['code' => $data]);
    }
    /**
     * 发送邮箱验证码
     */
    protected function sendEmail($email, $code)
    {
        Mail::send($email, 'bind_change', ['code' => $code]);
    }
    /**
     * 用户设置首页
     */
    public function actionIndex()
    {
        $model = $this->currentUser;
        $model->scenario = 'self';
        $model->password = '';
        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                Yii::$app->session->setFlash('success', '设置保存成功');
                return $this->refresh();
            }
        }
        return $this->render('index', [
            'model' => $model,
        ]);
    }
    /**
     * update-avatar
     * 更新用户头像
     */
    public function actionUpdateAvatar()
    {
        $model = $this->currentUser;
        $model->scenario = 'updateAvatar';
        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                Yii::$app->session->setFlash('success', '头像更新成功');
                return $this->refresh();
            }
        }
        return $this->render('update-avatar', [
            'model' => $model,
        ]);
    }
    /**
     * set-phone
     * 设置手机号
     */
    public function actionSetPhone()
    {
        $model = $this->currentUser;
        $model->scenario = 'setPhone';
        if ($model->phone) {
            Yii::$app->session->setFlash('error', '手机号已设置');
            return $this->redirectAdmin(['index']);
        }
        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                Yii::$app->session->setFlash('success', '手机号设置成功');
                /**
                 * 删除cache
                 */
                $cache = Yii::$app->cache;
                $cache->delete('phone_code_' . $model->phone);
                return $this->redirectAdmin(['index']);
            }
        }
        return $this->render('set-phone', [
            'model' => $model,
        ]);
    }
    /**
     * update-phone
     * 更新手机号
     */
    public function actionUpdatePhone()
    {
        $model = $this->currentUser;
        $model->scenario = 'updatePhone';
        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                Yii::$app->session->setFlash('success', '手机号更新成功');
                /**
                 * 删除cache
                 */
                $cache = Yii::$app->cache;
                $cache->delete('phone_code_' . $model->phone);
                return $this->redirectAdmin(['index']);
            }
        }
        return $this->render('update-phone', [
            'model' => $model,
        ]);
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
                'msg' => '手机号不能为空',
            ]);
        }
        //cache 缓存手机号验证码
        $cache = Yii::$app->cache;
        $cache_key = 'phone_code_' . $phone;
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
     * change-password
     * 修改密码
     */
    public function actionChangePassword()
    {
        $model = $this->currentUser;
        $model->scenario = 'changePassword';
        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                Yii::$app->session->setFlash('success', '密码修改成功');
                return $this->redirectAdmin(['index']);
            }
        }
        return $this->render('change-password', [
            'model' => $model,
        ]);
    }
    /**
     * set-email
     * 设置邮箱
     */
    public function actionSetEmail()
    {
        $model = $this->currentUser;
        $model->scenario = 'setEmail';
        if ($model->email) {
            Yii::$app->session->setFlash('error', '邮箱已设置');
            return $this->redirectAdmin(['index']);
        }
        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                Yii::$app->session->setFlash('success', '邮箱设置成功');
                /**
                 * 删除cache
                 */
                $cache = Yii::$app->cache;
                $cache->delete('email_code_' . $model->email);
                return $this->redirectAdmin(['index']);
            }
        }
        return $this->render('set-email', [
            'model' => $model,
        ]);
    }
    /**
     * update-email
     * 修改邮箱
     */
    public function actionUpdateEmail()
    {
        $model = $this->currentUser;
        $model->scenario = 'updateEmail';
        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                Yii::$app->session->setFlash('success', '邮箱修改成功');
                /**
                 * 删除cache
                 */
                $cache = Yii::$app->cache;
                $cache->delete('email_code_' . $model->email);
                return $this->redirectAdmin(['index']);
            }
        }
        return $this->render('update-email', [
            'model' => $model,
        ]);
    }
    /**
     * send-email-code
     * 发送邮箱验证码
     */
    public function actionSendEmailCode()
    {
        $type = $this->request->post('type') ?? '';
        if ($type == 'current') {
            $model = $this->currentUser;
            $email = $model->email;
        } else {
            $email = $this->request->post('email') ?? '';
        }

        if (!$email) {
            return $this->asJson([
                'code' => 1,
                'message' => '用户邮箱不能为空',
            ]);
        }
        $code = rand(100000, 999999);
        $cache = Yii::$app->cache;
        $cache->set('email_code_' . $email, $code, 60 * 5);

        $this->sendEmail($email, $code);

        return $this->asJson([
            'code' => 0,
            'message' => '发送成功',
            'data' => [
                'email' => $email,
                'code' => is_dev() ? $code : '',
            ],
        ]);
    }
}
