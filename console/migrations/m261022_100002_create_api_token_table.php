<?php

declare(strict_types=1);

use yii\db\Migration;

/**
 * Tokens de acesso da API REST (aplicacao Android): o login cria, o logout apaga.
 */
class m261022_100002_create_api_token_table extends Migration
{
    public function safeUp(): void
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%api_token}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'token' => $this->string(64)->notNull()->unique(),
            'created_at' => $this->integer()->notNull(),
            'expires_at' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createIndex('idx_api_token_user', '{{%api_token}}', 'user_id');
        $this->addForeignKey('fk_api_token_user', '{{%api_token}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function safeDown(): void
    {
        $this->dropTable('{{%api_token}}');
    }
}
