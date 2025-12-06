<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';
$modules = require __DIR__ . '/modules.php';
$migrations = [];
foreach ($modules as $k => $v) {
    $migrations[] = 'app\modules\\' . $k . '\migrations\\';
}
$config = [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'app\commands',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
        '@tests' => '@app/tests',
        '@webroot' => '@app/web',
    ],
    'modules' => $modules,
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => '127.0.0.1',
            //'password' => '',
            'port' => 6379,
            'database' => 0,
        ],

        'mutex' => [
            'class' => 'yii\redis\Mutex',
            'redis' => 'redis', // 直接使用上面配置的 redis 组件
            'keyPrefix' => 'mutex_', // 锁键前缀
        ],

        'lock' => [
            'class' => 'app\modules\core\classes\Lock',
        ],

        'order_num' => [
            'class' => 'app\modules\core\components\OrderNum',
            'datacenterId' => 1, // 数据中心 ID (0-31)
            'workerId' => 1,     // 机器 ID (0-31)
        ],
    ],
    'params' => $params,

    'controllerMap' => [
        'fixture' => [ // Fixture generation command line.
            'class' => 'yii\faker\FixtureController',
        ],
        'migrate' => [
            'class' => 'yii\console\controllers\MigrateController',
            'migrationPath' => null,
            'migrationNamespaces' => $migrations
        ],
    ],

];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
    // configuration adjustments for 'dev' environment
    // requires version `2.1.21` of yii2-debug module
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
