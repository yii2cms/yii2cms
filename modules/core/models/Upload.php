<?php

namespace app\modules\core\models;

use app\modules\core\classes\Cdn;
use Yii;

class Upload extends \app\modules\core\classes\ActiveRecord
{
    public $http_url;

    public static function tableName()
    {
        return 'upload';
    }

    public function rules()
    {
        return [
            [['name', 'url', 'hash', 'size', 'type', 'ext'], 'required'],
            [['created_at'], 'integer'],
            [['name', 'url', 'hash', 'type', 'ext'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', '名称'),
            'url' => Yii::t('app', '地址'),
            'hash' => Yii::t('app', 'Hash'),
            'size' => Yii::t('app', '大小'),
            'type' => Yii::t('app', '类型'),
            'ext' => Yii::t('app', '扩展名'),
            'created_at' => Yii::t('app', '创建时间'),
        ];
    }

    public function afterFind()
    {
        parent::afterFind();
        $this->http_url = Cdn::getUrl($this->url);
    }
}
