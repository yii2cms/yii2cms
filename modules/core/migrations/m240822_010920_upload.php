<?php

namespace app\modules\core\migrations;

use yii\db\Migration;



class m240822_010920_upload extends Migration
{

    public function up()
    {
        $this->createTable('upload', [
            'id' => $this->bigPrimaryKey()->notNull(),
            'name' => $this->string(255)->notNull(),
            'url' => $this->string(1000)->notNull(),
            'hash' => $this->string()->notNull(),
            'size' => $this->float()->notNull(),
            'type' => $this->string()->notNull(),
            'ext' => $this->string()->notNull(),
            'used' => $this->integer()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
        ]);
    }

    public function down()
    {
        $this->dropTable('upload');

        return false;
    }
}
