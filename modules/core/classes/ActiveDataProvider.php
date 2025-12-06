<?php

/**
 * 授权请查看根目录下的LICENSE.md
 * @author ken <yiiphp@foxmail.com> 
 * @link   https://github.com/yii2cms/yii2cms 
 */

namespace app\modules\core\classes;

use yii\helpers\Url;

/**
 * yii\data\ActiveDataProvider
 */
class ActiveDataProvider extends \yii\data\ActiveDataProvider
{
    /**
     * 分页
     * @example
     * <pre>
     * <code>
     * $searchModel = new GoodsSearch();
     * $dataProvider = $searchModel->search($this->request->queryParams);
     * return $this->asJson($dataProvider->pager());
     * </code>
     * </pre>
     * @return array
     */
    public function pager()
    {
        $pagination = $this->getPagination();
        $count = $this->getTotalCount();
        $pageSize = $pagination->pageSize;
        $data = $this->getModels();
        if ($data) {
            $new_data = [];
            foreach ($data as $key => $value) {
                $new_data[] = $value->toApi();
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
        return $result;
    }
}
