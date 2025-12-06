<?php

namespace app\modules\core\models;

use Yii;

class MailTemplate extends \app\modules\core\classes\ActiveRecord
{


    public static function tableName()
    {
        return 'mail_template';
    }


    public function rules()
    {
        return [
            [['name', 'content', 'created_at'], 'default', 'value' => null],
            [['key'], 'default', 'value' => ''],
            [['content'], 'string'],
            [['created_at'], 'integer'],
            [['name', 'key'], 'string', 'max' => 255],
            [['key'], 'unique'],
            [['title'], 'string', 'max' => 255],
            [['title'], 'safe'],
            //key必须是字母数字下划线
            ['key', 'match', 'pattern' => '/^[a-z0-9_.:]+$/'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', '模板名称'),
            'key' => Yii::t('app', '模板键名'),
            'content' => Yii::t('app', '邮件内容'),
            'created_at' => Yii::t('app', '创建时间'),
            'title' => Yii::t('app', '邮件标题'),
        ];
    }
}
