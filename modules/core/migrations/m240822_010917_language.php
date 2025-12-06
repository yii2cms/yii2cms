<?php

namespace app\modules\core\migrations;

use yii\db\Migration;



class m240822_010917_language extends Migration
{

    public function up()
    {
        $this->createTable('language', [
            'id'      => $this->bigPrimaryKey()->notNull(),
            'table'   => $this->string()->comment('关联模型表名'),
            'code'    => $this->string()->comment('语言代码'),
            'nid'     => $this->bigInteger()->comment('关联模型ID'),
            'data'    => $this->json()->comment('语言数据'),
        ]);

        $this->createTable('language_code', [
            'id'      => $this->bigPrimaryKey()->notNull(),
            'name'    => $this->string()->comment('语言名称'),
            'code'    => $this->string()->comment('语言代码'),
            'badge'   => $this->string()->comment('语言标志'),
            'sort'   => $this->integer()->comment('排序'),
            'is_default' => $this->boolean()->comment('是否默认语言')->defaultValue(0),
        ]);
    }

    public function down()
    {
        $this->dropTable('language');
        $this->dropTable('language_code');

        return false;
    }
}
