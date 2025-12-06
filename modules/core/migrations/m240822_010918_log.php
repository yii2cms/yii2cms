<?php

namespace app\modules\core\migrations;

use yii\db\Migration;



class m240822_010918_log extends Migration
{

    public function up()
    {
        $this->createTable('log', [
            'id' => $this->bigPrimaryKey()->notNull(),
            'content' => $this->text()->comment('日志'),
            'type' => $this->string()->comment('类型'),
            'user_id' => $this->bigInteger()->comment('操作员'),
            'ip' => $this->string()->comment('IP'),
            'agent' => $this->string()->comment('浏览器'),
            'created_at' => $this->integer()->comment('创建时间'),
        ]);
    }

    public function down()
    {
        $this->dropTable('log');

        return false;
    }
}
