<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%post}}`.
 */
class m240203_152425_create_post_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp(): void
    {
        $this->createTable('{{%post}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(256),
            'content' => 'LONGTEXT',
            'user_id' => $this->integer()->notNull(),
            'is_published' => $this->boolean()->defaultValue(false),
            'created_at' => $this->date(),
            'published_at' => $this->date()->null(),
        ]);

        $this->createIndex(
            'idx-post-user_id',
            'post',
            'user_id'
        );

        $this->addForeignKey(
            'fk-post-user_id',
            'post',
            'user_id',
            'user',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(): void
    {
        $this->dropForeignKey(
            'fk-post-user_id',
            'post'
        );

        $this->dropIndex(
            'idx-post-user_id',
            'post'
        );

        $this->dropTable('{{%post}}');
    }
}
