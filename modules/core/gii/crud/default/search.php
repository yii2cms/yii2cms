<?php

use yii\helpers\StringHelper;

$modelClass = StringHelper::basename($generator->modelClass);
$searchModelClass = StringHelper::basename($generator->searchModelClass);
if ($modelClass === $searchModelClass) {
    $modelAlias = $modelClass . 'Model';
}
$rules = $generator->generateSearchRules();
$labels = $generator->generateSearchLabels();
$searchAttributes = $generator->getSearchAttributes();
$searchConditions = $generator->generateSearchConditions();

echo "<?php\n";
?>

namespace <?= StringHelper::dirname(ltrim($generator->searchModelClass, '\\')) ?>;

use yii\base\Model;
use app\modules\core\classes\ActiveDataProvider;
use <?= ltrim($generator->modelClass, '\\') . (isset($modelAlias) ? " as $modelAlias" : "") ?>;


class <?= $searchModelClass ?> extends <?= isset($modelAlias) ? $modelAlias : $modelClass ?>

{
   
    public function rules()
    {
        return [
            <?= implode(",\n            ", $rules) ?>,
        ];
    }
    
    public function scenarios()
    {
        return Model::scenarios();
    }
   
    public function search($params, $formName = null)
    {
        $query = <?= isset($modelAlias) ? $modelAlias : $modelClass ?>::find();       

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
 
        <?= implode("\n        ", $searchConditions) ?>

        return $dataProvider;
    }
}
