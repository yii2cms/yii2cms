<?php


namespace app\modules\core\models;

use Yii;

class Notice extends \app\modules\core\classes\ActiveRecord
{


    public static function tableName()
    {
        return 'notice';
    }


    public function rules()
    {
        return [
            [['data', 'send_at', 'created_at', 'updated_at'], 'default', 'value' => null],
            [['title'], 'default', 'value' => ''],
            [['status'], 'default', 'value' => 0],
            [['content', 'type'], 'required'],
            [['content'], 'string'],
            [['data'], 'safe'],
            [['user_id', 'shop_id', 'status', 'send_at', 'created_at', 'updated_at'], 'integer'],
            [['title'], 'string', 'max' => 255],
            [['type'], 'string', 'max' => 100],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'title' => Yii::t('app', '标题'),
            'content' => Yii::t('app', '内容'),
            'data' => Yii::t('app', '数据'),
            'user_id' => Yii::t('app', '用户ID'),
            'shop_id' => Yii::t('app', '店铺ID'),
            'status' => Yii::t('app', '状态'),
            'type' => Yii::t('app', '发送方式'),
            'send_at' => Yii::t('app', '发送时间'),
            'created_at' => Yii::t('app', '创建时间'),
            'updated_at' => Yii::t('app', '更新时间'),
        ];
    }
    
    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->status = 0;
        }
        return parent::beforeSave($insert);
    }
}
