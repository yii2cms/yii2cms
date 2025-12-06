<?php

namespace app\modules\core\models;

use Yii;
use app\modules\core\classes\Sms;
class SmsTemplate extends \app\modules\core\classes\ActiveRecord
{


    public static function tableName()
    {
        return 'sms_template';
    }


    public function rules()
    {
        return [
            [['name', 'content', 'created_at'], 'default', 'value' => null],
            [['key'], 'default', 'value' => ''],
            [['type'], 'default', 'value' => 'default'],
            [['content'], 'string'],
            [['created_at'], 'integer'],
            [['name', 'key', 'type'], 'string', 'max' => 255],
            //key必须是字母数字下划线
            ['key', 'match', 'pattern' => '/^[a-z0-9_.:]+$/'],
            //key type 唯一
            ['key', 'unique', 'targetAttribute' => ['key', 'type'], 'message' => '短信模板键名和类型已存在'], 
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', '短信模板名称'),
            'key' => Yii::t('app', '短信模板键名'),
            'content' => Yii::t('app', '短信模板内容'),
            'type' => Yii::t('app', '短信模板类型'),
            'created_at' => Yii::t('app', '创建时间'),
        ];
    }

    public function getDropdownType()
    {
        return Sms::getDriveTypes();
    }

    /**
     * 显示type
     */
    public function getDisplayType()
    {
        return $this->getDropdownType()[$this->type]??'';
    }
}
