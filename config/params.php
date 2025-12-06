<?php

return [
    /**
     * 域名，支付回回调，在命令行下有用
     */
    'host' => 'https://.com',
    /**
     * api_create_token_key
     */
    'api_create_token_key' => '12$kX5vF1!mY8&rD4^hoE9',

    /**
     * 微信支付
     */
    'weixin_pay_mch_id' => '',
    'weixin_pay_mch_secret_key_v2' => '',
    'weixin_pay_mch_secret_key' => '',
    'weixin_pay_mch_secret_cert' => '', //apiclient_key.pem
    'weixin_pay_mch_public_cert' => '', //apiclient_cert.pem
    'weixin_pay_mp_app_id' => '', //公众号app_id
    'weixin_pay_mini_app_id' => '', //小程序app_id
    'weixin_pay_app_id' => '', //安卓IOS用到的app_id 
    'weixin_pay_root_key' => '', //根公钥
    'weixin_pay_root_public_cert' => '', //根公钥证书


    /**
     * 支付宝配置
     */
    'alipay_app_id' => '',
    'alipay_app_secret_cert' => '',
    'alipay_app_public_cert' => '',
    'alipay_public_secret_cert' => '',
    'alipay_root_secret_cert' => '',

    /**
     * 短信配置
     */
    'sms.default.drive' => 'Default',
    // 短信默认驱动
    'sms.default.user' => '',
    'sms.default.pwd'  => '',
    'sms.default.ip'   => '',
    'sms.default.sign' => '',
    //阿里云
    'aliyun.sms.key_id' => '',
    'aliyun.sms.key_secret' => '',
    'aliyun.sms.endpoint' => '',
    'aliyun.sms..sign'   => '签名',
    //腾讯云短信
    'tencent.sms.secret_id' => '',
    'tencent.sms.secret_key' => '',
    //ap-guangzhou
    'tencent.sms.endpoint' => '',
    //应用 ID
    'tencent.sms.sdk_app_id' => '',
    'tencent.sms.sign'   => '签名',

    /**
     * 邮件服务器
     */
    'mail.host'       => 'smtp.qq.com',
    'mail.port'       => 587,
    'mail.username'   => '',
    'mail.password'   => '',
    'mail.from_email' => '',
    'mail.from_name'  => '',
    'mail.encryption' => 'tls',

    /**
     * 多语言
     */
    'multi_language' => false,
    /**
     * cdn url
     */
    'cdn.urls' => [
        //'https://cdn.example.com',
    ],
    /**
     * 阿里云市场appcode
     */
    'aliyun.market_code' => '',

    /**
     * aes加密向量
     */
    'aes.iv' => 'Kj8nP9mQ2vX5cRa1',
    'aes.secret' => 'Yw3sH7nM9kL4xPr2',
    /**
     * 订单号生成开始时间
     * 毫秒 https://tool.lu/timestamp/
     */
    'order_num_twepoch' => '1759197952618', //毫秒
    /**
     * 是否多语言
     */
    'is_muit_language' => false,
    /**
     * 小牛翻译
     */
    'niutrans_text_secret_key' => '',
];
