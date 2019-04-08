<?php

namespace file\migrations;
use yii\db\Migration;
use yii\db\Schema;

class m150127_040544_add_attachments extends Migration
{
    public function up()
    {
        $this->createTable('{{%file}}', [
            'id' => $this->primaryKey(),
            'model' => $this->string(40)->notNull()->comment('模型'),
            'attribute' => $this->string(40)->notNull()->comment('属性'),
            'item_id' => $this->integer()->notNull()->comment('实体ID'),
            'type' => $this->string(40)->notNull()->comment('类型'),
            'name' => $this->string(200)->notNull()->defaultValue('')->comment('文件名'),
            'hash' => $this->string(64)->notNull()->comment('HASH')->unique(),
            'mime' => $this->string(40)->notNull()->comment('MIME'),
            'is_main' => $this->boolean()->notNull()->defaultValue(0)->comment('主图'),
            'created_at' => $this->dateTime()->notNull()->comment('创建时间'),
            'sort' => $this->integer()->notNull()->defaultValue(0)->comment('顺序'),
            'size'=>$this->integer()->notNull()->defaultValue(0)->comment('大小'),
        ]);
        $this->createIndex('model_attribute_item_id', 'file', ['model','attribute','item_id']);
    }

    public function down()
    {
        $this->dropTable('{{%file}}');
    }
}
