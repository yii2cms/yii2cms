<?php

namespace app\modules\core\migrations;

use yii\db\Migration;



class m240822_010917_mail extends Migration
{

    public function up()
    {
        $this->createTable('mail_template', [
            'id'      => $this->bigPrimaryKey()->notNull(),
            'title'   => $this->string()->comment('邮件模板标题')->defaultValue(''),
            'name'    => $this->string()->comment('邮件模板名称'),
            'key'     => $this->string()->comment('邮件模板键名')->defaultValue(''),
            'content' => $this->text()->comment('邮件模板内容'), 
            'created_at' => $this->bigInteger()->comment('创建时间'),
        ]);
    }

    public function down()
    {
        $this->dropTable('mail_template');  
        return false;
    }
}
