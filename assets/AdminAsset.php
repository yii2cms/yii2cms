<?php

namespace app\assets;

use yii\web\AssetBundle;

class AdminAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'lib/fontawesome-free-7.0.1-web/css/all.min.css',
        'lib/layui/css/layui.css', 
        'css/admin/admin.css',
        'css/admin/index.css',
        'css/admin/view.css',
    ];
    public $js = [
        'lib/layui/layui.js',  
        'js/common/math.js',
        'js/common/echarts.js',
        'js/common/jquery.cookie.js',
        'js/admin/admin.js',

    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapAsset', 
        'yii\jui\JuiAsset',
    ];
}
