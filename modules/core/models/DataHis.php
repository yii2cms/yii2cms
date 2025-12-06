<?php

namespace app\modules\core\models;

use Yii;

class DataHis extends \app\modules\core\classes\ActiveRecord
{


    public static function tableName()
    {
        return 'data_his';
    }


    public function rules()
    {
        return [
            [['table_name', 'table_id', 'data', 'user_id', 'created_at'], 'default', 'value' => null],
            [['table_id', 'user_id', 'created_at'], 'integer'],
            [['data'], 'string'],
            [['table_name'], 'string', 'max' => 255],
            [['color'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'table_name' => 'Table Name',
            'table_id' => 'Table ID',
            'data' => 'Data',
            'user_id' => 'User ID',
            'created_at' => 'Created At',
        ];
    }

    /**
     * 操作人用户名
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
    /**
     * 操作人用户名
     */
    public function getUserName()
    {
        $user = $this->user;
        if ($user) {
            return $user->fullName;
        }
        return '';
    }
    /**
     * body
     */
    public function getBody()
    {
        $data = $this->data;
        if (is_string($data)) {
            return $data;
        }
    }

    public function toApi()
    {
        return [
            'color' => $this->color,
            'data' => $this->data,
            'created_at' => date('Y-m-d H:i', $this->created_at),
        ];
    }
}
