<?php

namespace app\modules\core\trait;

use Yii;

/**
 * 父级下拉框
 * @example
 * <pre>
 * <code>
 * 
 * 字段： title parent_id
 * 
 * <?= $form->field($model, 'parent_id')
 *      ->dropDownList(
 *          $model->getParentSelect(), 
 *          ['prompt' => '','class' => 'select2','encode' => false]
 * ) ?>
 * </code>
 * </pre>
 */

trait ParentSelect
{
    /**
     *  获取Tree数据
     * @example
     * <pre>
     * <code>
     * $list = $model->getTree(function($model){ 
     *    $model['isOpen'] = false;
     *    return $model;
     * }); 
     * </code>
     * </pre>
     */
    public function getTree($call = '')
    {
        $list = [];
        $models = $this->find()->all();
        if (!$models) {
            return [];
        }
        foreach ($models as $model) {
            if (!$model->parent_id) {
                $model->parent_id = 0;
            }
            $model = $model->toArray();
            $model['text'] = $model['title'];
            if ($model['id'] == $this->id) {
                continue;
            }
            if ($call) {
                $model  = $call($model);
            }
            $list[] = $model;
        }
        if (!$list) {
            return [];
        }
        $list = \app\modules\core\classes\Arr::toTree($list, 'id', 'parent_id');
        return $list;
    }
    /**
     * 加载下拉框数据
     * @return array
     */
    public function getParentSelect($call = '')
    {
        $list = $this->getTree();
        return $this->loadSelectLoop($list);
    }

    protected function loadSelectLoop($list, $pre = '')
    {
        $new_list = [];
        foreach ($list as $v) {
            // 根据前缀格式化当前节点的标题
            //$outputPre = $pre ? "|".$pre : '';
            $outputPre = $pre;
            // 将当前节点添加到新的列表中
            $new_list[$v['id']] = $outputPre . $v['title'];

            // 检查当前节点是否有子节点
            if (isset($v['children'])) {
                // 增加前缀显示层级
                $children = $this->loadSelectLoop($v['children'], $pre . "&nbsp;&nbsp;&nbsp;&nbsp;");
                $new_list = $new_list + $children;
            }
        }
        return $new_list;
    }
}
