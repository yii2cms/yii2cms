<?php

/**
 * 授权请查看根目录下的LICENSE.md
 * @author ken <yiiphp@foxmail.com> 
 * @link   https://github.com/yii2cms/yii2cms 
 */

namespace app\modules\core\classes;

use Yii;
use app\modules\core\models\Language as LanguageModel;
use app\modules\core\models\LanguageCode;
use PDO;

/**
 * yii\db\ActiveRecord
 */
class ActiveRecord extends \yii\db\ActiveRecord
{
    /**
     * 语言
     */
    protected $_language;
    /**
     * 启用多语言model
     */
    protected $enableLanguage = false;
    /**
     * 需要多语言处理的字段
     */
    protected $languageFields = [];
    /**
     * 表名
     */
    protected static $tableName = '';
    /**
     * 搜索时间区间
     */
    public $created_at_start;
    /**
     * 结束时间
     */
    public $created_at_end;

    /**
     * 表名
     */
    public static function tableName()
    {
        return "{{%" . static::$tableName . "}}";
    }
    /**
     * 设置表名
     */
    public static function setTable($table)
    {
        static::$tableName = $table;
    }
    /**
     * 查找 ActiveQuery
     */
    public static function find()
    {
        return new ActiveQuery(get_called_class());
    }
    /**
     * 行为
     */
    public function behaviors()
    {
        return [
            \app\modules\core\classes\TrimBehavior::class,
        ];
    }
    /**
     * 解析类名
     */
    public function getMyClassName()
    {
        /**
         * 取调用的类名
         */
        $class = get_class($this);
        $class = str_replace('\\', '.', $class);
        $class = str_replace('app.modules.', '', $class);
        $class = str_replace('.models.', '.', $class);
        return $class;
    }
    /**
     * beforeSave
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                if ($this->hasAttribute('created_at') && $insert && !$this->created_at) {
                    $this->created_at = time();
                }
            }
            if ($this->hasAttribute('updated_at')) {
                $this->updated_at = time();
            }

            return true;
        }
        return false;
    }


    /**
     * 获取文章状态列表
     * @return array
     */
    public function getStatusList()
    {
        return [
            1 => Yii::t('app', '启用'),
            0 => Yii::t('app', '禁用'),

        ];
    }
    /**
     * status 状态显示名称
     * @return string
     */
    public function getStatusLabel()
    {
        $list = $this->getStatusList();
        $text = $list[$this->status] ?? Yii::t('app', '未知');
        return $text;
    }
    /**
     * 状态颜色
     */
    public function getStatusLabelColor()
    {
        switch ($this->status) {
            case 0:
                return 'danger';
            case 1:
                return 'success';
            default:
                return 'primary';
        }
    }
    /**
     * 创建时间格式化
     */
    public function getCreatedAtLabel()
    {
        if (!$this->created_at) {
            return;
        }
        return date('Y-m-d H:i:s', $this->created_at);
    }
    /**
     * 更新时间格式化
     */
    public function getUpdatedAtLabel()
    {
        if (!$this->updated_at) {
            return;
        }
        return date('Y-m-d H:i:s', $this->updated_at);
    }

    /**
     * 搜索时间区间
     */
    public function searchTimeRange(&$query)
    {
        if ($this->created_at_start !== null && $this->created_at_start !== '') {
            $query->andWhere(['>=', 'created_at', strtotime($this->created_at_start . ' 00:00:00')]);
        }
        if ($this->created_at_end !== null && $this->created_at_end !== '') {
            $query->andWhere(['<=', 'created_at', strtotime($this->created_at_end . ' 23:59:59')]);
        }
    }
    /**
     * 获取第一个错误
     */
    public function getErr()
    {
        if ($this->hasErrors()) {
            // 获取所有错误数组
            $errors = $this->getErrors();

            // 提取第一个属性的第一个错误
            foreach ($errors as $attributeErrors) {
                $firstError = reset($attributeErrors);
                break;
            }
            return $firstError;
        }
        return '';
    }
    /**
     * 重写save,记录错误
     */
    public function save($runValidation = true, $attributeNames = null)
    {
        $this->getQueryLanguage();
        // 如果是多语言模式且不是默认语言
        if ($this->enableLanguage && $this->_language && $this->_language !== Language::getDefaultLanguageCode()) {
            // 只验证不保存到主表
            if ($runValidation && !$this->validate($attributeNames)) {
                return false;
            }
            return $this->saveToLanguageTable();
        }
        $parent = parent::save($runValidation, $attributeNames);
        if ($parent) {
            return $parent;
        } else {
            $err = $this->getErr();
            if ($err && $this->tableName() != 'log') {
                Log::add("ActiveRecord 【" . $this->tableName() . "】 save异常，原因：" . $err, 'error');
            }
            return false;
        }
    }
    /**
     * 只添加一次
     * @param array $data 数据
     * @param array $where 条件
     * @return bool
     */
    public static function addOnce($data = [], $where = [])
    {
        if (!$where) {
            add_log('addOnce 条件不能为空', 'error');
            throw new \Exception(Yii::t('app', 'saveOnce 条件不能为空'));
        }
        // 检查订单是否已存在
        if (static::find()->where($where)->exists()) {
            return false;
        }
        $model = new static();
        $model->setAttributes($data);
        $res = $model->save();
        $err = $model->getErr();
        if ($err) {
            add_log('addOnce 保存失败，原因：' . $err, 'error');
        }
        return $res;
    }
    /**
     * 按条件查询是否存在，存在更新，不存在添加
     * @param array $data 数据 [json.key=>1]
     * @param array $where 条件
     * @return bool
     */
    public static function saveOnce($data = [], $where = [])
    {
        if (!$where) {
            add_log('saveOnce 条件不能为空', 'error');
            throw new \Exception(Yii::t('app', 'saveOnce 条件不能为空'));
        }
        $model = static::find()->where($where)->one();
        if (!$model) {
            $model = new static();
        }
        foreach ($data as $k => $v) {
            if ($k == 'id') {
                continue;
            }
            /**
             * 处理json字段
             */
            if (strpos($k, '.') !== false) {
                $arr = explode('.', $k);
                $key = $arr[0];
                $fieldData = $model->$key;
                if (!$fieldData) {
                    $fieldData = [];
                }
                $fieldData[trim($arr[1])] = $v;
                $model->$key = $fieldData;
            } else {
                $model->$k = $v;
            }
        }
        $res = $model->save();
        $err = $model->getErr();
        if ($err) {
            add_log('saveOnce 保存失败，原因：' . $err, 'error');
        }
        return $res;
    }
    /**
     * 保存到多语言表
     */
    protected function saveToLanguageTable()
    {
        if ($this->isNewRecord) {
            $enableLanguage = $this->enableLanguage;
            $this->enableLanguage = false;
            if (!parent::save()) {
                return false;
            }
            $this->enableLanguage = $enableLanguage;
        }
        $attributes = $this->getAttributes();
        // 过滤多语言字段
        $new_attributes = [];
        foreach ($this->languageFields as $field) {
            if (isset($attributes[$field])) {
                $new_attributes[$field] = $attributes[$field];
            }
        }
        $languageModel = LanguageModel::find()->where([
            'code' => $this->_language,
            'nid'  => $this->id,
            'table' => $this->tableName(),
        ])->one();
        if (!$languageModel) {
            $languageModel = new LanguageModel();
            $languageModel->table = $this->tableName();
            $languageModel->code = $this->_language;
            $languageModel->nid = $this->id;
        }
        $languageModel->data = $new_attributes;
        if (!$languageModel->save()) {
            return false;
        }
        return true;
    }
    /**
     * 从多语言表加载数据
     */
    public function afterFind()
    {
        parent::afterFind();
        $this->loadFromLanguage();
    }
    /**
     * 从多语言表加载数据
     */
    public function loadFromLanguage($languageCode = null)
    {
        if (!$this->id) {
            return false;
        }
        $languageCode = $languageCode ?: $this->getQueryLanguage();
        $languageModel = LanguageModel::find()->where([
            'code' => $languageCode,
            'nid'  => $this->id,
            'table' => $this->tableName(),
        ])->one();
        if ($languageModel && !empty($languageModel->data)) {
            $data = $languageModel->data;
            $new_data = [];
            foreach ($this->languageFields as $field) {
                if (isset($data[$field])) {
                    $new_data[$field] = $data[$field];
                }
            }
            $this->setAttributes($new_data);
            return true;
        }
        return false;
    }
    /**
     * 判断对应的语言的有没有翻译过
     */
    public function hasLanguage($languageCode = null)
    {
        $languageCode = $languageCode ?: $this->getQueryLanguage();
        return LanguageModel::find()->where([
            'code' => $languageCode,
            'nid'  => $this->id,
            'table' => $this->tableName(),
        ])->exists();
    }
    /**
     * 获取语言
     */
    public function getQueryLanguage()
    {
        if (is_cli()) {
            return 'zh-CN';
        }
        if ($this->_language === null) {
            $this->_language = Yii::$app->request->get('lang') ?: Env::getInput('lang');
        }
        return $this->_language;
    }

    /**
     * 获取语言字段
     */
    public function getAllowLanguageFields()
    {
        $this->getQueryLanguage();
        // 如果是多语言模式且不是默认语言
        if ($this->enableLanguage && $this->_language && $this->_language !== Language::getDefaultLanguageCode()) {
            return $this->languageFields;
        }
    }
    /**
     * 加载数据操作历史
     */
    public function getActionHistory()
    {
        $data_his = $this->dataHisList ?? [];
        if ($data_his) {
            $data_his = array_map(function ($item) {
                return $item->toApi();
            }, $data_his);
        }
        return $data_his;
    }
    /**
     * 虚拟字段 如 data为json字段，是真实存在的，其他data_image会保存到data下
     * @example
     * <pre>
     * <code>
     * public function __get($name)
     * {
     *     $value = $this->virtualGet($name);
     *     if (strpos($name, 'data_') === 0) {
     *         return $value;
     *     }
     *     if ($value !== null) {
     *         return $value;
     *     }
     *     return parent::__get($name);
     * }
     * 
     * public function __set($name, $value)
     * {
     *     $res = $this->virtualSet($name, $value);
     *     if ($res) {
     *         return $res;
     *     }
     *     parent::__set($name, $value);
     * }
     * 
     * </code>
     * </pre>
     */
    public function virtualGet($name, $virtural_name = 'data')
    {
        $data = $this->getAttribute($virtural_name);
        if (is_string($data)) {
            $decoded = json_decode($data, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $data = $decoded;
            } else {
                $data = [];
            }
        } elseif (!is_array($data)) {
            $data = [];
        }
        // data_* 虚拟字段
        if (strpos($name, $virtural_name . '_') === 0) {
            if (is_array($data) && array_key_exists($name, $data)) {
                return $data[$name];
            }
            return null;
        }
        if (is_array($data) && array_key_exists($name, $data)) {
            return $data[$name];
        }
    }
    /**
     * 为 data_* 虚拟字段提供写入支持，统一落到 data 数组。 
     */
    public function virtualSet($name, $value, $virtural_name = 'data')
    {
        if (strpos($name, $virtural_name . '_') === 0) {
            $data = $this->getAttribute($virtural_name);
            if (is_string($data)) {
                $decoded = json_decode($data, true);
                $data = json_last_error() === JSON_ERROR_NONE ? $decoded : [];
            } elseif (!is_array($data)) {
                $data = [];
            }
            $data[$name] = $value;
            $this->setAttribute($virtural_name, $data);
            return true;
        }
    }
}
