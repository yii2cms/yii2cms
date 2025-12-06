<?php

namespace app\modules\core\migrations;

use yii\db\Migration;



class m240822_010917_cache_data extends Migration
{

    public function up()
    {
        $this->createTable('cache_data', [
            'id'      => $this->bigPrimaryKey()->notNull(),
            'key'     => $this->string()->comment('缓存键名')->notNull()->defaultValue(''),
            'group'   => $this->string()->comment('缓存分组')->defaultValue('default'),
            'content' => $this->binary()->comment('缓存内容'), 
        ]);
    }

    public function down()
    {
        $this->dropTable('cache_data');

        return false;
    }
}
