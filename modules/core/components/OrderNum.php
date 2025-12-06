<?php

namespace app\modules\core\components;

use Yii;
use yii\base\Component;
use yii\redis\Connection;
use yii\base\NewException;
use app\modules\core\classes\Log;

/**
 * 生成订单号（64位雪花ID优化版）
 * @example
 * <pre>
 * <code>
 * Yii::$app->order_num->create('JD') // 生成如 "JD1234567890123456789"
 * </code>
 * </pre>
 */
class OrderNum extends Component
{
    // 数据中心 ID (0-31)
    public $datacenterId = 1;
    // 机器 ID (0-31)
    public $workerId = 1;
    // 初始时间戳（2025-09-29 22:13:51.468）
    private $twepoch = 1759155231468;
    // 序列号占用位数（12位 → 0-4095）
    private $sequenceBits = 12;
    // 机器 ID 占用位数
    private $workerIdBits = 5;
    // 数据中心 ID 占用位数
    private $datacenterIdBits = 5;
    // 最大序列号
    private $maxSequence = -1 ^ (-1 << 12);
    // Redis 键名前缀
    private $redisKeyPrefix = 'snowflake64:seq:';
    // Redis 键过期时间（秒）
    private $redisKeyTTL = 2;
    // 最后使用的时间戳
    private $lastTimestamp = 0;

    /**
     * 初始化，验证配置
     * @throws Exception
     */
    public function init()
    {
        parent::init();
        $this->twepoch = Yii::$app->params['order_num_twepoch'] ?: $this->twepoch;
        if ($this->workerId < 0 || $this->workerId > (1 << $this->workerIdBits) - 1) {
            throw new Exception("workerId 必须在 0 到 " . ((1 << $this->workerIdBits) - 1) . " 之间");
        }
        if ($this->datacenterId < 0 || $this->datacenterId > (1 << $this->datacenterIdBits) - 1) {
            throw new Exception("datacenterId 必须在 0 到 " . ((1 << $this->datacenterIdBits) - 1) . " 之间");
        }
    }

    /**
     * 生成订单号
     * @param string $prefix 订单号前缀（如 'JD'）
     * @return string 唯一订单号
     * @throws Exception
     */
    public function create($prefix = '')
    {
        $redis     = $this->getRedisConnection();
        $timestamp = $this->getTimestamp();
        $sequence  = $this->getSequence($redis, $timestamp);
        // 64位雪花ID结构：
        // | 42位时间戳 | 5位数据中心 | 5位机器ID | 12位序列号 |
        $snowflakeId = bcadd(
            bcmul(bcsub($timestamp, $this->twepoch), 1 << ($this->workerIdBits + $this->datacenterIdBits + $this->sequenceBits)),
            bcadd(
                bcmul($this->datacenterId, 1 << ($this->workerIdBits + $this->sequenceBits)),
                bcadd(
                    bcmul($this->workerId, 1 << $this->sequenceBits),
                    $sequence
                )
            )
        );
        return $prefix . $snowflakeId;
    }

    /**
     * 获取 Redis 连接
     * @return Connection
     * @throws Exception
     */
    private function getRedisConnection()
    {
        $redis = Yii::$app->redis;
        try {
            $redis->ping();
        } catch (\NewException $e) {
            add_log('订单号生成器异常，Redis连接失败: ' . $e->getMessage(), 'error');
            throw new Exception("Redis连接不可用" . $e->getMessage());
        }
        return $redis;
    }

    /**
     * 获取当前时间戳（毫秒）
     * @return int
     * @throws Exception
     */
    private function getTimestamp()
    {
        $timestamp = (int)(microtime(true) * 1000);
        if ($timestamp < $this->twepoch) {
            throw new Exception("系统时间早于初始时间戳，无法生成订单号");
        }
        if ($timestamp < $this->lastTimestamp) {
            throw new Exception("系统时钟回退，无法生成订单号");
        }
        $this->lastTimestamp = $timestamp;
        return $timestamp;
    }

    /**
     * 获取序列号（Redis 原子操作）
     * @param Connection $redis
     * @param int $timestamp
     * @return int
     * @throws Exception
     */
    private function getSequence($redis, $timestamp)
    {
        $key = $this->redisKeyPrefix . $timestamp;

        try {
            $sequence = $redis->incr($key);

            // 首次设置时添加过期时间
            if ($sequence === 1) {
                $redis->expire($key, $this->redisKeyTTL);
            }

            if ($sequence > $this->maxSequence) {
                usleep(1000); // 等待1ms
                return $this->getSequence($redis, $this->getTimestamp());
            }

            return $sequence;
        } catch (\NewException $e) {
            Yii::error("Redis序列号获取失败: " . $e->getMessage(), __METHOD__);
            throw new Exception("系统繁忙，请重试");
        }
    }
}
