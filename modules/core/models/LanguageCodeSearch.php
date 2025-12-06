<?php

namespace app\modules\core\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\core\models\LanguageCode;


class LanguageCodeSearch extends LanguageCode
{
   
    public function rules()
    {
        return [
            [['id', 'sort', 'is_default'], 'integer'],
            [['name', 'code'], 'safe'],
        ];
    }
    
    public function scenarios()
    {
        return Model::scenarios();
    }
   
    public function search($params, $formName = null)
    {
        $query = LanguageCode::find();       

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'defaultOrder' => [
                    'sort' => SORT_DESC,
                    'id'   => SORT_ASC,
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
            'is_default' => $this->is_default,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'code', $this->code]);

        return $dataProvider;
    }
}
