<?php

namespace app\modules\core\migrations;

use yii\db\Migration;

class m250926_085811_user extends Migration
{
    public function up()
    {
        $this->createTable('user', [
            'id' => $this->primaryKey(),
            'username' => $this->string(255)->notNull(),
            'email' => $this->string(255)->null(),
            'phone' => $this->string(255)->null(),
            'password' => $this->string(255)->null(),
            'access_token' => $this->string(255)->null(),
            'auth_key' => $this->string(255)->null(),
            'role' => $this->string(255)->null(),
            'nickname' => $this->string(255)->null(),
            'avatar' => $this->string(255)->null(),
            'status' => $this->string(20)->defaultValue('active'),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->null(),
        ]);
    }

    public function down()
    {
        $this->dropTable('user');
        return false;
    }
}
