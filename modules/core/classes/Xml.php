<?php

/**
 * 授权请查看根目录下的LICENSE.md
 * @author ken <yiiphp@foxmail.com> 
 * @link   https://github.com/yii2cms/yii2cms 
 */

namespace app\modules\core\classes;

use Yii;

/**
 * XML助手类
 */
class Xml
{
    /**
     * 将XML转换为数组
     * @param string $xml_content XML内容
     * @return array 数组
     */
    public static function toArray($xml_content)
    {
        $doc = new \DOMDocument();
        $doc->loadXML($xml_content);
        $root = $doc->documentElement;
        $output = (array) self::node($root);
        $output['@root'] = $root->tagName ?? '';
        return $output ?? [];
    }

    /**
     * 递归处理节点 
     * 内部调用
     * @param \DOMNode $node 节点
     * @return array|string 数组或字符串
     */
    public static function node($node)
    {
        $output = [];
        switch ($node->nodeType) {
            case 4:
            case 3:
                $output = trim($node->textContent);
                break;
            case 1:
                for ($i = 0, $m = $node->childNodes->length; $i < $m; $i++) {
                    $child = $node->childNodes->item($i);
                    $v = self::node($child);
                    $t = $child->tagName ?? '';
                    if ($t) {
                        if (!isset($output[$t])) {
                            $output[$t] = [];
                        }
                        if (is_array($v) && empty($v)) {
                            $v = '';
                        }
                        $output[$t][] = $v;
                    } elseif ($v || $v === '0') {
                        $output = (string) $v;
                    }
                }
                if ($node->attributes->length && !is_array($output)) {
                    $output = ['@content' => $output];
                }
                if (is_array($output)) {
                    if ($node->attributes->length) {
                        $attr = [];
                        foreach ($node->attributes as $name => $node) {
                            $attr[$name] = (string) $node->value;
                        }
                        $output['@attributes'] = $attr;
                    }
                    foreach ($output as $t => $v) {
                        if ($t !== '@attributes' && is_array($v) && count($v) === 1) {
                            $output[$t] = $v[0] ?? '';
                        }
                    }
                }
                break;
        }
        return $output;
    }
}
