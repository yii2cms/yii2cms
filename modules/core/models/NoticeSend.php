<?php
 
 
namespace app\modules\core\models;

use Yii;
 
class NoticeSend extends \app\modules\core\classes\ActiveRecord
{

 
    public static function tableName()
    {
        return 'notice_send';
    }

     
    public function rules()
    {
        return [
            [['created_at', 'updated_at'], 'default', 'value' => null],
            [['notice_id'], 'default', 'value' => 0],
            [['account'], 'default', 'value' => ''],
            [['notice_id', 'created_at', 'updated_at'], 'integer'],
            [['type'], 'required'],
            [['type'], 'string', 'max' => 100],
            [['account'], 'string', 'max' => 255],
        ];
    }
 
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'notice_id' => Yii::t('app', '通知ID'),
            'type' => Yii::t('app', '发送方式'),
            'account' => Yii::t('app', '账号'),
            'created_at' => Yii::t('app', '创建时间'),
            'updated_at' => Yii::t('app', '更新时间'),
        ];
    }
 

}
