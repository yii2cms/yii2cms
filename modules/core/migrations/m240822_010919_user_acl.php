<?php

namespace app\modules\core\migrations;

use yii\db\Migration;



class m240822_010919_user_acl extends Migration
{

    public function up()
    {
        $this->createTable('user_acl', [
            'id' => $this->bigPrimaryKey()->notNull(),
            'user_id' => $this->bigInteger()->notNull(),
            'url' => $this->json()->null(),
        ]);
    }

    public function down()
    {
        $this->dropTable('user_acl');

        return false;
    }
}
