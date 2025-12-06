<?php

namespace app\modules\core\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\core\models\Log;


class LogSearch extends Log
{

    public function rules()
    {
        return [
            [['id', 'user_id', 'created_at'], 'integer'],
            [['content', 'type', 'word'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params, $formName = null)
    {
        $query = Log::find();

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

        $word = $params['LogSearch']['word'] ?? '';
        if ($word) {
            /**
             * 搜索email 或 phone
             */
            $query->joinWith('user')
                ->andFilterWhere(['like', 'user.email', $word]);
            $query->orFilterWhere(['like', 'user.phone', $word]);
        }

        $query->andFilterWhere(['like', 'content', $this->content])
            ->andFilterWhere(['like', 'type', $this->type]);

        if (!$this->validate()) {
            // 如果验证失败不想返回任何记录，请取消下面这行的注释
            // $query->where('0=1');
            return $dataProvider;
        }


        return $dataProvider;
    }
}
