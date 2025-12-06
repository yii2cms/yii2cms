<?php

namespace app\modules\core\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\core\models\MailTemplate;


class MailTemplateSearch extends MailTemplate
{
   
    public function rules()
    {
        return [
            [['id', 'created_at'], 'integer'],
            [['name', 'key', 'content'], 'safe'],
        ];
    }
    
    public function scenarios()
    {
        return Model::scenarios();
    }
   
    public function search($params, $formName = null)
    {
        $query = MailTemplate::find();       

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ],
        ]);

        $this->load($params, $formName);

        if (!$this->validate()) {
            // 如果验证失败不想返回任何记录，请取消下面这行的注释
            // $query->where('0=1');
            return $dataProvider;
        }
 
        $query->andFilterWhere([
            'id' => $this->id,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'key', $this->key])
            ->andFilterWhere(['like', 'content', $this->content]);

        return $dataProvider;
    }
}
