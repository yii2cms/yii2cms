<?php

/**
 * 授权请查看根目录下的LICENSE.md
 * @author ken <yiiphp@foxmail.com> 
 * @link   https://github.com/yii2cms/yii2cms 
 */

namespace app\modules\core\classes;

use Yii;
use app\modules\core\models\LanguageCode;
use app\modules\core\models\LanguageT;

/**
 * 语言类
 */
class Language
{
    public static $_langData = [];
    /**
     * 取默认语言code
     */
    public static function getDefaultLanguageCode()
    {
        $model = LanguageCode::find()->orderBy(['sort' => SORT_ASC, 'is_default' => SORT_DESC])->one();
        $code = $model ? $model->code : '';
        return $code ?? 'zh-CN';
    }
    /**
     * 获取所有语言列表
     * @return array
     */
    public static function getAllLanguageCode()
    {
        if (self::$_langData) {
            return self::$_langData;
        }
        $all =  LanguageCode::find()->orderBy(['sort' => SORT_ASC, 'is_default' => SORT_DESC])->all();
        $list = [];
        if ($all) {
            foreach ($all as $item) {
                $list[$item->code] = [
                    'name'  => $item->name,
                    'badge' => $item->badge,
                ];
            }
        }
        self::$_langData = $list;
        return $list;
    }
    /**
     * 初始化语言
     */
    public static function init()
    {
        if (is_dev()) {
            $model = LanguageCode::find()->one();
            if (!$model) {
                $model = new LanguageCode();
                $model->is_default = 1;
                $model->code = 'zh-CN';
                $model->name = '中文';
                $model->badge = '中';
                $model->save();
            }
        }
    }
    /**
     * 是否开启
     */
    public static function isEnable()
    {
        return get_config('is_muit_language') == 1 ? true : false;
    }
    /**
     * 同步语言key到表中
     * @param $key 语言key
     * @param $value 语言值
     * @param $code 语言code
     */
    public static function sync($key, $value)
    {
        if (!is_dev()) {
            return;
        }
        $all = self::getAllLanguageCode();
        foreach ($all as $code => $v) {
            $model = LanguageT::find()->where(['code' => $code, 'key' => $key])->one();
            if (!$model) {
                $model = new LanguageT();
                $model->code = $code;
                $model->key = $key;
                $model->value = $value;
                $model->save();
            }
        }
    }
}
