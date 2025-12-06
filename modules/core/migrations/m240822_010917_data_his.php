<?php

namespace app\modules\core\migrations;

use yii\db\Migration;



class m240822_010917_data_his extends Migration
{

    public function up()
    {
        $this->createTable('data_his', [
            'id'         => $this->bigPrimaryKey()->notNull(),
            'table_name' => $this->string()->comment('表名'),
            'table_id'   => $this->bigInteger()->comment('表ID'),
            'data'       => $this->text()->comment('数据'),
            'color'      => $this->string()->comment('颜色'),
            'user_id'    => $this->bigInteger()->comment('用户ID'),
            'created_at' => $this->integer()->comment('创建时间'),
        ]);
    }

    public function down()
    {
        $this->dropTable('data_his');

        return false;
    }
}
