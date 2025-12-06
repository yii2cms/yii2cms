<?php

namespace app\modules\core\migrations;

use yii\db\Migration;

class m250926_085811_user_login_log extends Migration
{
    public function up()
    {
        $this->createTable('user_login_log', [
            'id'         => $this->primaryKey(),
            'user_id'    => $this->integer()->notNull(),
            'login_type' => $this->string(255)->notNull(), 
            'ip'         => $this->string(255)->null(),
            'agent'      => $this->string(255)->null(),
            'token'      => $this->string(255)->null(),
            'status'     => $this->string(20)->null()->defaultValue('login'),
            'created_at' => $this->integer()->notNull(), 
            'updated_at' => $this->integer()->null(),
        ]);
    }

    public function down()
    {
        $this->dropTable('user_login_log');
        return false;
    }
}
