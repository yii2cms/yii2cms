<?php

namespace app\modules\core\migrations;

use yii\db\Migration;



class m240822_010917_sms extends Migration
{

    public function up()
    {
        $this->createTable('sms_template', [
            'id'      => $this->bigPrimaryKey()->notNull(),
            'name'    => $this->string()->comment('短信模板名称'),
            'key'     => $this->string()->comment('短信模板键名')->defaultValue(''),
            'content' => $this->text()->comment('短信模板内容'),
            'type'    => $this->string()->comment('短信模板类型')->defaultValue('default'),   
            'created_at' => $this->integer()->comment('创建时间'),
        ]);
    }

    public function down()
    {
        $this->dropTable('sms_template');  
        return false;
    }
}
