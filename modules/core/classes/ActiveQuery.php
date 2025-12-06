<?php

/**
 * 授权请查看根目录下的LICENSE.md
 * @author ken <yiiphp@foxmail.com> 
 * @link   https://github.com/yii2cms/yii2cms 
 */

namespace app\modules\core\classes;

use Yii;

/**
 * yii\db\ActiveQuery
 */
class ActiveQuery extends \yii\db\ActiveQuery
{
    /**
     * 分页,返回api格式数据
     */
    public function page($pageSize = 20)
    {
        return $this->pager($pageSize, false);
    }
    /**
     * 分页,返回 LinkPager 需要的 $pagination
     * @param int  $pageSize 每页数量
     * @param bool $is_php_page 是否返回php分页对象,在PHP视图时需要返回
     * @return array
     */
    public function pager($pageSize = 20, $is_php_page = true)
    {
        $query = clone $this;
        $count = $query->count();
        $page = Yii::$app->request->get('page', 1) - 1;
        $pagination = new \yii\data\Pagination([
            'totalCount' => $count,
            'pageSize'   => $pageSize,
            'page'       => $page,
        ]);
        $data = $query->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();
        if ($data) {
            $new_data = [];
            foreach ($data as $value) {
                if (!$is_php_page && method_exists($value, 'toApi')) {
                    $new_data[] = $value->toApi();
                } else {
                    $new_data[] = $value;
                }
            }
            $data = $new_data;
        }
        $result = [
            'code' => 0,
            'total' => $count,
            'pageSize' => $pageSize,
            'page' => $pagination->page + 1,
            'pageCount' => ceil($count / $pageSize),
            'data' => $data,
        ];
        if ($is_php_page === true) {
            $result['pagination'] = $pagination;
            $result['query'] = $query;
        }
        return $result;
    }

    /**
     * debug sql
     */
    public function getSql()
    {
        $command = $this->createCommand();
        $sql = $command->sql;
        $params = $command->params;
        echo "SQL: " . $sql . "\n";
        echo "参数: " . print_r($params, true) . "\n";
    }
}
