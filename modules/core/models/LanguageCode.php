<?php

namespace app\modules\core\models;

use Yii;

class LanguageCode extends \app\modules\core\classes\ActiveRecord
{


    public static function tableName()
    {
        return 'language_code';
    }


    public function rules()
    {
        return [
            [['name', 'code', 'sort'], 'default', 'value' => null],
            [['is_default'], 'default', 'value' => 0],
            [['sort', 'is_default'], 'integer'],
            [['name', 'code'], 'string', 'max' => 255],
            [['badge'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', '语言名称'),
            'code' => Yii::t('app', '语言代码'),
            'sort' => Yii::t('app', '排序'),
            'is_default' => Yii::t('app', '是否默认语言'),
            'badge' => Yii::t('app', '语言标志'),
        ];
    }

    /**
     * beforeSave
     */
    public function beforeSave($insert)
    {
        if ($this->is_default == 1) {
            LanguageCode::updateAll(['is_default' => 0]);
        }
        return parent::beforeSave($insert);
    }
}
