<?php

namespace app\modules\core\migrations;

use yii\db\Migration;

class m250926_085813_post extends Migration
{
    public function up()
    {
        $this->createTable('post', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull()->comment('标题'),
            'content' => $this->text()->null()->comment('内容'),
            'type_id' => $this->integer()->null()->comment('类型'),
            'status' => $this->integer()->null()->comment('状态'),
            'image' => $this->string(255)->null()->comment('图片'),
            'images' => $this->json()->null()->comment('图片列表'),
            'sort' => $this->integer()->null()->defaultValue(0)->comment('排序'),

            'delete_at' => $this->integer()->null()->comment('删除时间'),
            'created_at' => $this->integer()->null()->comment('创建时间'),
            'updated_at' => $this->integer()->null()->comment('更新时间'),
        ]);

        /**
         * post_type
         */
        $this->createTable('post_type', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull()->comment('类型名称'),
            'sort' => $this->integer()->null()->comment('排序'),
            'delete_at' => $this->integer()->null()->comment('删除时间'),
            'created_at' => $this->integer()->null()->comment('创建时间'),
            'updated_at' => $this->integer()->null()->comment('更新时间'),
        ]);
    }

    public function down()
    {
        $this->dropTable('post');
        $this->dropTable('post_type');
        return false;
    }
}
