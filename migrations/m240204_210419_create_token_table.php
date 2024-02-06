<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%token}}`.
 */
class m240204_210419_create_token_table extends Migration
{
    /**
     * @return void
     */
    public function up(): void
    {
        $this->createTable('token', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'token' => $this->string(255)->notNull()->unique(),
            'type' => $this->string(255)->notNull(),
            'expires_at' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex(
            'idx-token-user_id',
            'token',
            'user_id'
        );

        $this->addForeignKey(
            'fk-token-user_id',
            'token',
            'user_id',
            'user',
            'id',
            'CASCADE'
        );
    }

    /**
     * @return void
     */
    public function down(): void
    {
        $this->dropForeignKey(
            'fk-token-user_id',
            'token'
        );

        $this->dropIndex(
            'idx-token-user_id',
            'token'
        );

        $this->dropTable('token');
    }
}
