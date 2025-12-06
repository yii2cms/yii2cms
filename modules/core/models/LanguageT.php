<?php

namespace app\modules\core\models;

use Yii;
 
class LanguageT extends \app\modules\core\classes\ActiveRecord
{

 
    public static function tableName()
    {
        return 'language_t';
    }

     
    public function rules()
    {
        return [
            [['code', 'key', 'value'], 'default', 'value' => null],
            [['code', 'key', 'value'], 'string', 'max' => 255],
        ];
    }
 
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'code' => Yii::t('app', '语言代码'),
            'key' => Yii::t('app', '翻译翻译的key'),
            'value' => Yii::t('app', '翻译翻译的value'),
        ];
    }
 

}
