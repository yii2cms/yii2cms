<?php

/**
 * 授权请查看根目录下的LICENSE.md
 * @author ken <yiiphp@foxmail.com> 
 * @link   https://github.com/yii2cms/yii2cms 
 */

namespace app\modules\core\classes;

use Yii;
use app\modules\core\models\User as UserModel;

/**
 * 用户
 */
class User
{
    public static $currentRole;
    /**
     * 解密token
     * @param string $token token
     * @return int|null 用户ID
     */
    public static function decodeToken($token)
    {
        try {
            $key = Yii::$app->params['api_create_token_key'];
            $data = base64_decode($token);
            $data = Yii::$app->security->decryptByKey($data, $key);
            list($id,) = explode('|', $data);
            return $id;
        } catch (\NewException $e) {
            Yii::error("Token 解密失败: " . $e->getMessage());
            return null;
        }
    }
    /**
     * 生成接口需要用的token
     * @param UserModel $user 用户模型
     * @return string token
     */
    public static function createToken($user)
    {
        $key = Yii::$app->params['api_create_token_key'];
        $data = $user->id . '|' . Yii::$app->security->generateRandomString(16);
        return base64_encode(Yii::$app->security->encryptByKey($data, $key));
    }
    /**
     * 根据手机号创建用户
     * @param string $phone 手机号
     * @return UserModel
     */
    public static function createByPhone($phone)
    {
        $user = UserModel::find()->where(['phone' => $phone])->one();
        if ($user) {
            return $user;
        }
        $user = new UserModel();
        $user->scenario = 'phone';
        $user->phone = $phone;
        $user->save();
        return $user;
    }
    /**
     * 根据邮箱创建用户
     * @param string $email 邮箱
     * @return UserModel
     */
    public static function createByEmail($email)
    {
        $user = UserModel::find()->where(['email' => $email])->one();
        if ($user) {
            return $user;
        }
        $user = new UserModel();
        $user->scenario = 'email';
        $user->email = $email;
        $user->save();
        return $user;
    }
    /**
     * 当前登录的用户信息
     */
    public static function getCurrent($role = 'admin')
    {
        $id = self::getId($role);
        if (!$id) {
            return null;
        }
        $model =  self::findOne($id);
        if (!$model) {
            throw new \NewException(Yii::t('app', '用户不存在'));
        }
        if ($model->status == 'disabled') {
            throw new \NewException(Yii::t('app', '用户已被禁用'));
        }
        return $model;
    }
    /**
     * 根据ID查找用户
     * @param int $id 用户ID
     * @return UserModel|null
     */
    public static function findOne($id)
    {
        $user = UserModel::find()->where(['id' => $id])->one();
        return $user;
    }
    /**
     * 用户acl
     * @param int $id 用户ID
     * @return array
     */
    public static function getAcl($id, $role = 'admin')
    {
        $user = self::findOne($id);
        if (!$user) {
            return [];
        }
        self::$currentRole = $user->role;
        if ($user->role != $role) {
            return [];
        }
        $acls = $user->acls ?? [];
        if ($acls) {
            foreach ($acls as $acl) {
                $url = $acl->url;
                if (strpos($url, ',')) {
                    $urls = explode(',', $url);
                    foreach ($urls as $url) {
                        $value[] = trim($url);
                    }
                } else {
                    $value[] = trim($url);
                }
            }
        }
        return $value ?? [];
    }
    /**
     * 获取登录用户信息
     * @return identity
     */
    public static function getLoginInfo($role = 'admin')
    {
        return Yii::$app->$role->identity ?? null;
    }
    /**
     * 获取用户ID
     * @return int
     */
    public static function getId($role = 'admin')
    {
        return self::getLoginInfo($role)->id ?? 0;
    }
}
