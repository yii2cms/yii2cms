<?php

namespace app\modules\core\models;

use Yii;

class Post extends \app\modules\core\classes\ActiveRecord
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
        'content',
    ];

    public static function tableName()
    {
        return 'post';
    }


    public function rules()
    {
        return [
            [['content', 'type_id', 'status', 'delete_at', 'created_at', 'updated_at'], 'default', 'value' => null],
            [['name', 'type_id'], 'required'],
            [['content'], 'string'],
            [['type_id', 'status', 'delete_at', 'created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 255],

            [['created_at_start', 'created_at_end'], 'date', 'format' => 'yyyy-MM-dd'],
            [['created_at_start', 'created_at_end', 'image', 'images', 'sort'], 'safe'],

        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', '标题'),
            'content' => Yii::t('app', '内容'),
            'type_id' => Yii::t('app', '类型'),
            'status' => Yii::t('app', '状态'),
            'statusLabel' => Yii::t('app', '状态'),
            'delete_at' => Yii::t('app', '删除时间'),
            'created_at' => Yii::t('app', '创建时间'),
            'updated_at' => Yii::t('app', '更新时间'),
            'image' => Yii::t('app', '图片'),
            'images' => Yii::t('app', '图片库'),
            'sort' => Yii::t('app', '排序'),

        ];
    }

    /**
     * 获取文章类型列表 dropDownList 
     * @return array
     */
    public function getTypeList()
    {
        $all = PostType::find()->select(['id', 'name'])->orderBy(['sort' => SORT_ASC])->indexBy('id')->all();
        $list = [];
        if ($all) {
            foreach ($all as $item) {
                $list[$item->id] = $item->name;
            }
        }
        return $list;
    }

    /**
     * 关联Type
     */
    public function getType()
    {
        return $this->hasOne(PostType::class, ['id' => 'type_id']);
    }

    /**
     * 商品详情html
     */
    public function getContentHtml()
    {
        $body = $this->content;
        if (!$body) {
            return '';
        }
        $body = preg_replace_callback('/<img.*?src=[\'"](.*?)[\'"].*?>/i', function ($matches) {
            return '<img src="' . cdn_url($matches[1]) . '" style="width: 100%;" />';
        }, $body);
        $body = str_replace('<figure class="image">', '<p>', $body);
        $body = str_replace('</figure>', '</p>', $body);
        return $body;
    }

    public function toApi()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'content' => $this->content,
            'content_html' => $this->contentHtml,
            'type_id' => $this->type_id,
            'type'    => $this->type,
            'image' => $this->image ? cdn_url($this->image) : '',
            'images' => $this->images ? cdn_url($this->images) : '',
            'created_at' => date('Y-m-d H:i', $this->created_at),
            'updated_at' => date('Y-m-d H:i', $this->updated_at),
            'sort' => $this->sort,
        ];
    }
}
