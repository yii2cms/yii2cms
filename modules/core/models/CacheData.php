<?php

 
namespace app\modules\core\models;

use Yii;
 
class CacheData extends \app\modules\core\classes\ActiveRecord
{

 
    public static function tableName()
    {
        return 'cache_data';
    }

     
    public function rules()
    {
        return [
            [['content'], 'default', 'value' => null],
            [['key'], 'default', 'value' => ''],
            [['group'], 'default', 'value' => 'default'], 
            [['key', 'group'], 'string', 'max' => 255],
        ];
    }
 
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => 'Key',
            'group' => 'Group',
            'content' => 'Content',
        ];
    }
 

}
