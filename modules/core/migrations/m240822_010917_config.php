<?php

namespace app\modules\core\migrations;

use app\modules\core\classes\Migration;



class m240822_010917_config extends Migration
{

    public function up()
    {
        $this->createTable('config', [
            'id'      => $this->bigPrimaryKey()->notNull(),
            'name'    => $this->string()->comment('配置名称'),
            'key'     => $this->string()->comment('配置键名')->defaultValue(''),
            'content' => $this->text()->comment('配置内容'),
            'type'    => $this->string()->comment('类型')->defaultValue('text'),
            'type_value' => $this->json()->comment('类型值'),
            'help'    => $this->longText()->comment('帮助'),
            'created_at' => $this->bigInteger()->comment('创建时间'),
        ]);
    }

    public function down()
    {
        $this->dropTable('config');

        return false;
    }
}
