<?php

defined('YII_DEBUG') or define('YII_DEBUG', true);
/**
 * 环境变量 dev prod 上线时必须为prod
 */
defined('YII_ENV') or define('YII_ENV', 'dev');

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

$config = require __DIR__ . '/../config/web.php';

$app = (new yii\web\Application($config)); 
require __DIR__ . '/../config/bootstrap.php';
$app->run();
