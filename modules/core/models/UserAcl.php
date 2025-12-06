<?php

namespace app\modules\core\models;

use Yii;
 
class UserAcl extends \app\modules\core\classes\ActiveRecord
{

 
    public static function tableName()
    {
        return 'user_acl';
    }

     
    public function rules()
    {
        return [
            [['url'], 'default', 'value' => null],
            [['user_id'], 'required'],
            [['user_id'], 'integer'],
            [['url'], 'safe'],
        ];
    }
 
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'url' => 'Url',
        ];
    }
 

}
