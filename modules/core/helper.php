<?php

use app\modules\core\classes\Cookie;
use app\modules\core\classes\Language;
use app\modules\core\classes\Env;
use app\modules\core\classes\Acl;
use app\modules\core\classes\User as UserClass;
use app\modules\core\models\Post;
use app\modules\core\models\PostType;
use app\modules\core\models\DataHis;
use app\modules\core\models\User;
use app\modules\core\models\CacheData;

class NewException extends \yii\web\NotFoundHttpException {}
/**
 * 执行加锁操作
 * @param string $key 锁键名
 * @param callable $call 要执行的回调函数
 * @param int $time 锁的超时时间（秒）
 */
function lock($key, $call, $time = 10)
{
    return \Yii::$app->lock->execute($key, $call, $time);
}
/**
 * 数据库缓存
 * @param string $key 缓存键名
 * @param mixed $value 缓存值
 * @param string $group 缓存分组
 * @return mixed
 */
function db_cache($key, $value = null, $group = 'default')
{
    if ($value === null) {
        return get_db_cache($key, $group);
    }
    set_db_cache($key, $value, $group);
}
/**
 * 缓存数据
 * @param string $key 缓存键名
 * @param mixed $value 缓存值
 * @return mixed
 */
function set_db_cache($key, $value, $group = 'default')
{
    $value = is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value;
    $model = CacheData::findOne(['key' => $key, 'group' => $group]);
    if ($model) {
        $model->content = $value;
        $model->save();
    } else {
        $model = new CacheData();
        $model->key = $key;
        $model->group = $group;
        $model->content = $value;
        $model->save();
    }
    $err = $model->getErr();
    if ($err) {
        pr($err);
        exit;
    }
}

/**
 * 获取缓存数据
 * @param string $key 缓存键名
 * @param string $group 缓存分组
 * @return mixed
 */
function get_db_cache($key, $group = 'default')
{
    $model = CacheData::findOne(['key' => $key, 'group' => $group]);
    $content = $model->content ?? '';
    $data = json_decode($content, true) ?? [];
    if ($data) {
        return $data;
    } else {
        return $content;
    }
}

/**
 * 获取用户信息
 * @param int $id 用户ID
 * @return User
 */
function get_user($id)
{
    if (is_array($id)) {
        return get_instance('user:' . md5(json_encode($id)), function () use ($id) {
            return User::find()->where($id)->one();
        });
    }
    return get_instance('user:' . $id, function () use ($id) {
        return User::findOne($id);
    });
}
/**
 * 获取用户基础信息
 */
function get_user_info($id)
{
    $user = get_user($id);
    if (!$user) {
        return [];
    }
    $info = $user->toMinApi() ?? [];
    return $info;
}

/**
 * 缓存实例，单次请求多次调用返回数据相同
 * @param mixed $key 缓存键值
 * @param callable $call 回调函数
 * @return mixed
 */
function get_instance($key, $call)
{
    static $_instance = [];
    if (!isset($_instance[$key])) {
        $_instance[$key] = call_user_func($call);
    }
    return $_instance[$key];
}
/**
 * 添加操作日志
 * @param string $table_name 表名
 * @param int $table_id 表ID
 * @param string $data 操作数据
 * @param string $color 颜色
 * @param int $uid 用户ID
 */
function add_his($table_name, $table_id, $data, $color = null)
{
    global $uid;
    $model = new DataHis();
    $model->table_name = $table_name;
    $model->table_id = $table_id;
    $model->data = $data;
    $model->user_id = $uid ?: 0;
    $model->created_at = time();
    $model->color = $color;
    $model->save();
}
/**
 * 添加文章
 * @example 
 * <pre>
 * <code>
 * addPost([
 *     'type' => '默认',
 *     'name' => '默认标题',
 *     'content' => '默认内容',
 * ]);
 * </code>
 * </pre>
 * @param array $data 文章数据
 * @return bool
 */
function addPost($data = [])
{
    $type = $data['type'] ?? '默认';
    $postType = PostType::findOne(['name' => $type]);
    if (!$postType) {
        $postType = new PostType();
        $postType->name = $type;
        $postType->save();
    }
    $name =  $data['name'] ?? '默认标题';
    if (Post::findOne(['name' => $name])) {
        return false;
    }
    $post = new Post();
    $post->type_id = $postType->id;
    $post->name    = $name;
    $post->status  = 1;
    $post->content = $data['content'] ?? '默认内容';
    $post->save();
}

/**
 * 是否ajax请求
 */
function is_ajax()
{
    return Env::isAjax();
}
/**
 * 获取当前角色
 */
function get_current_role()
{
    return  UserClass::$currentRole;
}
/**
 * 检查权限
 * @param $name 用户名
 * @param $action 操作路径
 * @return bool
 */
function check_acl($action = '', $role = 'admin')
{
    $id  = Yii::$app->$role->identity->id ?? 0;
    if ($id == 0) {
        return false;
    }
    if ($id == 1) {
        return true;
    }
    $acl = UserClass::getAcl($id, $role);
    $db_role = UserClass::$currentRole;
    if ($db_role != $role) {
        return false;
    }
    $allow = [
        '/core/user-setting/*',
    ];
    if (Acl::has($action, $allow)) {
        return true;
    }
    if ($acl && Acl::has($action, $acl)) {
        return true;
    }
    return false;
}
/**
 * 是否本地环境
 */
function is_local()
{
    return Env::isLocal();
}
/**
 * 日志记录
 * @param $msg 日志消息
 * @param $type 日志类型
 */
function add_log($msg, $type = 'info')
{
    return app\modules\core\classes\Log::add($msg, $type);
}
/**
 * 获取CDN地址
 */
function cdn_url($url)
{
    if (!$url) {
        return '';
    }
    if (is_array($url)) {
        $url = array_map(function ($item) {
            return app\modules\core\classes\Cdn::getUrl($item);
        }, $url);
        return $url;
    }
    return app\modules\core\classes\Cdn::getUrl($url);
}
/**
 * 数组循环取值
 */
function array_get($array, $key, $default = null)
{
    $value = $array[$key] ?? $default;
    /**
     * 如果是string直接trim,如果是数组递归调用并trim
     */
    if (is_string($value)) {
        $value = trim($value);
    }
    return $value;
}
/**
 * 获取当前主机地址
 * @return string
 */
function host()
{
    return Env::getHost();
}
/**
 * 翻译，用于菜单动态菜单
 */
function lang($text)
{
    if (Yii::$app->language == 'zh-CN') {
        Language::sync($text, $text);
    }

    return Yii::t('app', $text);
}
/**
 * 缓存
 * @param $key 缓存键名
 * @param $value 缓存值
 * @param $exp 过期时间
 */
function cache($key, $value = null, $exp = null)
{
    if ($value === null) {
        $value = Yii::$app->cache->get($key);
        if ($value) {
            $arr = json_decode($value, true);
            if ($arr) {
                return $arr;
            }
            return $value;
        }
    }
    if (is_array($value)) {
        $value = json_encode($value, JSON_UNESCAPED_UNICODE);
    }
    Yii::$app->cache->set($key, $value, $exp);
}
/**
 * 设置或获取cookie
 * @param $name cookie名称
 * @param $value cookie值
 * @param $exp 过期时间
 */
function cookie($name, $value = null, $exp = 0)
{
    if ($value === null) {
        return Cookie::get($name);
    }
    Cookie::set($name, $value, $exp);
}
/**
 * 获取配置项
 * @param $key 配置项键名
 * @param $default 默认值
 * @return mixed
 */
function get_config($key, $default = null)
{
    $db_data = app\modules\core\classes\Config::get($key);
    if ($db_data) {
        return $db_data;
    }
    $params = Yii::$app->params;
    return $params[$key] ?? $default;
}
/**
 * 设置配置项
 * @param $key 配置项键名
 * @param $value 配置项值
 */
function set_config($key, $value)
{
    app\modules\core\classes\Config::set($key, $value);
    Yii::$app->params[$key] = $value;
}

/**
 * 是否命令行模式
 * @return bool
 */
function is_cli()
{
    return PHP_SAPI === 'cli';
}
/**
 * 翻译
 * @param $msg 消息
 * @return string
 */
function t($msg)
{
    return Yii::t('app', $msg);
}
/**
 * 打印变量
 * @param $data 要打印的变量
 */
function pr($data)
{
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}
/**
 * 添加动作 
 */
function add_action($name, $call, $level = 20)
{
    global $_app;
    if (strpos($name, '|') !== false) {
        $arr = explode('|', $name);
        foreach ($arr as $v) {
            add_action($v, $call, $level);
        }
        return;
    }
    $_app['actions'][$name][] = ['func' => $call, 'level' => $level];
}
/**
 * 执行动作 
 */
function do_action($name, &$par = null)
{
    global $_app;
    if (!is_array($_app)) {
        return;
    }
    $calls  = $_app['actions'][$name] ?? [];
    $calls  = array_order_by($calls, 'level', SORT_DESC);
    if ($calls) {
        foreach ($calls as $v) {
            $func = $v['func'];
            $func($par);
        }
    }
}
/**
 * 数组排序
 * array_order_by($row,$order,SORT_DESC);
 */
function array_order_by()
{
    $args = func_get_args();
    $data = array_shift($args);
    foreach ($args as $n => $field) {
        if (is_string($field)) {
            $tmp = array();
            if (!$data) {
                return;
            }
            foreach ($data as $key => $row) {
                $tmp[$key] = $row[$field];
            }
            $args[$n] = $tmp;
        }
    }
    $args[] = &$data;
    if ($args) {
        call_user_func_array('array_multisort', $args);
        return array_pop($args);
    }
    return;
}
/**
 * 跳转
 *
 * @param string $url
 * @return void
 */
function jump($url)
{
    if (strpos($url, '://') === false && substr($url, 0, 1) != '/') {
        $url = '/' . $url;
    }
    header("Location: " . $url);
    exit;
}
/**
 * 是否开发环境
 * @return bool
 */
function is_dev()
{
    return YII_ENV === 'dev';
}
/**
 * 生成url
 * @param $url 路由
 * @param $par 参数
 * @return string
 */
function url($url, $par = [])
{
    return app\modules\core\classes\Url::create($url, $par);
}
/**
 * 检查字符串是否为 HTML
 * @param string $str 要检查的字符串
 * @return bool 如果是 HTML 返回 true，否则返回 false
 */
function is_html(string $str): bool
{
    if (!$str) {
        return false;
    }
    $trimmed = trim($str);
    if (preg_match('/^<[^>]+>$/', $trimmed)) {
        return true;
    }
    return false;
}
/**
 * 时间 ago 格式
 * @param $time 时间
 * @return string
 */
function timeago($time)
{
    return app\modules\core\classes\Time::ago($time);
}

/**
 * yaml转数组
 *
 * @param string $str
 * @version 1.0.0
 * @author sun <sunkangchina@163.com>
 * @return array
 */
function yaml_load($str)
{
    return Symfony\Component\Yaml\Yaml::parse($str);
}
/**
 * 数组转yaml
 *
 * @param array $array
 * @param integer $line
 * @version 1.0.0
 * @author sun <sunkangchina@163.com>
 * @return string
 */
function yaml_dump($array, $line = 3)
{
    return Symfony\Component\Yaml\Yaml::dump($array, $line);
}
/**
 * yaml转数组，数组转yaml格式
 *
 * @param string $str
 * @version 1.0.0
 * @author sun <sunkangchina@163.com>
 * @return string|array
 */
function yaml($str)
{
    if (is_string($str)) {
        return yaml_load($str);
    } else {
        return yaml_dump($str);
    }
}

/**
 * 当前时间
 */
function now()
{
    return date('Y-m-d H:i:s', time());
}


/**
 * 计算两点地理坐标之间的距离
 * @param  Decimal $longitude1 起点经度
 * @param  Decimal $latitude1  起点纬度
 * @param  Decimal $longitude2 终点经度
 * @param  Decimal $latitude2  终点纬度
 * @param  Int     $unit       单位 1:米 2:公里
 * @param  Int     $decimal    精度 保留小数位数
 * @return Decimal
 */
function get_distance($longitude1, $latitude1, $longitude2, $latitude2, $unit = 2, $decimal = 2)
{

    $EARTH_RADIUS = 6370.996; // 地球半径系数
    $PI = 3.1415926;
    $radLat1 = $latitude1 * $PI / 180.0;
    $radLat2 = $latitude2 * $PI / 180.0;
    $radLng1 = $longitude1 * $PI / 180.0;
    $radLng2 = $longitude2 * $PI / 180.0;
    $a = $radLat1 - $radLat2;
    $b = $radLng1 - $radLng2;
    $distance = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2)));
    $distance = $distance * $EARTH_RADIUS * 1000;
    if ($unit == 2) {
        $distance = $distance / 1000;
    }
    return round($distance, $decimal);
}
/**
 * 更新用户角色
 * @param int $user_id 用户ID
 * @param string $role 角色
 * @return bool
 */
function update_user_role($user_id, $role = 'shop')
{
    $user = User::findOne($user_id);
    if (!$user) {
        return false;
    }
    $oldRole = $user->role;
    if ($oldRole == 'admin') {
        return false;
    }
    User::updateAll(['role' => $role], ['id' => $user_id]);
    return true;
}


/**
 * 取较小值
 */
function bcmin($a, $b)
{
    return bccomp($a, $b) < 0 ? $a : $b;
}

/**
 * json错误输出
 */
function json_error($msg)
{
    echo json_encode(['code' => 250, 'msg' => $msg], JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * json成功输出
 */
function json_success($msg = '操作成功', $data = [])
{
    echo json_encode(['code' => 0, 'msg' => $msg, 'data' => $data], JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * db事务
 */
function db_transaction($callback)
{
    $res = false;
    $transaction = Yii::$app->db->beginTransaction();
    try {
        $res = $callback();
        $transaction->commit();
    } catch (\Exception $e) {
        $transaction->rollBack();
        $controller = Yii::$app->controller->id;
        if (strpos($controller, 'api-') !== false) {
            json_error($e->getMessage());
        } else {
            throw $e;
        }
    }
    return $res;
}


include __DIR__ . '/notice.php';
