<?php

namespace app\modules\core\models;

use Yii;

class PostType extends \app\modules\core\classes\ActiveRecord
{
    /**
     * 启用多语言model
     */
    protected $enableLanguage = true;
    /**
     * 需要多语言处理的字段
     */
    protected $languageFields = [
        'name',
    ]; 

    public static function tableName()
    {
        return 'post_type';
    }


    public function rules()
    {
        return [
            [['sort', 'delete_at', 'created_at', 'updated_at'], 'default', 'value' => null],
            [['name'], 'required'],
            [['sort', 'delete_at', 'created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', '类型名称'),
            'sort' => Yii::t('app', '排序'),
            'delete_at' => Yii::t('app', '删除时间'),
            'created_at' => Yii::t('app', '创建时间'),
            'updated_at' => Yii::t('app', '更新时间'),
        ];
    }
}
