<?php

namespace app\modules\core\models;

use Yii;

class Config extends \app\modules\core\classes\ActiveRecord
{


    public static function tableName()
    {
        return 'config';
    }


    public function rules()
    {
        return [
            [['key'], 'default', 'value' => ''],
            [['type'], 'default', 'value' => 'input'],
            [['created_at'], 'integer'],
            [['name', 'key', 'type'], 'string', 'max' => 255],
            //key 唯一
            [['key'], 'unique'],
            //key必须是字母数字下划线
            ['key', 'match', 'pattern' => '/^[a-z0-9_.:]+$/'],
            [['type_value', 'content','help'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', '配置名称'),
            'key' => Yii::t('app', '配置键名'),
            'content' => Yii::t('app', '配置内容'),
            'type' => Yii::t('app', '类型'),
            'type_value' => Yii::t('app', '类型值'),
            'help' => Yii::t('app', '帮助'),
            'created_at' => Yii::t('app', '创建时间'),
        ];
    }

    /**
     * 显示内容
     */
    public function getDisplayContent()
    {
        if ($this->type == 'dropDownList') {
            return $this->type_value[$this->content] ?? $this->content;
        }
        if ($this->type == 'image') {
            return  "<img src='" .  $this->content . "' alt='' style='max-width: 100px;'>";
        }
        return $this->content;
    }

    public function beforeSave($insert)
    {
        if(is_array( $this->content))
        {
            $this->content = json_encode($this->content,JSON_UNESCAPED_UNICODE);
        }
        return parent::beforeSave($insert);
    }
}
