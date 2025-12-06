<?php

namespace app\modules\core\migrations;

use yii\db\Migration;



class m240822_010917_notice extends Migration
{

    public function up()
    {
        $this->createTable('notice', [
            'id'      => $this->bigPrimaryKey()->notNull(),
            'title'   => $this->string()->comment('通知标题')->notNull()->defaultValue(''),
            'content' => $this->text()->comment('通知内容')->notNull(),
            'data' => $this->json()->comment('通知数据')->null(),

            'user_id' => $this->bigInteger()->comment('接收消息者')->notNull()->defaultValue(0),
            'shop_id' => $this->bigInteger()->comment('店铺ID')->notNull()->defaultValue(0),

            'status' => $this->smallInteger()->comment('状态')->notNull()->defaultValue(0),
            'type'       => $this->string(100)->comment('发送方式')->notNull(),   

            'send_at' => $this->integer()->comment('发送时间'),
            'created_at' => $this->integer()->comment('创建时间'),
            'updated_at' => $this->integer()->comment('更新时间'),
        ]);

        //消息发送记录
        $this->createTable('notice_send', [
            'id'         => $this->bigPrimaryKey()->notNull(),
            'notice_id'  => $this->bigInteger()->comment('通知ID')->notNull()->defaultValue(0),
            'type'       => $this->string(100)->comment('发送方式')->notNull(),
            'account'    => $this->string()->comment('接收账号')->notNull()->defaultValue(''),

            'created_at' => $this->integer()->comment('创建时间'),
            'updated_at' => $this->integer()->comment('更新时间'),
        ]);
    }

    public function down()
    {
        $this->dropTable('notice');

        return false;
    }
}
