<?php

/**
 * 授权请查看根目录下的LICENSE.md
 * @author ken <yiiphp@foxmail.com> 
 * @link   https://github.com/yii2cms/yii2cms 
 */

namespace app\modules\core\classes;

use yii\base\Component;
use Yii;

/**
 * 基于 Yii2 Redis Mutex 的锁服务
 * 复用您现有的 redis 配置
 */
class Lock extends Component
{
    /**
     * @var int 默认获取锁的超时时间（秒）
     */
    public $timeout = 10;

    /**
     * @var string 锁键前缀
     */
    public $keyPrefix = 'lock_';

    /**
     * 执行加锁操作
     * @param string $key 锁的键名
     * @param callable $callable 要执行的回调函数
     * @param int|null $timeout 获取锁的超时时间，null 使用默认值
     * @return mixed 回调函数的返回值
     */
    public function execute($key, callable $callable, $timeout = null)
    {
        $mutex = Yii::$app->mutex;
        $timeout = $timeout ?? $this->timeout;
        $fullKey = $this->keyPrefix . $key;

        Yii::info("尝试获取锁: {$fullKey} (超时: {$timeout}s)", 'lock');

        if ($mutex->acquire($fullKey, $timeout)) {
            Yii::info("成功获取锁: {$fullKey}", 'lock');

            try {
                $result = call_user_func($callable);
                Yii::info("锁内操作完成: {$fullKey}", 'lock');
                return $result;
            } catch (\Exception $e) {
                Yii::error("锁内操作异常: {$fullKey} - {$e->getMessage()}", 'lock');
                throw $e;
            } finally {
                $mutex->release($fullKey);
                Yii::info("释放锁: {$fullKey}", 'lock');
            }
        } else {
            Yii::warning("获取锁失败: {$fullKey} (超时: {$timeout}s)", 'lock');
            return null;
        }
    }

    /**
     * 阻塞方式执行（无限等待直到获取锁）
     * @param string $key 锁的键名
     * @param callable $callable 要执行的回调函数
     * @return mixed 回调函数的返回值
     */
    public function executeBlocking($key, callable $callable)
    {
        $mutex = Yii::$app->mutex;
        $fullKey = $this->keyPrefix . $key;

        Yii::info("阻塞方式获取锁: {$fullKey}", 'lock');

        // 无限等待直到获取锁
        while (!$mutex->acquire($fullKey, 1)) {
            Yii::info("等待锁: {$fullKey}", 'lock');
            sleep(1);
        }

        Yii::info("成功获取锁(阻塞): {$fullKey}", 'lock');

        try {
            $result = call_user_func($callable);
            return $result;
        } finally {
            $mutex->release($fullKey);
            Yii::info("释放锁(阻塞): {$fullKey}", 'lock');
        }
    }

    /**
     * 尝试获取锁（非阻塞，立即返回）
     * @param string $key 锁的键名
     * @return bool 是否成功获取锁
     */
    public function tryLock($key)
    {
        $mutex = Yii::$app->mutex;
        $fullKey = $this->keyPrefix . $key;

        // 超时时间为0，立即返回
        $acquired = $mutex->acquire($fullKey, 0);

        if ($acquired) {
            Yii::info("尝试获取锁成功: {$fullKey}", 'lock');
        } else {
            Yii::info("尝试获取锁失败: {$fullKey}", 'lock');
        }

        return $acquired;
    }

    /**
     * 手动释放锁
     * @param string $key 锁的键名
     */
    public function release($key)
    {
        $mutex = Yii::$app->mutex;
        $fullKey = $this->keyPrefix . $key;

        $mutex->release($fullKey);
        Yii::info("手动释放锁: {$fullKey}", 'lock');
    }
}
