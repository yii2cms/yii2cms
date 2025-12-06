<?php

use app\modules\core\classes\Config;
use app\modules\core\classes\Mail;
use app\modules\core\classes\Sms;
use \app\modules\core\classes\AdminController;
use \app\modules\core\classes\Acl;
use app\modules\core\classes\Menu;
use yii\base\Event;

/**
 * 绑定全局 beforeAction 钩子
 */
Event::on(AdminController::class, AdminController::EVENT_BEFORE_ACTION, function ($event) {

    Menu::addMenu('文章管理', 'fas fa-file-alt', '#', ['core/post', 'core/post-type'], 20);
    Menu::addSubMenu('文章管理', '文章', '/core/post/index', 'core/post', 10);
    Menu::addSubMenu('文章管理', '文章类型', '/core/post-type/index', 'core/post-type', 9);

    Menu::addMenu('系统', 'fas fa-cog', '#', [], 1);
    Menu::addSubMenu('系统', '用户', '/core/user/index', 'core/user', 10);
    Menu::addSubMenu('系统', '邮件模板', '/core/mail-template/index', 'core/mail-template', 9);
    Menu::addSubMenu('系统', '短信模板', '/core/sms-template/index', 'core/sms-template', 8);


    Menu::addSubMenu('系统', '配置', '/core/config/index', 'core/config', 4);

    if (get_config('is_muit_language') == 1) {
        Menu::addSubMenu('系统', '语言', '/core/language-code/index', [
            'core/language-code',
            'core/language-t'
        ], 3);
    }

    Menu::addSubMenu('系统', '日志', '/core/log/index', 'core/log', -1);

    /**
     * 获取所有格式化的权限数据
     */
    Acl::get('core', '\app\modules\core\classes\AdminController');
});
/**
 * 页面：后台首页
 */
add_action("page.admin.index", function () {});

/**
 * 初始化
 */
if (is_dev()) {

    Config::init('site_name', 'Yii2 Admin', '站点名称');
    Config::init('site_logo', '/img/logo.png', '站点logo', 'image');
    Config::init('cross_domain', '*', '接口.跨域域名', 'text');

    Config::init('mail.host', 'smtp.qq.com', '邮箱.SMTP主机');
    Config::init('mail.port', 587, '邮箱.SMTP端口');
    Config::init('mail.username', '68103403@qq.com', '邮箱.SMTP用户名');
    Config::init('mail.password', '111111', '邮箱.SMTP密码');
    Config::init('mail.from_email', '68103403@qq.com', '邮箱.发件人邮箱');
    Config::init('mail.from_name', 'yiicms', '邮箱.发件人名称');
    Config::init('mail.encryption', 'tls', '邮箱.SMTP加密方式');


    Config::init('is_muit_language', -1, '是否多语言', 'dropDownList', [
        -1 => '否',
        1  => '是',
    ]);

    Config::init('sms.default.drive', 'Default', '短信默认驱动', 'dropDownList', [
        'Default' => '默认',
        'Aliyun'  => '阿里云',
        'Tencent' => '腾讯云',
    ]);


    Config::init('sms.default.user', '100373', '默认短信.用户');
    Config::init('sms.default.pwd', 'ym37LLrH', '默认短信.密码');
    Config::init('sms.default.ip', '121.196.204.71', '默认短信IP');
    Config::init('sms.default.sign', '顶策科技', '默认短信.签名');

    //阿里云
    Config::init('aliyun.sms.key_id', '', '阿里云.短信.KEY_ID');
    Config::init('aliyun.sms.key_secret', '', '阿里云.短信.KEY_SECRET');
    Config::init('aliyun.sms.endpoint', '', '阿里云.短信.ENDPOINT');
    Config::init('aliyun.sms.sign', '签名', '阿里云.短信.签名');

    //腾讯云短信
    Config::init('tencent.sms.secret_id', '', '腾讯云.短信.SecretId');
    Config::init('tencent.sms.secret_key', '', '腾讯云.短信.SecretKey');
    Config::init('tencent.sms.endpoint', 'ap-guangzhou', '腾讯云.短信.地域信息');
    Config::init('tencent.sms.sdk_app_id', '', '腾讯云.短信.应用ID');
    Config::init('tencent.sms.sign', '签名', '腾讯云.短信.签名');

    /**
     * 邮箱模板
     */
    Mail::init('登录验证码', 'login', '验证登录验证码', "正在验证您的登录验证码<br>您的验证码是：{code}<br> 如非本人操作请忽略");
    Mail::init('绑定邮箱验证码', 'bind_change', '绑定邮箱验证码', "用户正在绑定您的邮箱<br>您的验证码是：{code} <br> 如非本人操作请忽略");

    /**
     * 短信模板
     */
    Sms::init('登录', 'login',  "您的验证码是：{code},该验证码5分钟内有效，请勿泄露于他人!");
    Sms::init('绑定手机号', 'bind_change', "您的验证码是：{code},该验证码5分钟内有效，请勿泄露于他人!");

    /**
     * 小牛翻译
     */
    Config::init('niutrans_text_secret_key', '', '小牛翻译.API-KEY');
    Config::help('niutrans_text_secret_key', 'https://niutrans.com/cloud/api/list');
}
