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
class ShopMenu extends Menu
{
    public static $menu = [];
    /**
     * 检查菜单权限,商家不需要配置权限
     */
    public static $checkMenu = false;
    /**
     * 获取商店菜单
     */
    public static function get($role = 'shop')
    {
        $config[] = [
            'label' => Yii::t('app', '仪表板'),
            'icon' => 'fas fa-tachometer-alt',
            'url' => '/core/shop/index',
            'data' => ['menu' => 'dashboard'],
            'active' => static::isActive('core/shop'),
            'level' => 1000, // 仪表板优先级最高
            'className' => __CLASS__, // 添加类名标识
        ];
        $config = self::getAll($config, $role);
        return $config;
    }
}
