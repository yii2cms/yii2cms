<?php
/**
 * 父级label数组
 * @example $model->getParentLabels()
 * <pre>
 * <code>
 * 字段： title parent_id
 * </code>
 * </pre>
 */

namespace app\modules\core\trait;

trait ParentLable
{
    /**
     * 获取所有父级label名称
     * @return array
     */
    public function getParentLabels()
    {
        $parentLabel = $this->parentLabel;
        //删除数组最后一个元素
        array_pop($parentLabel);
        return $parentLabel;
    }
    /**
     * 获取父级label名称
     * @param int $id 模型id
     * @return array
     */
    public function getParentLabel($id = null)
    {
        // 如果没有传入id，则使用当前模型的id
        $id = $id ?: $this->id;
        // 查找当前模型
        $model = $this->find()->where(['id' => $id])->one();
        // 若找到模型
        if ($model) {
            // 先生成当前节点的信息
            $arr[$model->id] = $model->title;
            // 获取父级id
            $parent_id = $model->parent_id;
            // 如果有父级且父级id大于0
            if ($parent_id > 0) {
                // 递归调用，获取父节点的信息
                $parent = $this->getParentLabel($parent_id);
                // 拼接父节点的标题和当前节点的标题
                if ($parent) {
                    $arr = $parent + $arr;
                }
            }
            return $arr;
        }
        return null;
    }
}
