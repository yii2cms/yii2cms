<?php

/**
 * 授权请查看根目录下的LICENSE.md
 * @author ken <yiiphp@foxmail.com> 
 * @link   https://github.com/yii2cms/yii2cms 
 */
/**
 * 数组功能  
 */

namespace app\modules\core\classes;

class Arr extends \Nette\Utils\Arrays
{
    /**
     * 二维数组去重
     * @param array $array 二维数组
     * @param string $key 去重的键名
     * @return array
     */
    public static function unique($array, $key)
    {
        $in = [];
        foreach ($array as $k => $v) {
            $vv = $v[$key] ?? '';
            if ($vv && $in && in_array($vv, $in)) {
                unset($array[$k]);
                continue;
            }
            if ($vv) {
                $in[] = $vv;
            }
        }
        return $array;
    }
    /**
     * 数组转select,用于uniapp表单select 需要 value 和 label
     *
     * @param $all
     * @param $key
     * @param $value
     * @param $key_label 默认 label
     * @param $key_value 默认 value
     * @return array
     */
    public static function toSelect($all, $key = 'title', $value = 'id', $key_label = "label", $key_value = 'value')
    {
        $arr = [];
        if (!$all) {
            return [];
        }
        foreach ($all as $k => $v) {
            if (is_object($v)) {
                $arr[] =  [
                    $key_label => $v->$key,
                    $key_value => $v->$value
                ];
            } elseif (is_array($v)) {
                $arr[] =  [
                    $key_label => $v[$key],
                    $key_value => $v[$value]
                ];
            }
        }
        return $arr;
    }
    /**
     * 数组转xml
     * @param $arr
     */
    public static function toXml($arr, $root = '')
    {
        return \Spatie\ArrayToXml\ArrayToXml::convert($arr, $root);
    }
    /**
     * 根据数组的key排序
     * @param array $sortArray [ 'a'=>'','b'=>'' ]
     * @param array $sortValue ['b','a','d']
     * @return array
     */
    public static function sortKeyByArray($sortArray = [], $sortValue = [])
    {
        $arr = [];
        foreach ($sortValue as $k) {
            if (isset($sortArray[$k])) {
                $arr[$k] = $sortArray[$k];
            }
        }
        return $arr;
    }
    /**
     * 数组转树状结构
     * @param array $list 数组
     * @param string $pk 主键
     * @param string $pid 父级主键
     * @param string $child 子级键名
     * @param int $root 根节点
     * @param string $my_id 我的id
     * @return array
     */
    public static function toTree($list, $pk = 'id', $pid = 'pid', $child = 'children', $root = 0, $my_id = '')
    {
        $tree = array();
        if (is_array($list)) {
            $refer = array();
            foreach ($list as $key => $data) {
                $refer[$data[$pk]] = &$list[$key];
            }
            foreach ($list as $key => $data) {
                $parentId = $data[$pid];
                if ($root == $parentId) {
                    $tree[$data[$pk]] = &$list[$key];
                } else {
                    if (isset($refer[$parentId])) {
                        $parent = &$refer[$parentId];
                        if ($my_id && $my_id == $list[$key]['id']) {
                        } else {
                            $parent[$child][] = &$list[$key];
                        }
                    }
                }
            }
        }
        return $tree;
    }
    /**
     * 数组分页 分页
     * @param array $array
     * @param int $page_size
     * @return array
     */
    public static function page($array, $page_size = 5)
    {
        $count = count($array);
        $total_page = ceil($count / $page_size);
        for ($i = 1; $i <= $total_page; $i++) {
            $start = ($i - 1) * $page_size;
            $list[] = array_slice($array, $start, $page_size);
        }
        return [
            'data' => $list,
            'size' => $page_size,
            'total' => $count,
            'pages' => $total_page,
        ];
    }
    /**
     * 对二维数组进行group by操作
     * @param array $arr
     * @param string $groupby
     * @return array
     */
    public static function groupBy($arr, $groupby = "sid")
    {
        static $array = array();
        static $key = array();
        foreach ($arr as $k => $v) {
            $g = $v[$groupby];
            if (!in_array($g, $key)) {
                $key[$k] = $g;
            }
            $array[$g][] = $v;
        }
        return $array;
    }

    /**
     * 数组排序
     * Arr::orderBy($row,[
     *    'name'=>'desc'
     * ]);
     */
    public static function orderBy($data, $sortRules)
    {
        if (empty($data) || empty($sortRules)) {
            return $data;
        }

        $sortParams = [];

        foreach ($sortRules as $field => $direction) {
            $column = [];
            foreach ($data as $row) {
                $column[] = $row[$field] ?? null;
            }

            $sortParams[] = $column;
            $sortParams[] = $direction === 'desc' ? SORT_DESC : SORT_ASC;
        }

        $sortParams[] = &$data;
        call_user_func_array('array_multisort', $sortParams);

        return $data;
    }
}
