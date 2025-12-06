<?php

namespace app\modules\core\models;

use Yii;

class Log extends \app\modules\core\classes\ActiveRecord
{
    public $word;

    public static function tableName()
    {
        return 'log';
    }


    public function rules()
    {
        return [
            [['content', 'type', 'user_id', 'created_at'], 'default', 'value' => null],
            [['content'], 'required'],
            [['user_id', 'created_at'], 'integer'],
            [['type'], 'string', 'max' => 255],
            [['ip', 'agent'], 'string', 'max' => 255],
            [['ip', 'agent'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'content' => Yii::t('app', '日志'),
            'type' => Yii::t('app', '类型'),
            'user_id' => Yii::t('app', '操作员'),
            'created_at' => Yii::t('app', '创建时间'),
            'word' => Yii::t('app', '用户'),
            'ip' => Yii::t('app', 'IP'),
            'agent' => Yii::t('app', '浏览器'),
            'contentRaw' => Yii::t('app', '日志详情'),
        ];
    }

    /**
     * 保存数据前
     */
    public function beforeSave($insert)
    {
        parent::beforeSave($insert);
        if ($this->isNewRecord) {
            $this->created_at = time();
        }
        if (is_array($this->content)) {
            $this->content = json_encode($this->content, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }
        return true;
    }
    /**
     * 搜索类型列表
     */
    public function getTypeList()
    {
        return [
            'info' => '信息',
            'error' => '错误',
            'debug' => '调试',
        ];
    }
    /**
     * type显示值
     * @return array
     */
    public function getTypeLabel()
    {
        return $this->getTypeList()[$this->type] ?? '';
    }
    /**
     * type颜色
     */
    public function getTypeColor()
    {
        $colors = [
            'info' => 'primary',
            'error' => 'danger',
            'debug' => 'info',
        ];
        return $colors[$this->type] ?? 'primary';
    }
    /**
     * 关联User
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * contentRaw
     */
    public function getContentRaw()
    {
        $str = $this->content;
        $arr = json_decode($str, true) ?? [];
        if ($arr) {
            $str = "";
            foreach ($arr as $key => $value) {
                if (is_array($value)) {
                    $value = json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                }
                $str .= $key . ': ' . $value . "\n";
            }
            return $str;
        } else {
            return $str;
        }
    }
}
