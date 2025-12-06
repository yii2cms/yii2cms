<?php

namespace app\modules\core\models;

use Yii;

class UserLoginLog extends \app\modules\core\classes\ActiveRecord
{


    public static function tableName()
    {
        return 'user_login_log';
    }


    public function rules()
    {
        return [
            [['ip', 'agent'], 'default', 'value' => null],
            [['user_id', 'login_type'], 'required'],
            [['user_id', 'created_at'], 'integer'],
            [['login_type', 'ip', 'agent'], 'string', 'max' => 255],
            [['token', 'status', 'name', 'updated_at'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'login_type' => 'Login Type',
            'ip' => 'Ip',
            'agent' => 'Agent',
            'created_at' => 'Created At',
        ];
    }
}
