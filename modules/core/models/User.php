<?php

namespace app\modules\core\models;

use Yii;
use yii\web\IdentityInterface;
use yii\db\ActiveRecord;
use app\modules\core\classes\Log;

class User extends ActiveRecord implements IdentityInterface
{
    public $acl;

    public $old_password;
    public $new_password;
    public $password_repeat;

    public $email_code;
    public $new_email;
    public $new_email_code;


    public $new_phone_code;
    public $phone_code;
    public $new_phone;

    public static function tableName()
    {
        return 'user';
    }

    public function rules()
    {
        return [
            [['phone'], 'match', 'pattern' => '/^1[3-9]\d{9}$/', 'message' => '请输入有效的手机号码'],
            [['avatar', 'email', 'phone', 'acl', 'role', 'status'], 'safe'],
            [['username', 'phone', 'password'], 'required', 'on' => 'create'],
            [['username', 'phone'], 'required', 'on' => 'update'],
            [['username', 'phone'], 'required', 'on' => 'self'],
            //username,phone,email唯一
            ['username', 'unique', 'message' => '用户名已存在'],
            ['phone', 'unique', 'message' => '手机号已存在'],
            ['email', 'unique', 'message' => '邮箱已存在'],
            /**
             * 修改密码
             */
            [['old_password', 'new_password', 'password_repeat'], 'required', 'on' => 'changePassword'],
            [['password_repeat'], 'compare', 'compareAttribute' => 'new_password', 'on' => 'changePassword'],
            [['old_password'], 'validateOldPassword', 'on' => 'changePassword'],
            /**
             * 修改邮箱
             */
            [['email_code', 'new_email_code', 'new_email'], 'required', 'on' => 'updateEmail',],
            [['email_code'], 'validateEmailCode', 'on' => 'updateEmail',],
            [['new_email'], 'email', 'on' => 'updateEmail'],
            [['new_email_code'], 'validateNewEmailCode', 'on' => 'updateEmail'],
            /**
             * 设置邮箱
             */
            [['new_email'], 'required', 'on' => 'setEmail'],
            [['new_email'], 'email', 'on' => 'setEmail'],
            [['new_email_code'], 'validateNewEmailCode', 'on' => 'setEmail'],

            /**
             * 设置手机号
             */
            [['new_phone'], 'required', 'on' => 'setPhone'],
            [['new_phone'], 'match', 'pattern' => '/^1[3-9]\d{9}$/', 'message' => Yii::t('app', '请输入有效的手机号码')],
            [['new_phone_code'], 'required', 'on' => 'setPhone'],
            [['new_phone_code'], 'validateNewPhoneCode', 'on' => 'setPhone'],

            /**
             * 更新手机号
             */
            [
                ['phone_code', 'new_phone', 'new_phone_code'],
                'required',
                'on' => 'updatePhone',
                'except' => ['avatar'],
            ],
            [['phone_code'], 'validatePhoneCode', 'on' => 'updatePhone'],
            [
                ['new_phone'],
                'unique',
                'message' => Yii::t('app', '手机号已存在'),
                'on' => 'updatePhone',
                //new_phone对应的是数据库中的phone字段
                'targetAttribute' => 'phone',
            ],
            [['new_phone_code'], 'validateNewPhoneCode', 'on' => 'updatePhone'],

            /**
             * updateAvatar
             */
            [['avatar'], 'required', 'on' => 'updateAvatar',],

            /**
             * 场景为纯手机号
             */
            [['phone'], 'required', 'on' => 'phone'],
            [['email'], 'required', 'on' => 'email'],

        ];
    }
    /**
     * 验证原手机号
     */
    public function validatePhoneCode($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $cache = Yii::$app->cache;
            $code = $cache->get('phone_code_' . $this->phone);
            if (!$code || $code != $this->phone_code) {
                $this->addError($attribute, Yii::t('app', '手机号验证码错误'));
            }
        }
    }
    /**
     * 验证手机号验证码
     */
    public function validateNewPhoneCode($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $cache = Yii::$app->cache;
            $code = $cache->get('phone_code_' . $this->new_phone);
            //新手机号与原手机号不能相同
            if ($this->new_phone == $this->phone) {
                $this->addError($attribute, Yii::t('app', '新手机号不能与原手机号相同'));
            }
            if (!$code || $code != $this->new_phone_code) {
                $this->addError($attribute, Yii::t('app', '手机号验证码错误'));
            }
        }
    }
    /**
     * 验证邮箱验证码
     */
    public function validateEmailCode($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $cache = Yii::$app->cache;
            $code = $cache->get('email_code_' . $this->email);
            if (!$code || $code != $this->email_code) {
                $this->addError($attribute, Yii::t('app', '原邮箱验证码错误'));
            }
        }
    }
    /**
     * 验证新邮箱验证码
     */
    public function validateNewEmailCode($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $cache = Yii::$app->cache;
            $code = $cache->get('email_code_' . $this->new_email);
            //新邮件与原邮件不能相同
            if ($this->new_email == $this->email) {
                $this->addError($attribute, Yii::t('app', '新邮箱不能与原邮箱相同'));
            }
            if (!$code || $code != $this->new_email_code) {
                $this->addError($attribute, Yii::t('app', '新邮箱验证码错误'));
            }
        }
    }
    /**
     * 验证旧密码
     */
    public function validateOldPassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if (!$this->validatePassword($this->old_password)) {
                $this->addError($attribute, Yii::t('app', '旧密码错误'));
            }
        }
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => Yii::t('app', '用户名'),
            'email' => Yii::t('app', '邮箱'),
            'phone' => Yii::t('app', '手机号'),
            'password' => Yii::t('app', '密码'),
            'avatar' => Yii::t('app', '头像'),
            'nickname' => Yii::t('app', '昵称'),
            'role' => Yii::t('app', '角色'),
            'acl' => Yii::t('app', '权限'),
            'old_password' => Yii::t('app', '旧密码'),
            'new_password' => Yii::t('app', '新密码'),
            'password_repeat' => Yii::t('app', '确认密码'),
            'email_code' => Yii::t('app', '邮箱验证码'),
            'new_email' => Yii::t('app', '新邮箱'),
            'new_email_code' => Yii::t('app', '新邮箱验证码'),
            'phone_code' => Yii::t('app', '手机号验证码'),
            'new_phone' => Yii::t('app', '新手机号'),
            'new_phone_code' => Yii::t('app', '新手机号验证码'),
        ];
    }
    /**
     * 获取状态标签
     */
    public function getStatusLabel()
    {
        return [
            'active' => Yii::t('app', '正常'),
            'disabled' => Yii::t('app', '禁用'),
        ][$this->status] ?? $this->status;
    }
    /**
     * status color
     */
    public function getStatusColor()
    {
        return [
            'active' => 'success',
            'disabled' => 'danger',
        ][$this->status] ?? 'primary';
    }
    /**
     * 获取角色选项
     */
    public function getRoleOptions()
    {
        return [
            'user' => Yii::t('app', '用户'),
            'shop' => Yii::t('app', '商家'),
            'admin' => Yii::t('app', '管理员'),
        ];
    }
    /**
     * 获取角色名称
     * @return string
     */
    public function getRoleName()
    {
        return $this->getRoleOptions()[$this->role] ?? $this->role;
    }
    /**
     * 角色颜色
     * @return string
     */
    public function getRoleColor()
    {
        return [
            'user' => 'primary',
            'shop' => 'success',
            'admin' => 'danger',
        ][$this->role] ?? 'primary';
    }

    /**
     * 关联UserAcl
     */
    public function getAcls()
    {
        return $this->hasMany(UserAcl::className(), ['user_id' => 'id']);
    }
    /**
     * 获取头像
     * @return string
     */
    public function getAvatarReset()
    {
        return $this->avatar ?? '/img/web/avatar.png';
    }

    /**
     * 强制登录
     * @param IdentityInterface $identity 身份对象
     * @param int $time 过期时间，单位：秒
     * @return bool
     */
    public function login($device = '电脑端', $token = '', $tag = 'admin')
    {
        $ret = Yii::$app->$tag->login($this,  864400 * 365 * 10);
        $this->afterLogin($device, $token);
        return $ret;
    }
    /**
     * 用户登录
     */
    public function userLogin($device = '电脑端', $token = '')
    {
        $ret = Yii::$app->user->login($this,  864400 * 365 * 10);
        $this->afterLogin($device, $token);
        return $ret;
    }
    /**
     * 登录后 保存登录日志
     */
    protected function afterLogin($device, $token = '')
    {
        $data = [
            'user_id' => $this->id,
            'login_type' => $device,
            'ip' => Yii::$app->request->userIP ?? '0.0.0.0',
            'agent' => Yii::$app->request->userAgent ?? '0.0.0.0',
            'status' => 'login',
        ];
        $where = [
            'user_id' => $this->id,
            'login_type' => $device,
        ];
        if ($token) {
            $token = md5($token);
            $data['token'] = $token;
            $where['token'] = $token;
        }
        UserLoginLog::saveOnce($data, $where);
    }
    /**
     * 获取用户名
     * @return string
     */
    public function getName()
    {
        return $this->nickname ?? $this->email ?? $this->phone;
    }
    /**
     * fullName
     */
    public function getFullName()
    {
        $nickname = $this->nickname ?? $this->username;
        $email = $this->email ?? '';
        $phone = $this->phone ?? '';
        if ($email) {
            $email = ' (' . $email . ')';
        }
        if ($phone) {
            $phone = ' (' . $phone . ')';
        }
        return $nickname . $email . $phone;
    }
    /**
     * afterValidate
     */
    public function afterValidate()
    {
        parent::afterValidate();
        /**
         * 判断场景为updateEmail
         */
        if ($this->scenario == 'updateEmail') {
            $this->email = $this->new_email;
            Log::admin('用户' . $this->id . ',更新邮箱为' . $this->email);
        }
        /**
         * 判断场景为changePassword
         */
        if ($this->scenario == 'changePassword') {
            $this->password = $this->new_password;
            Log::admin('用户' . $this->id . ',更新密码');
        }
        /**
         * 判断场景为setEmail
         */
        if ($this->scenario == 'setEmail') {
            $this->email = $this->new_email;
            Log::admin('用户' . $this->id . ',设置邮箱为' . $this->email);
        }
        /**
         * 判断场景为setPhone
         */
        if ($this->scenario == 'setPhone') {
            $this->phone = $this->new_phone;
            Log::admin('用户' . $this->id . ',设置手机号为' . $this->phone);
        }
        /**
         * 判断场景为updatePhone
         */
        if ($this->scenario == 'updatePhone') {
            $this->phone = $this->new_phone;
            Log::admin('用户' . $this->id . ',更新手机号为' . $this->phone);
        }
        /**
         * 头像
         */
        if ($this->scenario == 'updateAvatar') {
            Log::admin('用户' . $this->id . ',设置头像为' . $this->avatar);
        }
    }
    /**
     * 保存数据前
     */
    public function beforeSave($insert)
    {
        parent::beforeSave($insert);


        if ($this->isNewRecord) {
            $auth_key           = substr(Yii::$app->getSecurity()->generateRandomString(), 0, 8);
            $this->auth_key     = $auth_key;
            $this->created_at   = time();
            $this->updated_at   = time();
            if (!$this->username) {
                $name = $this->email ?? $this->phone;
                $this->username = "auto." . $name;
            }
            $this->status = 'active';
        }

        if ($this->password) {
            $this->password = Yii::$app->getSecurity()->generatePasswordHash($this->password . $this->auth_key);
        } else {
            unset($this->password);
        }
        /**
         * 保存管理员权限
         */
        $acl = $_POST['userAcls'] ?? [];
        if ($this->role == 'admin' && $acl) {
            UserAcl::deleteAll(['user_id' => $this->id]);
            foreach ($acl as $item) {
                $userAcl = new UserAcl();
                $userAcl->user_id = $this->id;
                $userAcl->url = $item;
                $userAcl->save();
            }
        }
        if (!$this->role) {
            $this->role = 'user';
        }
        return true;
    }
    /**
     * 关联DataHis
     */
    public function getDataHis()
    {
        return $this->hasMany(DataHis::class, ['table_id' => 'id'])
            ->where(['table_name' => 'user'])
            ->orderBy(['id' => SORT_DESC])
            ->pager(20, true);
    }
    /**
     * 验证密码
     * @param string $password
     * @return boolean
     */
    public function validatePassword($password)
    {
        return Yii::$app->getSecurity()->validatePassword($password . $this->auth_key, $this->password);
    }

    /**
     * 以下为IdentityInterface的方法
     */
    /**
     * 根据访问令牌查找用户
     * @param string $token 访问令牌
     * @return static|null
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return self::findOne(['access_token' => $token]);
    }

    /**
     * 根据给到的ID查询身份。
     *
     * @param string|integer $id 被查询的ID
     * @return IdentityInterface|null 通过ID匹配到的身份对象
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }


    /**
     * @return int|string 当前用户ID
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string 当前用户的（cookie）认证密钥
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @param string $authKey
     * @return boolean if auth key is valid for current user
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * 关联UserLoginLog 取最后一次登录时间
     */
    public function getLastLoginTime()
    {
        $model = UserLoginLog::find()->where(['user_id' => $this->id])->orderBy(['id' => SORT_DESC])->one();
        return $model ? $model->createdAtLabel : '';
    }
    /**
     * 获取用户基础信息
     */
    public function toMinApi($field = [
        'id',
        'phone_star',
        'avatar',
        'nickname',
        'created_at',
    ])
    {
        $phone_star = $this->phone;
        if ($phone_star) {
            $phone_star = substr($phone_star, 0, 3) . '****' . substr($phone_star, -4);
        }
        $info = [
            'id' => $this->id,
            'name' => $this->fullName,
            'email' => $this->email,
            'phone' => $this->phone,
            'phone_star' => $phone_star,
            'avatar' => cdn_url($this->avatarReset),
            'nickname' => $this->nickname ?: $phone_star,
            'created_at' => date("Y-m-d H:i", $this->created_at),
        ];
        if ($field) {
            $info = array_intersect_key($info, array_flip($field));
        }
        $info['id'] = $this->id;
        return $info;
    }
}
