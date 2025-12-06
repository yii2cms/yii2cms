<?php

use app\modules\core\models\Notice;
use app\modules\core\models\NoticeSend;

/**
 * 添加消息
 */
function add_notice($opt = [])
{
    $title = $opt['title'] ?? '';
    $content = $opt['content'] ?? '';
    if (!$title || !$content) {
        return;
    }
    $notice = new Notice();
    $notice->setAttributes($opt);
    $notice->save();
    return $notice;
}
/**
 * 取需要发送的消息
 */
function get_notice($opt = [])
{
    $query = Notice::find()->where(['status' => 0]);
    $query->andWhere($opt);
    return $query->all();
}
/**
 * 发送消息
 */
function send_notice($notice_id, $send_type, $account)
{
    $notice = Notice::findOne($notice_id);
    if (!$notice) {
        return;
    }
    $notice->status = 1;
    $notice->save();
    /**
     * 发送
     */
    $notice_send = new NoticeSend();
    $notice_send->setAttributes([
        'notice_id' => $notice->id,
        'type'      => $send_type,
        'account'   => $account,
    ]);
    $notice_send->save();
    return $notice;
}
