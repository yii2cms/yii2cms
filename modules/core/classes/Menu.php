<?php

/**
 * 授权请查看根目录下的LICENSE.md
 * @author ken <yiiphp@foxmail.com> 
 * @link   https://github.com/yii2cms/yii2cms 
 */

namespace app\modules\core\classes;

use Yii;

/**
 * 菜单助手类
 */
class Menu
{
    protected static $active;
    public static $menu = [];
    public static $checkMenu = true;

    public static function setActive($name)
    {
        static::$active = $name;
    }

    public static function isActive($name)
    {
        if (is_array($name)) {
            foreach ($name as $item) {
                if (static::$active == $item || (is_array(static::$active) && in_array($item, static::$active))) {
                    return true;
                }
            }
            return false;
        }
        return static::$active == $name;
    }

    /**
     * 添加一级菜单
     * @param string $label 菜单标签
     * @param string $icon 图标类
     * @param string|array $url 菜单链接
     * @param string $activeKey 激活状态标识符
     * @param int $level 排序级别，数字越大越靠前
     */
    public static function addMenu($label, $icon, $url = '#', $activeKey = null, $level = 0)
    {
        $id = 'menu-' . md5($label);
        // 保留已存在的子菜单，避免后续模块覆盖导致丢失
        $existing = static::$menu[$id] ?? [];
        $existingItems = $existing['items'] ?? [];
        static::$menu[$id] = [
            'label' => $label,
            'icon' => $icon,
            'url' => $url,
            'data' => ['menu' => $id],
            'active' => static::isActive($activeKey),
            'items' => $existingItems,
            'level' => $level,
        ];
    }

    /**
     * 添加二级菜单
     * @param string $parentLabel 一级菜单标签
     * @param string $label 子菜单标签
     * @param string|array $url 子菜单链接
     * @param string $activeKey 激活状态标识符
     * @param string $icon 图标类（可选）
     */
    public static function addSubMenu($parentLabel, $label, $url, $activeKey = null, $level = 1)
    {
        $parentId = 'menu-' . md5($parentLabel);
        $id = 'submenu-' . md5($label);
        // 如果父级菜单不存在，先初始化占位，避免注册顺序导致子菜单无法添加
        if (!isset(static::$menu[$parentId])) {
            static::$menu[$parentId] = [
                'label' => $parentLabel,
                'icon' => '',
                'url' => '#',
                'data' => ['menu' => $parentId],
                'active' => false,
                'items' => [],
                'level' => 0,
            ];
        }
        static::$menu[$parentId]['items'][] = [
            'label' => $label,
            'level' => $level,
            'url' => $url,
            'data' => ['submenu' => $id],
            'active' => static::isActive($activeKey),
        ];
    }

    public static function get($role = 'admin')
    {
        $config[] = [
            'label' => Yii::t('app', '仪表板'),
            'icon' => 'fas fa-tachometer-alt',
            'url' => '/core/admin/index',
            'data' => ['menu' => 'dashboard'],
            'active' => static::isActive('core/admin'),
            'level' => 1000, // 仪表板优先级最高
        ];
        $config = self::getAll($config, $role);
        foreach ($config as $key => $v) {
            $url = $v['url'] ?? '';
            $items = $v['items'] ?? [];
            if (!$url || $url == '#') {
                if (!$items || !is_array($items) || count($items) == 0) {
                    unset($config[$key]);
                }
            }
            if ($items) {
                /**
                 * label去重
                 */
                $labels = [];
                foreach ($items as $k => $item) {
                    $label = $item['label'] ?? '';
                    if (in_array($label, $labels)) {
                        unset($items[$k]);
                    } else {
                        $labels[] = $label;
                    }
                }
                $config[$key]['items'] = $items;
            }
        }
        return $config;
    }

    public static function getAll($config, $role)
    {
        // 合并动态添加的菜单
        foreach (static::$menu as $menu) {
            $config[] = $menu;
        }
        /**
         * 检查权限
         */
        foreach ($config as $key => $item) {
            $items = $item['items'] ?? [];
            if ($items) {
                foreach ($items as $k => $item) {
                    if (static::$checkMenu && !check_acl($item['url'], $role)) {
                        unset($config[$key]['items'][$k]);
                    }
                }
                if (empty($config[$key]['items'])) {
                    unset($config[$key]);
                }
            }
        }
        if ($config) {
            $config = Arr::orderBy($config, ['level' => 'desc']);
            foreach ($config as $key => $item) {
                $items = $item['items'] ?? [];
                if ($items) {
                    $config[$key]['items'] = Arr::orderBy($items, ['level' => 'desc']);
                }
            }
        }
        return $config;
    }
}
