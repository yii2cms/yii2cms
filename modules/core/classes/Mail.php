<?php

/**
 * 授权请查看根目录下的LICENSE.md
 * @author ken <yiiphp@foxmail.com> 
 * @link   https://github.com/yii2cms/yii2cms 
 */

namespace app\modules\core\classes;

use yii\symfonymailer\Mailer;
use Yii;
use app\modules\core\models\MailTemplate;

/**
 * 邮件助手类
 */
class Mail
{
    /**
     * 初始化邮件模板
     * @param string $name 邮件模板名称
     * @param string $key 邮件模板键名
     * @param string $title 邮件标题
     * @param string $content 邮件内容
     * @return bool 是否初始化成功
     */
    public static function init($name = '登录', $key = 'login', $title = '登录验证码', $content = '')
    {
        // 检查模板是否已存在
        if (MailTemplate::findOne(['key' => $key])) {
            return false;
        }
        $model = new MailTemplate();
        $model->title = $title;
        $model->name  = $name;
        $model->key   = $key;
        $model->content = $content;
        $model->save();
    }
    /**
     * 发送模板内容
     * @param string $to 收件人邮箱
     * @param string $key 邮件模板键名
     * @param array $params 替换参数
     * @param array $attachments 附件
     * @return bool 是否发送成功
     */
    public static function send($to, $key, $params = [], $attachments = [])
    {

        $model = MailTemplate::findOne(['key' => $key]);
        if (!$model) {
            Log::add('邮件模板' . $key . '不存在', 'error');
            return false;
        }
        $subject = $model->title;
        $content = $model->content;
        if (!$content || !$subject) {
            Log::add('邮件模板内容或标题为空', 'error', 'mail');
            return false;
        }
        /**
         * 替换 content中的 {key} 对应  $params ['key']=>'value'
         */
        foreach ($params as $key => $value) {
            $content = str_replace('{' . $key . '}', $value, $content);
        }
        return self::sendRaw($to, $subject, $content, $attachments);
    }
    /**
     * 发送模板邮件
     * @param string $to 收件人邮箱
     * @param string $subject 邮件标题
     * @param string $content 邮件内容
     * @param array $attachments 附件
     * @return bool 是否发送成功
     */
    public static function sendRaw($to, $subject, $content, $attachments = [])
    {
        return self::processSending([
            'to' => $to,
            'subject' => $subject,
            'content' => $content,
            'attachments' => $attachments
        ]);
    }

    /**
     * 核心发送逻辑
     */
    private static function processSending($options)
    {
        // 验证收件人
        if (!self::validateEmail($options['to'])) {
            Log::add("无效邮箱: {$options['to']}", 'error');
            return false;
        }

        // 初始化邮件组件
        if (!self::initMailComponent()) {
            Log::add('邮件组件初始化失败', 'error');
            return false;
        }

        try {
            // 创建消息实例
            $message = Yii::$app->mailer->compose();

            // 设置邮件内容
            if (is_array($options['content'])) {
                $message->setHtmlBody($options['content']['html'] ?? '')
                    ->setTextBody($options['content']['text'] ?? '');
            } else {
                $message->setHtmlBody($options['content']);
            }

            // 添加附件
            self::handleAttachments($message, $options['attachments']);

            // 设置收发信息
            $message->setTo($options['to'])
                ->setSubject($options['subject'])
                ->setFrom(self::getFromAddress());

            // 发送邮件
            return Yii::$app->mailer->send($message);
        } catch (\NewException $e) {
            Log::add("邮件发送失败 - {$options['to']}: {$e->getMessage()}", 'error');
            return false;
        }
    }

    /**
     * 验证邮箱
     */
    private static function validateEmail($email)
    {
        return filter_var(trim($email), FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * 初始化邮件组件
     */
    private static function initMailComponent()
    {
        $config = [
            'host' => get_config('mail.host'),
            'user' => get_config('mail.username'),
            'pass' => get_config('mail.password'),
            'port' => get_config('mail.port', 587),
            'encryption' => get_config('mail.encryption', 'tls')
        ];

        if (empty($config['host']) || empty($config['user'])) {
            Log::add("SMTP配置缺失", 'error');
            return false;
        }

        // 构建DSN
        $dsn = sprintf(
            'smtp://%s:%s@%s:%d?encryption=%s',
            urlencode($config['user']),
            urlencode($config['pass']),
            $config['host'],
            $config['port'],
            $config['encryption']
        );

        // 配置邮件组件
        Yii::$app->set('mailer', [
            'class' => Mailer::class,
            'transport' => ['dsn' => $dsn],
            'viewPath' => '@app/mail'
        ]);

        return true;
    }

    /**
     * 处理附件
     */
    private static function handleAttachments($message, $files)
    {
        foreach ($files as $file) {
            try {
                if (isset($file['path'])) {
                    $message->attach($file['path'], [
                        'fileName' => $file['name'] ?? basename($file['path']),
                        'contentType' => $file['type'] ?? self::detectMimeType($file['path'])
                    ]);
                } elseif (isset($file['content'])) {
                    $message->attachContent($file['content'], [
                        'fileName' => $file['name'] ?? 'attachment.dat',
                        'contentType' => $file['type'] ?? 'application/octet-stream'
                    ]);
                }
            } catch (\NewException $e) {
                Yii::error("附件添加失败: {$e->getMessage()}", 'mail');
            }
        }
    }

    /**
     * 检测文件MIME类型
     */
    private static function detectMimeType($path)
    {
        return mime_content_type($path) ?: 'application/octet-stream';
    }
    /**
     * 设置发件人地址
     */
    public static function setFromAddress($email, $name = '')
    {
        set_config('mail.from_email', $email);
        set_config('mail.from_name', $name);
    }

    /**
     * 获取发件人地址
     */
    private static function getFromAddress()
    {
        $email = trim(get_config('mail.from_email', ''));
        $name = trim(get_config('mail.from_name', ''));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email = 'noreply@' . (Yii::$app->request->hostName ?? 'example.com');
        }

        return $name ? [$email => $name] : $email;
    }
}
