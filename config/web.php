<?php
$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';
$modules = require __DIR__ . '/modules.php';
$match = '[a-zA-Z0-9\-_]+';
$rules = [
    '<controller:' . $match . '>/<action:' . $match . '>' => '<controller>/<action>',
    '<module:' . $match . '>/<controller:' . $match . '>/<action:' . $match . '>' => '<module>/<controller>/<action>',
    '<language:' . $match . '>/<controller:' . $match . '>/<action:' . $match . '>' => '<controller>/<action>',
    '<language:' . $match . '>/<module:' . $match . '>/<controller:' . $match . '>/<action:' . $match . '>' => '<module>/<controller>/<action>',
];

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'language' => 'zh-CN',
    'modules' => $modules,
    'components' => [

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

        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],

        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '3HjPeFSGOHttB16WMtTDnNFGDQ7B2jBw',
        ],


        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],

        'admin' => [
            'class' => 'yii\web\User',
            'identityClass' => 'app\modules\core\models\User',
            'enableAutoLogin' => true,
            'loginUrl' => ['core/login/index'],
            'identityCookie' => ['name' => '_identity_admin', 'httpOnly' => true],
            'idParam' => '__id_admin',
            'returnUrlParam' => '__returnUrl_admin',
        ],

        'shop' => [
            'class' => 'yii\web\User',
            'identityClass' => 'app\modules\core\models\User',
            'enableAutoLogin' => true,
            'loginUrl' => ['core/login/index'],
            'identityCookie' => ['name' => '_identity_shop', 'httpOnly' => true],
            'idParam' => '__id_shop',
            'returnUrlParam' => '__returnUrl_shop',
        ],

        'errorHandler' => [
            'errorAction' => 'core/error/index',
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@app/mail',
            // send all mails to a file by default.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => $rules,
        ],

        'view' => [
            'class' => 'yii\web\View',
            'theme' => [
                'basePath' => '@app/themes/default',
                'baseUrl' => '@web/themes/default',
                'pathMap' => [
                    '@app/views' => '@app/themes/default',
                ],
            ],
        ],
        /**
         * $orderId = Yii::$app->order_num->create('ORDER_');
         */
        'order_num' => [
            'class' => 'app\modules\core\components\OrderNum',
            'datacenterId' => 1, // 数据中心 ID (0-31)
            'workerId' => 1,     // 机器 ID (0-31)
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['127.0.0.1', '::1'],
        /**
         * 使用 modules/core/gii 目录下的 Gii 模板生成器
         */
        'generators' => [ // 自定义生成器配置
            'crud' => [ // CRUD 生成器
                'class' => 'yii\gii\generators\crud\Generator',
                'templates' => [
                    'default' => '@app/modules/core/gii/crud/default', // 自定义 CRUD 模板路径
                ],
            ],
            'model' => [ // 模型生成器
                'class' => 'yii\gii\generators\model\Generator',
                'templates' => [
                    'default' => '@app/modules/core/gii/model/default', // 自定义模型模板路径
                ],
            ],
            'controller' => [ // 控制器生成器
                'class' => 'yii\gii\generators\controller\Generator',
                'templates' => [
                    'default' => '@app/modules/core/gii/controller/default', // 自定义控制器模板路径
                ],
            ],
            'module' => [ // 模块生成器
                'class' => 'yii\gii\generators\module\Generator',
                'templates' => [
                    'default' => '@app/modules/core/gii/module/default', // 自定义模块模板路径
                ],
            ],
            'form' => [ // 表单生成器
                'class' => 'yii\gii\generators\form\Generator',
                'templates' => [
                    'default' => '@app/modules/core/gii/form/default', // 自定义表单模板路径
                ],
            ],
        ],

    ];
}

return $config;
