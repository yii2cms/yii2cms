<?php

namespace app\modules\core\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\core\models\PostType;


class PostTypeSearch extends PostType
{
   
    public function rules()
    {
        return [
            [['id', 'sort', 'delete_at', 'created_at', 'updated_at'], 'integer'],
            [['name'], 'safe'],
        ];
    }
    
    public function scenarios()
    {
        return Model::scenarios();
    }
   
    public function search($params, $formName = null)
    {
        $query = PostType::find();       

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
            'sort' => $this->sort,
            'delete_at' => $this->delete_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
