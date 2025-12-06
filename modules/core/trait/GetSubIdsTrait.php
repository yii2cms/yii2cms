<?php

namespace app\modules\core\trait;


trait GetSubIdsTrait
{
    /**
     * 获取子分类ID,包含自己
     */
    public function getSubIdsWithSelf($type_id)
    {
        $subTypeIds = $this->getSubIds($type_id);
        return array_merge([$type_id], $subTypeIds);
    }
    /**
     * 递归查询分类下所有子分类
     */
    public function getSubIds($type_id)
    {
        $subTypeIds = $this->find()->select('id')->where(['parent_id' => $type_id])->column();
        if ($subTypeIds) {
            $subTypeIds = array_merge($subTypeIds, $this->getSubIds($subTypeIds));
        }
        return $subTypeIds;
    }
}
