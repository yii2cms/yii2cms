<?php

namespace app\modules\core\models;

use Yii;

class Language extends \app\modules\core\classes\ActiveRecord
{


    public static function tableName()
    {
        return 'language';
    }


    public function rules()
    {
        return [
            [['code', 'nid', 'data'], 'default', 'value' => null],
            [['nid'], 'integer'],
            [['data'], 'safe'],
            [['code'], 'string', 'max' => 255],
            [['table'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'code' => Yii::t('app', '语言代码'),
            'nid' => Yii::t('app', '关联模型ID'),
            'data' => Yii::t('app', '语言数据'),
        ];
    }
}
