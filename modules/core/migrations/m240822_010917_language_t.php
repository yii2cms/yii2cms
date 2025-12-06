<?php

namespace app\modules\core\migrations;

use yii\db\Migration;



class m240822_010917_language_t extends Migration
{

    public function up()
    {
        $this->createTable('language_t', [
            'id'      => $this->bigPrimaryKey()->notNull(), 
            'code'    => $this->string(255)->comment('语言代码'), 
            'key'     => $this->string(255)->comment('翻译翻译的key'),
            'value'   => $this->string(255)->comment('翻译翻译的value'),
        ]); 
    }

    public function down()
    {
        $this->dropTable('language_t');  
        return false;
    }
}
