<?php

/**
 * 授权请查看根目录下的LICENSE.md
 * @author ken <yiiphp@foxmail.com> 
 * @link   https://github.com/yii2cms/yii2cms 
 */

namespace app\modules\core\classes;

use yii\helpers\Inflector;
use Yii;
use ReflectionClass;
use ReflectionMethod;

/**
 * Acl 助手类，用于解析继承指定基控制器的控制器中的 ACL 注解
 */
class Acl
{
    /**
     * 存储所有模块的权限数据
     * @var array
     */
    private static $allPermissions = [];

    /**
     * 解析指定模块中所有控制器的 ACL 注解
     * @param string $moduleId 模块 ID（例如 'core'）
     * @param string $baseController 基控制器类名（完全限定命名空间，例如 'app\components\AdminController'）
     * @return array 权限列表，格式为：['controller' => ['action' => ['permission1', 'permission2'], ...]]
     */
    public static function parsePermissions($moduleId, $baseController)
    {
        $permissions = [];
        $controllerPath = Yii::getAlias("@app/modules/{$moduleId}/controllers");

        if (!is_dir($controllerPath)) {
            return $permissions;
        }

        $files = glob($controllerPath . '/*Controller.php');

        foreach ($files as $file) {
            $controllerName = basename($file, '.php');
            $controllerClass = "app\\modules\\{$moduleId}\\controllers\\{$controllerName}";

            if (!class_exists($controllerClass)) {
                continue;
            }

            $reflection = new ReflectionClass($controllerClass);

            // 检查控制器是否继承指定的基控制器
            if (!$reflection->isSubclassOf($baseController)) {
                continue;
            }

            $controllerId = Inflector::camel2id(str_replace('Controller', '', $controllerName));
            $permissions[$controllerId] = [];

            // 获取所有公共方法（动作）
            $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);

            foreach ($methods as $method) {
                // 只处理动作方法（以 'action' 开头）
                if (strpos($method->name, 'action') !== 0) {
                    continue;
                }

                $actionId = Inflector::camel2id(str_replace('action', '', $method->name));
                $docComment = $method->getDocComment();

                // 提取 @acl 注解
                if ($docComment && preg_match('/@acl\s+(.+)/', $docComment, $matches)) {
                    $aclString = trim($matches[1]);
                    $aclPermissions = array_map('trim', explode(' ', $aclString));
                    $permissions[$controllerId][$actionId] = $aclPermissions;
                }
            }

            // 删除空的控制器条目
            if (empty($permissions[$controllerId])) {
                unset($permissions[$controllerId]);
            }
        }

        return $permissions;
    }

    /**
     * 生成控制器动作的 URL
     * @param string $moduleId 模块 ID
     * @param string $controllerId 控制器 ID
     * @param string $actionId 动作 ID
     * @return string 生成的 URL
     */
    public static function getActionUrl($moduleId, $controllerId, $actionId)
    {
        return "/{$moduleId}/{$controllerId}/{$actionId}";
    }

    /**
     * 获取格式化的权限数据，包含模块、控制器、动作、权限和 URL，并存储到静态变量
     * @param string $moduleId 模块 ID
     * @param string $baseController 基控制器类名
     * @return array 格式化的权限数据，格式为：[ ['title' => '', 'items' => ['item' => ['url1', 'url2']]] ]
     */
    public static function get($moduleId, $baseController)
    {
        $permissions = self::parsePermissions($moduleId, $baseController);
        $formatted = [];

        // 用于跟踪已处理的 title，防止重复
        $titleMap = [];

        foreach ($permissions as $controllerId => $actions) {
            foreach ($actions as $actionId => $perms) {
                $url = self::getActionUrl($moduleId, $controllerId, $actionId);

                foreach ($perms as $perm) {
                    // 按点分割权限，如 文章.查看 -> ['文章', '查看']
                    $parts = explode('.', $perm, 2);
                    if (count($parts) !== 2) {
                        continue; // 跳过格式不正确的权限
                    }
                    [$title, $item] = $parts;

                    // 查找或创建 title 对应的索引
                    if (!isset($titleMap[$title])) {
                        $titleMap[$title] = count($formatted);
                        $formatted[] = [
                            'title' => $title,
                            'items' => []
                        ];
                    }
                    $index = $titleMap[$title];

                    // 如果 item 已存在，追加 URL，否则创建新数组
                    if (!isset($formatted[$index]['items'][$item])) {
                        $formatted[$index]['items'][$item] = [];
                    }
                    $formatted[$index]['items'][$item][] = $url;
                }
            }
        }

        // 合并到静态变量
        foreach ($formatted as $entry) {
            $title = $entry['title'];
            if (!isset(self::$allPermissions[$title])) {
                self::$allPermissions[$title] = [
                    'title' => $title,
                    'items' => []
                ];
            }
            foreach ($entry['items'] as $item => $urls) {
                if (!isset(self::$allPermissions[$title]['items'][$item])) {
                    self::$allPermissions[$title]['items'][$item] = [];
                }
                self::$allPermissions[$title]['items'][$item] = array_unique(
                    array_merge(self::$allPermissions[$title]['items'][$item], $urls)
                );
            }
        }

        return $formatted;
    }

    /**
     * 获取所有累积的权限数据
     * @return array 所有权限数据，格式为：[ ['title' => '', 'items' => ['item' => ['url1', 'url2']]] ]
     */
    public static function getAll()
    {
        return array_values(self::$allPermissions);
    }
    /**
     * 检查动作是否在权限列表中
     * @param string $action 动作路径，如 '/core/default/index'
     * @param array $in 权限列表，如 ['/core/default/*', '/core/user-setting/*']
     * @return bool 是否有权限
     */
    public static function has($action, $in)
    {
        foreach ($in as $v) {
            //如果最后一个是*需要删除*
            if (substr($v, -1) == '*') {
                $v = substr($v, 0, -1);
            }
            if (strpos($action, $v) !== false) {
                return true;
            }
        }
        return false;
    }
}
